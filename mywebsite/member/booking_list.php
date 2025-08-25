<?php

// ดึงค่า member_id จาก session
$member_id = $_SESSION['staff_id'] ?? null;

// ตรวจสอบว่ามีการเข้าสู่ระบบหรือไม่
if (!$member_id) {
    echo "กรุณาเข้าสู่ระบบก่อน";
    exit;
}

// ดึงข้อมูลการจองทั้งหมดของผู้ใช้
$stmt = $condb->prepare("SELECT * FROM tbl_booking WHERE member_id = :member_id ORDER BY booking_date DESC");
$stmt->execute(['member_id' => $member_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  .card-body {
    margin-left: 50px; /* ขยับตารางไปทางขวา */
  }

  .text-center-cell {
    text-align: center; /* จัดข้อความให้อยู่ตรงกลางเฉพาะเซลล์ที่มี class นี้ */
  }
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>ข้อมูลการจองของฉัน</h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

<div class="card-body">
 <table id="bookingsTable" class="table table-bordered table-hover">
    <tr class="table-info">
        <th width="2%" class="text-center">No.</th>
        <th width="5%" class="text-center">เลขที่การจอง</th>
        <th width="5%" class="text-center">วันที่จอง</th>
        <th width="5%" class="text-center">ชื่อห้อง</th>
        <th width="5%" class="text-center">วันที่ Check-in</th>
        <th width="5%" class="text-center">วันที่ Check-out</th>
        <th width="5%" class="text-center">จำนวนคืน</th>
        <th width="5%" class="text-center">ยอดรวม</th>
        <th width="5%" class="text-center">สถานะ</th>
        <th width="5%" class="text-center">รายละเอียด</th>
    </tr>

    <?php $i = 1; ?>
    <?php foreach ($bookings as $booking): ?>
    <tr>
        <td class="text-center"><?= $i++ ?></td> <!-- ลำดับ -->
        <td class="text-center"><?= htmlspecialchars($booking['booking_number']) ?></td>
        <td class="text-center"><?= htmlspecialchars($booking['booking_date']) ?></td>
        <td class="text-center"><?= htmlspecialchars($booking['room_name']) ?></td>
        <td class="text-center"><?= htmlspecialchars($booking['check_in_date']) ?></td>
        <td class="text-center"><?= htmlspecialchars($booking['check_out_date']) ?></td>
        <td class="text-center"><?= htmlspecialchars($booking['total_nights']) ?></td>
        <td class="text-center"><?= number_format($booking['total_amount'], 2) ?> บาท</td>
        <td class="text-center">
            <?php
            switch ($booking['status']) {
                case 'รอดำเนินการ':
                    echo '<span style="color:orange;">รอดำเนินการ</span>';
                    break;
                case 'ชำระแล้ว':
                    echo '<span style="color:green;">ชำระแล้ว</span>';
                    break;
                case 'ยกเลิก':
                    echo '<span style="color:red;">ยกเลิก</span>';
                    break;
                default:
                    echo htmlspecialchars($booking['status']);
            }
            ?>
        </td>
        <td class="text-center">
            <a href="member_booking_detail.php?booking_id=<?= $booking['booking_id'] ?>">ดู</a>
        </td>
    </tr>
    <?php endforeach; ?>
 </table>
</div>