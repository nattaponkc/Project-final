<?php
include('../config/condb.php');
include('header.php');
include('navbar.php');
include('sidebar_menu.php');
include('booking_functions.php');

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$booking_data = $bookingManager->getBookingDetails($booking_id);

$member_id = $booking_data['member_id'];
$stmt = $condb->prepare("SELECT name, surname, tel, email FROM tbl_member WHERE id = ?");
$stmt->execute([$member_id]);
$member_data = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$booking_data) {
  echo "ไม่พบข้อมูลการจอง";
  exit;
}

?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>รายละเอียดการจอง #<?= $booking_data['booking_number'] ?></h1>
        </div>
        <div class="col-sm-6">
          <a href="booking_order_list.php" class="btn btn-secondary float-right">กลับ</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ข้อมูลลูกค้า</h3>
            </div>
            <div class="card-body">
              <p><strong>ชื่อ:</strong> <?= htmlspecialchars($member_data['name']) ?> <?= htmlspecialchars($member_data['surname']) ?></p>
              <p><strong>โทรศัพท์:</strong> <?= htmlspecialchars($member_data['tel'] ?? 'ไม่ระบุ') ?></p>
              <p><strong>อีเมล:</strong> <?= htmlspecialchars($member_data['email'] ?? 'ไม่ระบุ') ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ข้อมูลการจอง</h3>
            </div>
            <div class="card-body">
              <p><strong>สถานะ:</strong> <?= BookingManager::getStatusBadge($booking_data['status']) ?></p>
              <p><strong>วันที่จอง:</strong> <?= date('d/m/Y H:i', strtotime($booking_data['booking_date'])) ?></p>
              <p><strong>วันที่เช็คอิน:</strong> <?= date('d/m/Y', strtotime($booking_data['check_in_date'])) ?></p>
              <p><strong>วันที่เช็คเอาท์:</strong> <?= date('d/m/Y', strtotime($booking_data['check_out_date'])) ?></p>
              <?php if ($booking_data['slip_image']): ?>
                <p><strong>สลิป:</strong> <a href="../assets/slips/<?= $booking_data['slip_image'] ?>" target="_blank" class="btn btn-info">ดูสลิป</a></p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">รายการห้องพัก</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ชื่อห้อง</th>
                    <th>จำนวนคืน</th>
                    <th>จำนวนเงิน</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?= htmlspecialchars($booking_data['room_name']) ?></td>
                    <td><?= $booking_data['total_nights'] ?></td>
                    <td><?= number_format($booking_data['total_amount'], 2) ?> บาท</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="2" class="text-right">รวมทั้งหมด</th>
                    <th><?= number_format($booking_data['total_amount'], 2) ?> บาท</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">อัพเดทสถานะ</h3>
            </div>
            <div class="card-body">
              <form action="booking_status_update.php" method="POST">
                <input type="hidden" name="booking_id" value="<?= $booking_data['booking_id'] ?>">
                <div class="form-group">
                  <label for="status">สถานะ</label>
                  <select name="status" id="status" class="form-control">
                    <option value="pending" <?= $booking_data['status'] == 'pending' ? 'selected' : '' ?>>รอยืนยัน</option>
                    <option value="confirmed" <?= $booking_data['status'] == 'confirmed' ? 'selected' : '' ?>>ยืนยันแล้ว</option>
                    <option value="checked_in" <?= $booking_data['status'] == 'checked_in' ? 'selected' : '' ?>>เช็คอินแล้ว</option>
                    <option value="checked_out" <?= $booking_data['status'] == 'checked_out' ? 'selected' : '' ?>>เช็คเอาท์แล้ว</option>
                    <option value="cancelled" <?= $booking_data['status'] == 'cancelled' ? 'selected' : '' ?>>ยกเลิก</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="note">หมายเหตุ</label>
                  <textarea name="note" id="note" class="form-control" rows="3"><?= htmlspecialchars($booking_data['note'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">บันทึก</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include('footer.php'); ?>