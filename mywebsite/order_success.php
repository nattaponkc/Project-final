<?php
session_start();
require_once('config/condb.php');

if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    header('Location: cart.php');
    exit;
}

// ดึงข้อมูลการสั่งซื้อ
$sql = "SELECT o.*, m.name as customer_name, m.tel as customer_phone
        FROM tbl_order o
        JOIN tbl_member m ON o.member_id = m.id
        WHERE o.order_id = :order_id AND o.member_id = :member_id";
$stmt = $condb->prepare($sql);
$stmt->execute(['order_id' => $order_id, 'member_id' => $_SESSION['staff_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: cart.php');
    exit;
}

// ดึงรายการสินค้า
$sql_items = "SELECT * FROM tbl_order_item WHERE order_id = :order_id";
$stmt_items = $condb->prepare($sql_items);
$stmt_items->execute(['order_id' => $order_id]);
$order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อสำเร็จ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .success-icon {
            font-size: 4rem;
            color: #28a745;
        }
        .order-card {
            border: 2px solid #28a745;
            border-radius: 15px;
        }
        .order-header {
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
                    <h2 class="text-success mt-3">สั่งซื้อสำเร็จ!</h2>
                    <p class="text-muted">ขอบคุณสำหรับการสั่งซื้อของคุณ</p>
                </div>
                
                <div class="card order-card shadow">
                    <div class="card-header order-header">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>รายละเอียดการสั่งซื้อ
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">หมายเลขคำสั่งซื้อ</h6>
                                <h5 class="text-primary"><?= $order['order_id'] ?></h5>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">วันที่สั่งซื้อ</h6>
                                <h5><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></h5>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">ชื่อลูกค้า</h6>
                                <h5><?= htmlspecialchars($order['customer_name']) ?></h5>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">เบอร์โทร</h6>
                                <h5><?= htmlspecialchars($order['customer_phone']) ?></h5>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">เวลารับสินค้า</h6>
                                <h5 class="text-warning"><?= date('d/m/Y H:i', strtotime($order['pickup_time'])) ?></h5>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">สถานะ</h6>
                                <span class="badge bg-warning fs-6"><?= $order['status'] ?></span>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['customer_note'])): ?>
                        <div class="mb-4">
                            <h6 class="text-muted">หมายเหตุ</h6>
                            <div class="alert alert-info">
                                <?= htmlspecialchars($order['customer_note']) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <h6 class="text-muted mb-3">รายการสินค้า</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>สินค้า</th>
                                        <th class="text-center">ประเภท</th>
                                        <th class="text-center">ราคา</th>
                                        <th class="text-center">จำนวน</th>
                                        <th class="text-end">รวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                            <?php if (!empty($item['note'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($item['note']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">
                                                <?php 
                                                switch($item['price_type']) {
                                                    case 'hot': echo 'ร้อน'; break;
                                                    case 'cold': echo 'เย็น'; break;
                                                    case 'frappe': echo 'ปั่น'; break;
                                                    default: echo 'ร้อน';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= number_format($item['unit_price'], 0) ?> ฿</td>
                                        <td class="text-center"><?= $item['qty'] ?></td>
                                        <td class="text-end"><?= number_format($item['total_price'], 2) ?> ฿</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="4" class="text-end"><strong>ยอดรวมทั้งหมด:</strong></td>
                                        <td class="text-end"><strong><?= number_format($order['total_amount'], 2) ?> ฿</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="alert alert-warning mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>คำแนะนำ
                            </h6>
                            <ul class="mb-0">
                                <li>กรุณารอรับสินค้าภายในเวลา <?= date('H:i', strtotime($order['pickup_time'])) ?></li>
                                <li>นำหมายเลขคำสั่งซื้อมาแสดงเมื่อมารับสินค้า</li>
                                <li>หากมีข้อสงสัย กรุณาติดต่อเจ้าหน้าที่</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="menu.php" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i>สั่งซื้อเพิ่ม
                            </a>
                            <a href="member/order.php" class="btn btn-primary">
                                <i class="fas fa-list me-2"></i>ดูรายการสั่งซื้อ
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