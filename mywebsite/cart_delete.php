<?php
session_start();
require_once('config/condb.php');

if (!isset($_SESSION['staff_id'])) {
    echo "กรุณาเข้าสู่ระบบก่อนใช้งาน";
    exit;
}

if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];
    $member_id = $_SESSION['staff_id'];
    
    // ลบรายการในตะกร้าที่เป็นของผู้ใช้นี้เท่านั้น
    $sql = "DELETE FROM tbl_cart WHERE cart_id = :cart_id AND member_id = :member_id";
    $stmt = $condb->prepare($sql);
    $result = $stmt->execute(['cart_id' => $cart_id, 'member_id' => $member_id]);
    
    if ($result) {
        header('Location: cart.php?success=1');
    } else {
        header('Location: cart.php?error=1');
    }
} else {
    header('Location: cart.php');
}
?> 