<?php
session_start();
require_once('config/condb.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบข้อมูลที่ส่งมา
if (!isset($_POST['cart_id']) || !isset($_POST['note'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$cart_id = $_POST['cart_id'];
$note = $_POST['note'];
$member_id = $_SESSION['staff_id'];

// จำกัดความยาวข้อความ
if (strlen($note) > 500) {
    echo json_encode(['success' => false, 'message' => 'ข้อความยาวเกินไป (สูงสุด 500 ตัวอักษร)']);
    exit;
}

try {
    // อัปเดตข้อความเพิ่มเติมในตะกร้า (เฉพาะของผู้ใช้นี้)
    $sql = "UPDATE tbl_cart SET note = :note WHERE cart_id = :cart_id AND member_id = :member_id";
    $stmt = $condb->prepare($sql);
    $result = $stmt->execute([
        'note' => $note,
        'cart_id' => $cart_id,
        'member_id' => $member_id
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'อัปเดตข้อความเพิ่มเติมสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตได้']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?> 