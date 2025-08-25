<?php
// คิวรี่ข้อมูลออเดอร์ทั้งหมด
require_once('../config/condb.php');
include('order_functions.php');
$orderManager = new OrderManager($condb);
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>จัดการคำสั่งซื้อ</h1>
        </div>
        <div class="col-sm-6">
          <div class="float-right">
            <a href="?status=pending" class="btn btn-sm btn-warning mr-2">
              <i class="fas fa-clock"></i> รอยืนยัน
            </a>
            <a href="?status=preparing" class="btn btn-sm btn-info mr-2">
              <i class="fas fa-spinner"></i> กำลังเตรียม
            </a>
            <a href="?status=ready" class="btn btn-sm btn-primary mr-2">
              <i class="fas fa-check"></i> พร้อมรับ
            </a>
            <a href="order_list.php" class="btn btn-sm btn-secondary">
              <i class="fas fa-list"></i> ทั้งหมด
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
              <table id="ordersTable" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th width="2%" class="text-center">No.</th>
                    <th>เลขที่คำสั่งซื้อ</th>
                    <th>ลูกค้า</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>ยอดรวม</th>
                    <th>สถานะ</th>
                    <th width="15%" class="text-center">การดำเนินการ</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $status_filter = $_GET['status'] ?? null;
                  $orders = $orderManager->getAllOrders($status_filter);

                  $i = 1; //start number
                  ?>

                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td class="text-center"><?= $i++ ?></td>
                      <td>#<?= $order['order_id'] ?></td>
                      <td>
                        <?= $order['name'] ?> <?= $order['surname'] ?>
                        <br><small><?= $order['tel'] ?></small>
                      </td>
                      <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                      <td><?= number_format($order['total_amount'], 2) ?> บาท</td>
                      <td>

                        <?= OrderManager::getStatusBadge($order['status']) ?>
                      </td>
                      <td align="center">
                        <a href="order_detail.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-primary">
                          <i class="fas fa-eye"></i> ดู
                        </a>

                        <a href="order.php?id=<?= $order['order_id'] ?>&act=delete"
                          class="btn btn-danger btn-sm"
                          onclick="return confirm('ยืนยันการลบข้อมูล??');">ลบ</a>

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
    $('#ordersTable').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
      },
      "order": [
        [2, "desc"]
      ]
    });
  });
</script>