<?php
include('../config/condb.php');
include('order_functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $order_id = $_POST['order_id'];
    $email = $_POST['customer_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    $result = $orderManager->sendReply($order_id, $email, $subject, $message);
    
    if ($result) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'ส่งข้อความตอบกลับเรียบร้อยแล้ว'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'เกิดข้อผิดพลาดในการส่งข้อความ'
        ];
    }
    
    header("Location: order_detail.php?order_id=$order_id");
    exit;
}

header("Location: order_list.php");
exit;
?>