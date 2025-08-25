<?php
include('../config/condb.php');
include('header.php');
include('navbar.php');
include('sidebar_menu.php');
include('order_functions.php');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order_data = $orderManager->getOrderDetails($order_id);

if (!$order_data) {
    header('Location: order_list.php');
    exit;
}

extract($order_data);
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>รายละเอียดคำสั่งซื้อ #<?= $order['order_id'] ?></h1>
        </div>
        <div class="col-sm-6">
          <a href="order.php" class="btn btn-secondary float-right">
            <i class="fas fa-arrow-left"></i> กลับ
          </a>
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
              <p><strong>ชื่อ:</strong> <?= $order['name'] ?> <?= $order['surname'] ?></p>
              <p><strong>โทรศัพท์:</strong> <?= $order['tel'] ?></p>
              <p><strong>อีเมล:</strong> <?= $order['email'] ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ข้อมูลการสั่งซื้อ</h3>
            </div>
            <div class="card-body">
              
        <p><strong>สถานะ:</strong> <?= OrderManager::getStatusBadge(isset($_GET['status']) ? $_GET['status'] : $order['status']) ?></p>
              <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
              <p><strong>เวลารับ:</strong> <?= date('d/m/Y H:i', strtotime($order['pickup_time'])) ?></p>
              <?php if ($order['slip_image']): ?>
              <p>
                <strong>สลิป:</strong>
                <a href="../assets/slips/<?= $order['slip_image'] ?>" target="_blank" class="btn btn-sm btn-info ml-2">
                  <i class="fas fa-image"></i> ดูสลิป
                </a>
              </p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">รายการสินค้า</h3>
            </div>

            <div class="card-body">
              <table class="table table-bordered">
  <thead>
    <tr>
      <th>ภาพสินค้า</th> 
      <th>สินค้า</th>
      <th>ประเภทสินค้า</th> <th>จำนวน</th>
      <th>ราคา</th>
      <th>รวม</th>
    </tr>
  </thead>
<tbody>
  <?php if (empty($items)): ?>
    <tr>
      <td colspan="6" class="text-center text-muted">ไม่มีรายการสินค้าในคำสั่งซื้อ</td>
    </tr>
  <?php else: ?>
    <?php foreach ($items as $item): ?>
    <tr>
      <td>
        <?php if (!empty($item['product_image'])): ?>
          <img src="../assets/product_img/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" width="50">
        <?php else: ?>
          <span class="text-muted">ไม่มีภาพ</span>
        <?php endif; ?>
      </td>
      <td>
        <?= htmlspecialchars($item['product_name']) ?>
        <?php if (!empty($item['note'])): ?>
          <small class="text-muted d-block"><?= htmlspecialchars($item['note']) ?></small>
        <?php endif; ?>
      </td>
      <td><?= htmlspecialchars($item['price_type']) ?></td>
      <td><?= $item['qty'] ?></td>
      <td><?= number_format($item['unit_price'], 2) ?></td>
      <td><?= number_format($item['subtotal'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</tbody>

<!-- <?php
// var_dump($order_data); // ตรวจสอบข้อมูลคำสั่งซื้อ
// extract($order_data);
?> -->

  <tfoot>
    <tr>
      <th colspan="5" class="text-right">รวมทั้งหมด</th>
      <th><?= number_format($total, 2) ?> บาท</th>
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
              <form action="order_status_update.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                
                <div class="form-group">
                  <label>สถานะ</label>
                  <select name="status" class="form-control" required>
                    <?php
                      $selected_status = isset($_GET['status']) ? $_GET['status'] : $order['status'];
                    ?>
                    <option value="pending" <?= $selected_status == 'pending' ? 'selected' : '' ?>>รอยืนยัน</option>
                    <option value="preparing" <?= $selected_status == 'preparing' ? 'selected' : '' ?>>กำลังเตรียม</option>
                    <option value="ready" <?= $selected_status == 'ready' ? 'selected' : '' ?>>พร้อมรับ</option>
                    <option value="completed" <?= $selected_status == 'completed' ? 'selected' : '' ?>>รับแล้ว</option>
                    <option value="cancelled" <?= $selected_status == 'cancelled' ? 'selected' : '' ?>>ยกเลิก</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>หมายเหตุ</label>
                  <textarea name="note" class="form-control" rows="3"><?= $order['note'] ?? '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> บันทึก
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include('footer.php'); ?>