<?php
session_start();
require_once('config/condb.php');

if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

$member_id = $_SESSION['staff_id'];

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

try {
    // เริ่ม transaction
    $condb->beginTransaction();

    // ดึงรายการในตะกร้าของผู้ใช้
    $sql = "SELECT c.*, p.product_name, p.product_image, p.product_price, p.price_hot, p.price_cold, p.price_frappe
            FROM tbl_cart AS c
            JOIN tbl_product AS p ON c.product_id = p.id
            WHERE c.member_id = :member_id";
    $stmt = $condb->prepare($sql);
    $stmt->execute(['member_id' => $member_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        throw new Exception('ไม่มีสินค้าในตะกร้า');
    }

    // สร้าง order_id (ใช้ timestamp + member_id)
    $order_id = time() . '_' . $member_id;

    // กำหนดเวลารับสินค้า (30 นาทีจากปัจจุบัน)
    $pickup_time = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // สร้าง customer_note จากข้อความเพิ่มเติมทั้งหมด
    $customer_notes = [];
    foreach ($cart_items as $item) {
        if (!empty($item['note'])) {
            $customer_notes[] = $item['product_name'] . ': ' . $item['note'];
        }
    }
    $customer_note = !empty($customer_notes) ? implode(' & ', $customer_notes) : '';

    // คำนวณยอดรวม
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $price_type = $item['price_type'] ?? 'hot';
        switch ($price_type) {
            case 'hot':
                $price = $item['price_hot'];
                break;
            case 'cold':
                $price = $item['price_cold'];
                break;
            case 'frappe':
                $price = $item['price_frappe'];
                break;
            default:
                $price = $item['price_hot'];
        }
        $total_amount += $price * $item['qty'];
    }

   

    // ตรวจสอบและอัปโหลดรูป
$slip_image = '';
if (isset($_FILES['slip_image']) && $_FILES['slip_image']['error'] == 0) {
    $upload_dir = "assets/slips/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // สร้างโฟลเดอร์ถ้าไม่มี
    }

    $file_ext = pathinfo($_FILES['slip_image']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('slip_', true) . '.' . $file_ext; // ตั้งชื่อไฟล์ใหม่
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['slip_image']['tmp_name'], $target_file)) {
        $slip_image = $file_name; // เก็บชื่อไฟล์เพื่อบันทึก DB
    } else {
        die("อัปโหลดสลิปไม่สำเร็จ");
    }
}

    // บันทึกข้อมูลลงตาราง tbl_order
    $sql_order = "INSERT INTO tbl_order 
    (order_id, member_id, pickup_time, customer_note, status, order_date, slip_image, total_amount) 
    VALUES (:order_id, :member_id, :pickup_time, :customer_note, :status, NOW(), :slip_image, :total_amount)";

    $stmt_order = $condb->prepare($sql_order);
    $result_order = $stmt_order->execute([
        'order_id' => $order_id,
        'member_id' => $member_id,
        'pickup_time' => $pickup_time,
        'customer_note' => $customer_note,
        'status' => 'pending',
        'slip_image' => $slip_image, // ต้องมีตัวแปรนี้ด้วย
        'total_amount' => $total_amount
    ]);


    if (!$result_order) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลการสั่งซื้อได้');
    }

    // บันทึกรายการสินค้าลงตาราง tbl_order_item
    foreach ($cart_items as $item) {
        $price_type = $item['price_type'] ?? 'hot';
        switch ($price_type) {
            case 'hot':
                $price = $item['price_hot'];
                break;
            case 'cold':
                $price = $item['price_cold'];
                break;
            case 'frappe':
                $price = $item['price_frappe'];
                break;
            default:
                $price = $item['price_hot'];
        }

        $sql_item = "INSERT INTO tbl_order_item (order_id, product_id, product_name, price_type, unit_price, qty, total_price, note) 
                     VALUES (:order_id, :product_id, :product_name, :price_type, :unit_price, :qty, :total_price, :note)";
        $stmt_item = $condb->prepare($sql_item);
        $result_item = $stmt_item->execute([
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'product_name' => $item['product_name'],
            'price_type' => $price_type,
            'unit_price' => $price,
            'qty' => $item['qty'],
            'total_price' => $price * $item['qty'],
            'note' => $item['note'] ?? ''
        ]);

        if (!$result_item) {
            throw new Exception('ไม่สามารถบันทึกรายการสินค้าได้');
        }
    }

    // ลบรายการในตะกร้าหลังจากสั่งซื้อสำเร็จ
    $sql_delete_cart = "DELETE FROM tbl_cart WHERE member_id = :member_id";
    $stmt_delete_cart = $condb->prepare($sql_delete_cart);
    $result_delete_cart = $stmt_delete_cart->execute(['member_id' => $member_id]);

    if (!$result_delete_cart) {
        throw new Exception('ไม่สามารถลบรายการในตะกร้าได้');
    }

    // ยืนยัน transaction
    $condb->commit();

    // ส่งไปยังหน้าสำเร็จ
    header('Location: order_success.php?order_id=' . $order_id);
    exit;
} catch (Exception $e) {
    // ยกเลิก transaction หากเกิดข้อผิดพลาด
    $condb->rollBack();

    // ส่งกลับไปยังหน้า checkout พร้อมข้อความแจ้งเตือน
    header('Location: checkout.php?error=' . urlencode($e->getMessage()));
    exit;
}
