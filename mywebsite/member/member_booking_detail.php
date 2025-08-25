<?php
session_start();
require_once('../config/condb.php');
require_once('../admin/booking_functions.php');  // ใช้คลาส BookingManager
include 'sidebar_menu.php';  
include 'header.php';   // session + css + login check
include 'navbar.php';   // top bar

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$booking_data = $bookingManager->getBookingDetails($booking_id);

$member_id = $booking_data['member_id'];
$stmt = $condb->prepare("SELECT name, surname, tel, email FROM tbl_member WHERE id = ?");
$stmt->execute([$member_id]);
$member_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking_data) {
    echo "ไม่พบข้อมูลการจองของคุณ";
    exit;
}

extract($booking_data);
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>รายละเอียดการจอง #<?= htmlspecialchars($booking_number) ?></h1>
        </div>
        <div class="col-sm-6">
          <a href="booking.php" class="btn btn-secondary float-right">
            <i class="fas fa-arrow-left"></i> กลับ
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">

        <!-- ข้อมูลผู้จอง -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ข้อมูลผู้จอง</h3>
            </div>
            <div class="card-body">
              <p><strong>ชื่อ:</strong> <?= htmlspecialchars($member_data['name']) ?> <?= htmlspecialchars($member_data['surname']) ?></p>
              <p><strong>โทรศัพท์:</strong> <?= htmlspecialchars($member_data['tel'] ?? 'ไม่ระบุ') ?></p>
              <p><strong>อีเมล:</strong> <?= htmlspecialchars($member_data['email'] ?? 'ไม่ระบุ') ?></p>
            </div>
          </div>
        </div>

        <!-- ข้อมูลการจอง -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ข้อมูลการจอง</h3>
            </div>
            <div class="card-body">
              <p><strong>ห้อง:</strong> <?= htmlspecialchars($room_name) ?></p>
              <p><strong>สถานะ:</strong> <?= BookingManager::getStatusBadge($status) ?></p>
              <p><strong>วันที่จอง:</strong> <?= date('d/m/Y H:i', strtotime($booking_date)) ?></p>
              <p><strong>เช็คอิน:</strong> <?= date('d/m/Y', strtotime($check_in_date)) ?></p>
              <p><strong>เช็คเอาท์:</strong> <?= date('d/m/Y', strtotime($check_out_date)) ?></p>
              <p><strong>จำนวนคืน:</strong> <?= $total_nights ?> คืน</p>
              <?php if (!empty($slip_image)): ?>
              <p>
                <?php if ($booking_data['slip_image']): ?>
                <p><strong>สลิป:</strong> <a href="../assets/slips/<?= $booking_data['slip_image'] ?>" target="_blank" class="btn btn-info">ดูสลิป</a></p>
              <?php endif; ?>
              </p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- รายละเอียดการชำระเงิน -->
        <div class="col-12 ">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ข้อมูลการชำระเงิน</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th style="width: 30%;">ยอดรวม</th>
                    <td><?= number_format($total_amount, 2) ?> บาท</td>
                  </tr>
                  <tr>
                    <th>มัดจำ</th>
                    <td><?= number_format($deposit_amount, 2) ?> บาท</td>
                  </tr>
                  <tr>
                    <th>วิธีชำระเงิน</th>
                    <td><?= htmlspecialchars($payment_method) ?></td>
                  </tr>
                  <tr>
                    <th>สถานะการชำระ</th>
                    <td><?= htmlspecialchars($payment_status) ?></td>
                  </tr>
                  <tr>
                    <th>วันที่ชำระ</th>
                    <td><?= $payment_date ? date('d/m/Y H:i', strtotime($payment_date)) : '-' ?></td>
                  </tr>
                  <tr>
                    <th>หมายเหตุ</th>
                    <td><?= htmlspecialchars($note) ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>
