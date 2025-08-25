<?php
session_start();
require_once('../config/condb.php');
require_once('../admin/order_functions.php');
include 'sidebar_menu.php';  
include 'header.php';           // session + css + login check
include 'navbar.php';          // top bar

$order_id = $_GET['order_id'];
$order_data = $orderManager->getOrderDetails($order_id);

if (!$order_data) {
    echo "ไม่พบคำสั่งซื้อของคุณ";
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
              <p><strong>สถานะ:</strong> <?= OrderManager::getStatusBadge($order['status']) ?></p>
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
                    <th>ประเภทสินค้า</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>รวม</th>
                  </tr>
                </thead>
                <tbody>
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
                        <small class="text-muted d-block">เพิ่มเติม: <?= htmlspecialchars($item['note']) ?></small>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['price_type']) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td><?= number_format($item['unit_price'], 2) ?></td>
                    <td><?= number_format($item['subtotal'], 2) ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
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
      </div>
    </div>
  </section>
</div>
