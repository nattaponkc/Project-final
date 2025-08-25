<?php
session_start();
require_once('config/condb.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบข้อมูลที่ส่งมา
if (!isset($_POST['cart_id']) || !isset($_POST['qty'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$cart_id = $_POST['cart_id'];
$qty = $_POST['qty'];
$member_id = $_SESSION['staff_id'];

// ตรวจสอบขอบเขตจำนวน
if ($qty < 1 || $qty > 99) {
    echo json_encode(['success' => false, 'message' => 'จำนวนสินค้าไม่ถูกต้อง']);
    exit;
}

try {
    // อัปเดตจำนวนสินค้าในตะกร้า (เฉพาะของผู้ใช้นี้)
    $sql = "UPDATE tbl_cart SET qty = :qty WHERE cart_id = :cart_id AND member_id = :member_id";
    $stmt = $condb->prepare($sql);
    $result = $stmt->execute([
        'qty' => $qty,
        'cart_id' => $cart_id,
        'member_id' => $member_id
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'อัปเดตจำนวนสินค้าสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?> 