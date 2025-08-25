<?php
session_start(); // เริ่มต้น session
include('../config/condb.php');
include('booking_functions.php');

// ตรวจสอบว่าเป็นคำขอแบบ POST และมี booking_id หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'] ?? '';
    $note = $_POST['note'] ?? '';

    try {
        // อัปเดตสถานะการจองใน tbl_booking
        $stmt = $condb->prepare("UPDATE tbl_booking SET status = :status, note = :note WHERE booking_id = :booking_id");
        $stmt->execute([
            'status' => $status,
            'note' => $note,
            'booking_id' => $booking_id
        ]);

        // ตรวจสอบว่าการจองถูกเปลี่ยนเป็น "checked_out" หรือไม่
        if ($status === 'checked_out') {
            // ดึง room_id จากการจอง
            $stmtRoom = $condb->prepare("SELECT room_id FROM tbl_booking WHERE booking_id = :booking_id");
            $stmtRoom->execute(['booking_id' => $booking_id]);
            $room_id = $stmtRoom->fetchColumn();

            if ($room_id) {
                // อัปเดตสถานะห้องเป็น "ว่าง" ใน tbl_room
                $stmtUpdateRoom = $condb->prepare("UPDATE tbl_room SET room_status = 'ว่าง' WHERE room_id = :room_id");
                $stmtUpdateRoom->execute(['room_id' => $room_id]);
            }
        }

        // ดึง member_id ของการจองเพื่อสร้างการแจ้งเตือน
        $stmtMember = $condb->prepare("SELECT member_id FROM tbl_booking WHERE booking_id = :booking_id");
        $stmtMember->execute(['booking_id' => $booking_id]);
        $member_id = $stmtMember->fetchColumn();

        if ($member_id) {
            // เพิ่มการแจ้งเตือนใน tbl_notification
            $stmtNotify = $condb->prepare("INSERT INTO tbl_notification (member_id, title, message, notification_type, related_id) 
                                           VALUES (:member_id, :title, :message, :notification_type, :related_id)");
            $stmtNotify->execute([
                'member_id' => $member_id,
                'title' => 'สถานะการจองอัพเดต',
                'message' => "การจองหมายเลข #$booking_id ถูกอัพเดตสถานะเป็น: $status",
                'notification_type' => 'booking_status',
                'related_id' => $booking_id
            ]);
        }

        // ตั้งค่า alert ใน session
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'อัพเดทสถานะการจองเรียบร้อยแล้ว'
        ];

        // เปลี่ยนเส้นทางกลับไปยังหน้ารายละเอียดการจอง
        header("Location: booking_detail.php?booking_id=$booking_id");
        exit;
    } catch (Exception $e) {
        // ตั้งค่า alert ในกรณีเกิดข้อผิดพลาด
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'เกิดข้อผิดพลาดในการอัพเดทสถานะการจอง: ' . $e->getMessage()
        ];

        // เปลี่ยนเส้นทางกลับไปยังหน้ารายละเอียดการจอง
        header("Location: booking_detail.php?booking_id=$booking_id");
        exit;
    }
}
?>