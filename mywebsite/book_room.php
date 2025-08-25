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
$room_id = $_POST['room_id'] ?? '';
$room_price = $_POST['room_price'] ?? 0;
$check_in_date = $_POST['check_in_date'] ?? '';
$check_out_date = $_POST['check_out_date'] ?? '';
$guest_count = $_POST['guest_count'] ?? 1;
$payment_method = $_POST['payment_method'] ?? 'โอนเงินเต็ม';
$note = $_POST['note'] ?? '';
$member_id = $_SESSION['staff_id'];

// ตรวจสอบข้อมูล
if (empty($room_id) || empty($check_in_date) || empty($check_out_date)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// ตรวจสอบวันที่
$check_in = new DateTime($check_in_date);
$check_out = new DateTime($check_out_date);
$today = new DateTime();

if ($check_in < $today) {
    echo json_encode(['success' => false, 'message' => 'วันที่ Check-in ต้องไม่น้อยกว่าวันที่ปัจจุบัน']);
    exit;
}

if ($check_out <= $check_in) {
    echo json_encode(['success' => false, 'message' => 'วันที่ Check-out ต้องมากกว่าวันที่ Check-in']);
    exit;
}

try {
    // ดึงข้อมูลห้อง
    $sql_room = "SELECT * FROM tbl_room WHERE room_id = :room_id";
    $stmt_room = $condb->prepare($sql_room);
    $stmt_room->execute(['room_id' => $room_id]);
    $room = $stmt_room->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบห้องพัก']);
        exit;
    }


    // ตรวจสอบจำนวนคน
    if ($guest_count > $room['max_guests']) {
        echo json_encode(['success' => false, 'message' => 'จำนวนคนเกินกว่าที่ห้องรองรับได้']);
        exit;
    }

    // ปรับราคาตามฤดูกาล
    $season_multiplier = $room['season_type'] === 'high' ? 1.5 : 1.0;
    $adjusted_room_price = $room_price * $season_multiplier;

    // คำนวณจำนวนคืนและราคารวม
    $nights = $check_in->diff($check_out)->days;
    $total_amount = $adjusted_room_price * $nights;
    $deposit_amount = $room['deposit_required'] > 0 ? $room['deposit_required'] : ($payment_method === 'โอนเงินครึ่ง' ? $total_amount * 0.5 : $total_amount);

    // สร้างเลขที่การจอง
    $booking_number = 'BK' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);



    // ตรวจสอบว่ามีการจองซ้อนหรือไม่
    $sql_check_status = "SELECT status FROM tbl_booking 
                     WHERE room_id = :room_id 
                     AND :check_in_date BETWEEN check_in_date AND check_out_date 
                     AND status NOT IN ('cancelled', 'checked_out')";
    $stmt_check_status = $condb->prepare($sql_check_status);
    $stmt_check_status->execute([
        'room_id' => $room_id,
        'check_in_date' => $check_in_date
    ]);
    $booking_status = $stmt_check_status->fetch(PDO::FETCH_ASSOC);

    if ($booking_status) {
        echo json_encode(['success' => false, 'message' => 'ห้องพักไม่ว่าง']);
        exit;
    }

    // ตรวจสอบและจัดการไฟล์ slip_image
    if (isset($_FILES['slip_image']) && $_FILES['slip_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/slips/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['slip_image']['tmp_name'];
        $fileName = basename($_FILES['slip_image']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'ไฟล์สลิปต้องเป็นรูปภาพ (jpg, jpeg, png, gif)']);
            exit;
        }

        $newFileName = uniqid('slip_', true) . '.' . $fileExtension;
        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $destination)) {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์สลิป']);
            exit;
        }

        // เปลี่ยนสถานะการชำระเงินเป็น "ชำระแล้ว"
        $payment_status = 'ชำระแล้ว';
    } else {
        echo json_encode(['success' => false, 'message' => 'กรุณาแนบไฟล์สลิปการโอนเงิน']);
        exit;
    }

    // บันทึกการจอง
    $sql_booking = "INSERT INTO tbl_booking (
                        booking_number, member_id, room_id, booking_date, 
                        check_in_date, check_out_date, total_nights, total_amount, 
                        deposit_amount, payment_method, payment_status, guest_count, 
                        room_name, status, note, slip_image, created_at
                    ) VALUES (
                        :booking_number, :member_id, :room_id, NOW(),
                        :check_in_date, :check_out_date, :total_nights, :total_amount,
                        :deposit_amount, :payment_method, :payment_status, :guest_count,
                        :room_name, 'รอดำเนินการ', :note, :slip_image, NOW()
                    )";

    $stmt_booking = $condb->prepare($sql_booking);
    $result_booking = $stmt_booking->execute([
        'booking_number' => $booking_number,
        'member_id' => $member_id,
        'room_id' => $room_id,
        'check_in_date' => $check_in_date,
        'check_out_date' => $check_out_date,
        'total_nights' => $nights,
        'total_amount' => $total_amount,
        'deposit_amount' => $deposit_amount,
        'payment_method' => $payment_method,
        'payment_status' => $payment_status,
        'guest_count' => $guest_count,
        'room_name' => $room['room_name'],
        'note' => $note,
        'slip_image' => $newFileName
    ]);


    
if ($result_booking) {
        $booking_id = $condb->lastInsertId(); // ดึง booking_id หลังการบันทึกสำเร็จ

        // สร้างการแจ้งเตือน
        $sql_notification = "INSERT INTO tbl_notification (
                         member_id, title, message, notification_type, related_id, created_at
                     ) VALUES (
                         :member_id, :title, :message, 'booking_status', :booking_id, NOW()
                     )";
        $stmt_notification = $condb->prepare($sql_notification);
        $stmt_notification->execute([
            'member_id' => $member_id,
            'title' => 'การจองห้องพัก',
            'message' => "การจองห้อง {$room['room_name']} เรียบร้อยแล้ว กรุณาชำระเงิน {$deposit_amount} บาท",
            'booking_id' => $booking_id
        ]);

        // ส่ง JSON Response
        echo json_encode([
            'success' => true,
            'message' => 'จองห้องเรียบร้อยแล้ว',
            'booking_id' => $booking_id, // ส่ง booking_id กลับไป
            'booking_number' => $booking_number,
            'total_amount' => $total_amount,
            'deposit_amount' => $deposit_amount
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    exit;
}
