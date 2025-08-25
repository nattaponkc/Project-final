<?php
require_once('../config/condb.php');

/**
 * ฟังก์ชันสำหรับจัดการการจอง
 */

class BookingManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ดึงรายการจองทั้งหมด
    public function getAllBookings($status = null) {
        $sql = "SELECT b.booking_id, b.booking_number, b.member_id, b.room_id, b.booking_date, b.check_in_date, b.check_out_date, 
                       b.total_nights, b.total_amount, b.deposit_amount, b.payment_method, b.payment_status, b.payment_date, 
                       b.slip_image, b.guest_count, b.cancellation_policy, b.room_name, b.status, b.note, b.created_at, b.updated_at, 
                       m.name, m.surname 
                FROM tbl_booking b
                JOIN tbl_member m ON b.member_id = m.id";

        if ($status) {
            $sql .= " WHERE b.status = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->conn->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ดึงรายละเอียดการจอง
    public function getBookingDetails($booking_id) {
        $sql = "SELECT b.booking_id, b.booking_number, b.member_id, b.room_id, b.booking_date, b.check_in_date, b.check_out_date, 
                       b.total_nights, b.total_amount, b.deposit_amount, b.payment_method, b.payment_status, b.payment_date, 
                       b.slip_image, b.guest_count, b.cancellation_policy, b.room_name, b.status, b.note, b.created_at, b.updated_at, 
                       m.name, m.surname 
                FROM tbl_booking b
                JOIN tbl_member m ON b.member_id = m.id
                WHERE b.booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // อัพเดทสถานะการจอง
    public function updateBookingStatus($booking_id, $status, $note = null) {
        $sql = "UPDATE tbl_booking SET status = ?, note = ? WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $note, $booking_id]);
    }

    // แสดงสถานะเป็น Badge
    public static function getStatusBadge($status) {
        $statuses = [
            'pending' => ['text' => 'รอยืนยัน', 'class' => 'bg-warning'],
            'confirmed' => ['text' => 'ยืนยันแล้ว', 'class' => 'bg-info'],
            'checked_in' => ['text' => 'เช็คอินแล้ว', 'class' => 'bg-primary'],
            'checked_out' => ['text' => 'เช็คเอาท์แล้ว', 'class' => 'bg-success'],
            'cancelled' => ['text' => 'ยกเลิก', 'class' => 'bg-danger']
        ];

        return isset($statuses[$status]) ? 
            '<span class="badge '.$statuses[$status]['class'].'">'.$statuses[$status]['text'].'</span>' :
            '<span class="badge bg-secondary">'.$status.'</span>';
    }
}

// สร้าง instance สำหรับใช้งาน
$bookingManager = new BookingManager($condb);
?>