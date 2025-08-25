<?php
session_start();
require_once('config/condb.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบ method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// รับข้อมูล
$product_id = $_POST['product_id'] ?? '';
$price_type = $_POST['price_type'] ?? 'hot';
$qty = $_POST['qty'] ?? 1;
$note = $_POST['note'] ?? '';
$member_id = $_SESSION['staff_id'];

// ตรวจสอบข้อมูล
if (empty($product_id) || empty($qty)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    // ดึงข้อมูลสินค้า
    $sql_product = "SELECT * FROM tbl_product WHERE id = :product_id";
    $stmt_product = $condb->prepare($sql_product);
    $stmt_product->execute(['product_id' => $product_id]);
    $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบสินค้า']);
        exit;
    }

    // กำหนดราคาตามประเภท
    switch ($price_type) {
        case 'hot':
            $price = $product['price_hot'];
            break;
        case 'cold':
            $price = $product['price_cold'];
            break;
        case 'frappe':
            $price = $product['price_frappe'];
            break;
        default:
            $price = $product['price_hot'];
    }

    // ตรวจสอบว่าสินค้าอยู่ในตะกร้าแล้วหรือไม่
    $sql_check = "SELECT * FROM tbl_cart WHERE member_id = :member_id AND product_id = :product_id AND price_type = :price_type AND is_active = 1";
    $stmt_check = $condb->prepare($sql_check);
    $stmt_check->execute([
        'member_id' => $member_id,
        'product_id' => $product_id,
        'price_type' => $price_type
    ]);
    $existing_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existing_item) {
        // อัปเดตจำนวน
        $new_qty = $existing_item['qty'] + $qty;
        $sql_update = "UPDATE tbl_cart SET qty = :qty, updated_at = NOW() WHERE cart_id = :cart_id";
        $stmt_update = $condb->prepare($sql_update);
        $result = $stmt_update->execute([
            'qty' => $new_qty,
            'cart_id' => $existing_item['cart_id']
        ]);
    } else {
        // เพิ่มสินค้าใหม่
        $sql_insert = "INSERT INTO tbl_cart (member_id, product_id, type, price_type, note, price, qty, created_at) 
                       VALUES (:member_id, :product_id, :type, :price_type, :note, :price, :qty, NOW())";
        $stmt_insert = $condb->prepare($sql_insert);
        $result = $stmt_insert->execute([
            'member_id' => $member_id,
            'product_id' => $product_id,
            'type' => $price_type,
            'price_type' => $price_type,
            'note' => $note,
            'price' => $price,
            'qty' => $qty
        ]);
    }

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'เพิ่มลงตะกร้าเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
