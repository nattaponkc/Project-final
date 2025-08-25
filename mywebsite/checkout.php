<?php
session_start();
require_once('config/condb.php');

if (!isset($_SESSION['staff_id'])) {
    echo "กรุณาเข้าสู่ระบบก่อนใช้งาน";
    exit;
}

$member_id = $_SESSION['staff_id'];

// ดึงรายการในตะกร้าของผู้ใช้
$sql = "SELECT c.*, p.product_name, p.product_image, p.product_price, p.price_hot, p.price_cold, p.price_frappe
        FROM tbl_cart AS c
        JOIN tbl_product AS p ON c.product_id = p.id
        WHERE c.member_id = :member_id";
$stmt = $condb->prepare($sql);
$stmt->execute(['member_id' => $member_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header('Location: cart.php?error=empty_cart');
    exit;
}

// คำนวณยอดรวม
$total = 0;
foreach ($cart_items as $item) {
    // ใช้ราคาตามประเภทที่เลือก
    $price_type = $item['price_type'] ?? 'hot';
    switch ($price_type) {
        case 'hot':
            $price = $item['price_hot'];
            break;
        case 'cold':
            $price = $item['price_cold'];
            break;
        case 'frappe':
            $price = $item['price_frappe'];
            break;
        default:
            $price = $item['price_hot'];
    }
    $total += $price * $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.8em;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #1565c0;
        }

        .card-header {
            border-bottom: none;
        }

        .btn-lg {
            padding: 12px 24px;
        }
    </style>
</head>

<body>
    <?php include 'menu_top.php'; ?>
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">ยืนยันการสั่งซื้อ</h2>
                        <p class="text-muted mb-0">ตรวจสอบรายการสินค้าและยืนยันการสั่งซื้อ</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h4>รายการสินค้า</h4>
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="35%">สินค้า</th>
                            <th width="15%" class="text-center">ประเภท/ราคา</th>
                            <th width="10%" class="text-center">จำนวน</th>
                            <th width="15%" class="text-center">ราคารวม</th>
                            <th width="25%" class="text-center">ข้อความเพิ่มเติม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item):
                            // ใช้ราคาตามประเภทที่เลือก
                            $price_type = $item['price_type'] ?? 'hot';
                            switch ($price_type) {
                                case 'hot':
                                    $price = $item['price_hot'];
                                    $price_text = 'ร้อน';
                                    break;
                                case 'cold':
                                    $price = $item['price_cold'];
                                    $price_text = 'เย็น';
                                    break;
                                case 'frappe':
                                    $price = $item['price_frappe'];
                                    $price_text = 'ปั่น';
                                    break;
                                default:
                                    $price = $item['price_hot'];
                                    $price_text = 'ร้อน';
                            }
                            $item_total = $price * $item['qty'];
                        ?>
                            <tr>
                                <td>
                                    <img src="assets/product_img/<?= $item['product_image'] ?>" width="50" class="me-2">
                                    <div>
                                        <strong><?= $item['product_name'] ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            (เครื่องดื่ม: ร้อน <?= number_format($item['price_hot'], 0) ?>฿,
                                            เย็น <?= number_format($item['price_cold'], 0) ?>฿,
                                            ปั่น <?= number_format($item['price_frappe'], 0) ?>฿)
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $price_text ?></span>
                                    <br>
                                    <strong><?= number_format($price, 0) ?> ฿</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $item['qty'] ?></span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success"><?= number_format($item_total, 2) ?> บาท</strong>
                                </td>
                                <td>
                                    <?php if (!empty($item['note'])): ?>
                                        <div class="alert alert-info py-1 mb-0">
                                            <small><?= htmlspecialchars($item['note']) ?></small>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted">ไม่มีข้อความเพิ่มเติม</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>สรุปการสั่งซื้อ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>จำนวนสินค้า:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <?= count($cart_items) ?> รายการ
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>จำนวนชิ้น:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <?php
                                $total_qty = 0;
                                foreach ($cart_items as $item) {
                                    $total_qty += $item['qty'];
                                }
                                echo $total_qty;
                                ?> ชิ้น
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <h5 class="text-primary">ยอดรวมทั้งหมด:</h5>
                            </div>
                            <div class="col-6 text-end">
                                <h5 class="text-danger"><?= number_format($total, 2) ?> บาท</h5>
                            </div>
                        </div>
                        <hr>
                        <!-- Price Table & Bank Info -->
                        <?php
                        // ดึงข้อมูลธนาคาร
                        $stmtBank = $condb->prepare("SELECT * FROM tbl_bank ORDER BY id ASC");
                        $stmtBank->execute();
                        $banks = $stmtBank->fetchAll();
                        ?>
                        <div class="mb-3">
                            <h5 class="text-primary">โอนเงินเข้าบัญชีธนาคาร</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ธนาคาร</th>
                                            <th>เลขบัญชี</th>
                                            <th>ชื่อบัญชี</th>
                                            <th>สาขา</th>
                                            <th>QR Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($banks as $bank): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($bank['bank_name']) ?></td>
                                                <td><?= htmlspecialchars($bank['bank_account_number']) ?></td>
                                                <td><?= htmlspecialchars($bank['bank_account_name']) ?></td>
                                                <td><?= htmlspecialchars($bank['bank_branch']) ?></td>
                                                <td>
                                                    <?php if (!empty($bank['bank_qrcode'])): ?>
                                                        <a href="assets/bank_qrcode/<?= htmlspecialchars($bank['bank_qrcode']) ?>" target="_blank">
                                                            <img src="assets/bank_qrcode/<?= htmlspecialchars($bank['bank_qrcode']) ?>" width="60" height="60" style="object-fit:cover;">
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <form action="process_order.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="slip_image" class="form-label">แนบสลิปโอนเงิน <span class="text-danger">*</span></label>
                                <input type="file" name="slip_image" id="slip_image" class="form-control" required accept="image/*">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>ยืนยันการสั่งซื้อ
                                </button>
                                <a href="cart.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>กลับไปตะกร้า
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>