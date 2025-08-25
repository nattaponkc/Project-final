<?php
session_start();
require_once('config/condb.php');

if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['booking_id'] ?? '';

if (empty($booking_id)) {
    header('Location: cart.php');
    exit;
}
// var_dump($booking_id);
// exit;

// ดึงข้อมูลการจอง
$sql = "SELECT b.*, m.name as customer_name, m.tel as customer_phone
        FROM tbl_booking b
        JOIN tbl_member m ON b.member_id = m.id
        WHERE b.booking_id = :booking_id AND b.member_id = :member_id";
$stmt = $condb->prepare($sql);
$stmt->execute(['booking_id' => $booking_id, 'member_id' => $_SESSION['staff_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: cart.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองสำเร็จ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .success-icon {
            font-size: 4rem;
            color: #28a745;
        }
        .booking-card {
            border: 2px solid #28a745;
            border-radius: 15px;
        }
        .booking-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 13px 13px 0 0;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <?php include 'menu_top.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle success-icon"></i>
                    <h2 class="text-success mt-3">การจองสำเร็จ!</h2>
                    <p class="text-muted">ขอบคุณสำหรับการจองของคุณ</p>
                </div>
                
                <div class="card booking-card shadow">
                    <div class="card-header booking-header">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>รายละเอียดการจอง
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">หมายเลขการจอง</h6>
                                <h5 class="text-primary"><?= $booking['booking_number'] ?></h5>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">วันที่จอง</h6>
                                <h5><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></h5>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">ชื่อลูกค้า</h6>
                                <h5><?= htmlspecialchars($booking['customer_name']) ?></h5>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">เบอร์โทร</h6>
                                <h5><?= htmlspecialchars($booking['customer_phone']) ?></h5>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">วันที่เช็คอิน</h6>
                                <h5 class="text-warning"><?= date('d/m/Y', strtotime($booking['check_in_date'])) ?></h5>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">สถานะ</h6>
                                <span class="badge bg-warning fs-6"><?= $booking['status'] ?></span>
                            </div>
                        </div>
                        
                        <?php if (!empty($booking['note'])): ?>
                        <div class="mb-4">
                            <h6 class="text-muted">หมายเหตุ</h6>
                            <div class="alert alert-info">
                                <?= htmlspecialchars($booking['note']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-warning mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>คำแนะนำ
                            </h6>
                            <ul class="mb-0">
                                <li>กรุณาเช็คอินภายในวันที่ <?= date('d/m/Y', strtotime($booking['check_in_date'])) ?></li>
                                <li>นำหมายเลขการจองมาแสดงเมื่อเช็คอิน</li>
                                <li>หากมีข้อสงสัย กรุณาติดต่อเจ้าหน้าที่</li>
                            </ul>
                        </div>

                        
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">                            
                            <a href="menu.php" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i>จองเพิ่ม
                            </a>
                            
                                <a href="member/booking.php" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>ดูรายการจอง
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>