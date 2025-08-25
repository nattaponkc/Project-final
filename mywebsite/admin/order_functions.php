<?php
require_once('../config/condb.php');

/**
 * ฟังก์ชันสำหรับจัดการคำสั่งซื้อ
 */

class OrderManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ดึงรายการคำสั่งซื้อทั้งหมด
    public function getAllOrders($status = null) {
        $sql = "SELECT o.*, m.name, m.surname, m.tel 
                FROM tbl_order o
                JOIN tbl_member m ON o.member_id = m.id";
        
        if ($status) {
            $sql .= " WHERE o.status = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->conn->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ดึงรายละเอียดคำสั่งซื้อ
    public function getOrderDetails($order_id) {
        // ดึงข้อมูลคำสั่งซื้อ
        $sql = "SELECT o.*, m.name, m.surname, m.tel, m.email 
                FROM tbl_order o
                JOIN tbl_member m ON o.member_id = m.id
                WHERE o.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) return null;
        
        // ดึงรายการสินค้า
       
$order_item_id = $order['order_id'] . '_' . $order['member_id'];
$sql_items = "SELECT oi.*, p.product_name, p.product_image 
              FROM tbl_order_item oi
              JOIN tbl_product p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
$stmt_items = $this->conn->prepare($sql_items);
$stmt_items->execute([$order_item_id]);
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        // คำนวณยอดรวม
        $total = 0;
        foreach ($items as &$item) {
            $item['subtotal'] = $item['unit_price'] * $item['qty'];
            $total += $item['subtotal'];
        }
        
        return [
            'order' => $order,
            'items' => $items,
            'total' => $total
        ];
    }

    // อัพเดทสถานะคำสั่งซื้อ
    public function updateOrderStatus($order_id, $status, $note = null) {
        $sql = "UPDATE tbl_order SET status = ?, note = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $note, $order_id]);
    }

    // ส่งอีเมลตอบกลับ
    public function sendReply($order_id, $email, $subject, $message) {
        // บันทึกข้อความ
        $this->updateOrderStatus($order_id, null, $message);
        
        // ส่งอีเมล
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: noreply@yourdomain.com\r\n";
        
        $email_content = "<!DOCTYPE html>..."; // HTML template
        
        return mail($email, $subject, $email_content, $headers);
    }

    // แสดงสถานะเป็น Badge
    public static function getStatusBadge($status) {
        $statuses = [
            'pending' => ['text' => 'รอยืนยัน', 'class' => 'bg-warning'],
            'preparing' => ['text' => 'กำลังเตรียม', 'class' => 'bg-info'],
            'ready' => ['text' => 'พร้อมรับ', 'class' => 'bg-primary'],
            'completed' => ['text' => 'รับแล้ว', 'class' => 'bg-success'],
            'cancelled' => ['text' => 'ยกเลิก', 'class' => 'bg-danger']
        ];
        
        return isset($statuses[$status]) ? 
            '<span class="badge '.$statuses[$status]['class'].'">'.$statuses[$status]['text'].'</span>' :
            '<span class="badge bg-secondary">'.$status.'</span>';
    }
}

// สร้าง instance สำหรับใช้งาน
$orderManager = new OrderManager($condb);
?>