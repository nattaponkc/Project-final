<?php
session_start(); // เพิ่ม session_start() เพื่อให้สามารถใช้งาน $_SESSION ได้
include('../config/condb.php');
include('order_functions.php');

// สร้าง OrderManager Object
$orderManager = new OrderManager($condb);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $note = $_POST['note'] ?? null;
    
    $result = $orderManager->updateOrderStatus($order_id, $status, $note);
    
    if ($result) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'อัพเดทสถานะเรียบร้อยแล้ว'
        ];
        // ดึง member_id ของ order
        $sql_get_member = "SELECT member_id FROM tbl_order WHERE order_id = ?";
        $stmt_get_member = $condb->prepare($sql_get_member);
        $stmt_get_member->execute([$order_id]);
        $row_member = $stmt_get_member->fetch(PDO::FETCH_ASSOC);
        $member_id_notify = $row_member ? $row_member['member_id'] : null;

        if ($member_id_notify) {
           $sql_notify = "INSERT INTO tbl_notification (member_id, title, message, notification_type, related_id) VALUES (?, ?, ?, ?, ?)";
$stmt_notify = $condb->prepare($sql_notify);

            $title = "สถานะคำสั่งซื้ออัพเดต";
            $message = "คำสั่งซื้อหมายเลข #$order_id ถูกอัพเดตสถานะเป็น: $status";
            $notification_type = "order_status";
           $stmt_notify->execute([$member_id_notify, $title, $message, $notification_type, $order_id]);
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'เกิดข้อผิดพลาดในการอัพเดทสถานะ'
        ];
    }
    
        header("Location: order_detail.php?order_id=$order_id&status=$status");
        exit;
}

?>