<?php
// คิวรี่ข้อมูลการจองทั้งหมด
require_once('../config/condb.php');
include('booking_functions.php');
include('header.php');
 include('navbar.php');
include('sidebar_menu.php');

$bookingManager = new BookingManager($condb);

$status_filter = $_GET['status'] ?? null;
$bookings = $bookingManager->getAllBookings($status_filter);
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>จัดการรายการจอง</h1>
        </div>
        <div class="col-sm-6">
          <div class="float-right">
            <a href="?status=pending" class="btn btn-sm btn-warning mr-2">
              <i class="fas fa-clock"></i> รอยืนยัน
            </a>
            <a href="?status=confirmed" class="btn btn-sm btn-info mr-2">
              <i class="fas fa-check"></i> ยืนยันแล้ว
            </a>
            <a href="?status=checked_in" class="btn btn-sm btn-primary mr-2">
              <i class="fas fa-door-open"></i> เช็คอินแล้ว
            </a>
            <a href="?status=checked_out" class="btn btn-sm btn-success">
              <i class="fas fa-door-closed"></i> เช็คเอาท์แล้ว
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <table id="bookingsTable" class="table table-bordered table-hover table-striped">
                <thead class="table-info">
                  <tr>
                    <th>เลขที่การจอง</th>
                    <th>ลูกค้า</th>
                    <th>วันที่จอง</th>
                    <th>ยอดรวม</th>
                    <th>สถานะ</th>
                    <th>การดำเนินการ</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($bookings as $booking): ?>
                    <tr>
                      <td>#<?= $booking['booking_number'] ?></td>
                      <td>
                        <?= htmlspecialchars($booking['name']) ?> <?= htmlspecialchars($booking['surname']) ?>
                      </td>
                      <td><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></td>
                      <td><?= number_format($booking['total_amount'], 2) ?> บาท</td>
                      <td>
                        <?= BookingManager::getStatusBadge($booking['status']) ?>
                      </td>
                      <td align="center">
                        <a href="booking_detail.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-primary">
                          <i class="fas fa-eye"></i> ดู
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(document).ready(function() {
    $('#bookingsTable').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
      },
      "order": [
        [2, "desc"]
      ]
    });
  });
</script>