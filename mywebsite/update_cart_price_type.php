<?php
session_start();
require_once('config/condb.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบข้อมูลที่ส่งมา
if (!isset($_POST['cart_id']) || !isset($_POST['price_type'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$cart_id = $_POST['cart_id'];
$price_type = $_POST['price_type'];
$member_id = $_SESSION['staff_id'];

// ตรวจสอบประเภทราคาที่ถูกต้อง
$valid_price_types = ['hot', 'cold', 'frappe'];
if (!in_array($price_type, $valid_price_types)) {
    echo json_encode(['success' => false, 'message' => 'ประเภทราคาไม่ถูกต้อง']);
    exit;
}

try {
    // อัปเดตประเภทราคาในตะกร้า (เฉพาะของผู้ใช้นี้)
    $sql = "UPDATE tbl_cart SET price_type = :price_type WHERE cart_id = :cart_id AND member_id = :member_id";
    $stmt = $condb->prepare($sql);
    $result = $stmt->execute([
        'price_type' => $price_type,
        'cart_id' => $cart_id,
        'member_id' => $member_id
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'อัปเดตประเภทราคาสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?> 