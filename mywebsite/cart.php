<?php
session_start();
require_once('config/condb.php');

if (!isset($_SESSION['staff_id'])) {
    echo "กรุณาเข้าสู่ระบบก่อนใช้งาน";
    exit;
}
// ดึง member_id จาก session
$member_id = $_SESSION['staff_id'];



// ดึงรายการในตะกร้าของผู้ใช้
$sql = "SELECT c.*, p.product_name, p.product_image, p.product_price, p.price_hot, p.price_cold, p.price_frappe
        FROM tbl_cart AS c
        JOIN tbl_product AS p ON c.product_id = p.id
        WHERE c.member_id = :member_id";
$stmt = $condb->prepare($sql);
$stmt->execute(['member_id' => $member_id]);
$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .input-group .btn {
            border-radius: 0;
        }
        .input-group .form-control {
            border-left: 0;
            border-right: 0;
        }
        .table td {
            vertical-align: middle;
        }
        .item-total {
            font-weight: bold;
            color: #28a745;
        }
        #grand-total {
            color: #dc3545;
            font-size: 1.2em;
        }
        .btn-order {
            background-color: #c6a15b;
            color: white;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-order:hover {
            background-color: #b18d4d;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'menu_top.php'; ?>
    <div class="container mt-5">
        <h2>ตะกร้าสินค้าของคุณ</h2>
        
        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">
                <p>ไม่มีสินค้าในตะกร้า</p>
                <a href="menu.php" class="btn btn-primary">เลือกสินค้า</a>
            </div>
        <?php else: ?>
        <form action="checkout.php" method="post">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th width="30%">สินค้า</th>
                    <th width="15%" class="text-center">ราคา</th>
                    <th width="15%" class="text-center">จำนวน</th>
                    <th width="15%" class="text-center">ราคารวม</th>
                    <th width="15%" class="text-center">ข้อความเพิ่มเติม</th>
                    <th width="10%" class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $total = 0;
    foreach ($cart_items as $item):
        // ใช้ราคาแบบร้อนเป็นค่าเริ่มต้น
        $price_type = $item['price_type'] ?? 'hot';
        switch($price_type) {
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
        $sum = $price * $item['qty'];
        $total += $sum;
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
            <select class="form-select form-select-sm" id="price_type_<?= $item['cart_id'] ?>" onchange="updatePriceType(<?= $item['cart_id'] ?>)">
                <option value="hot" <?= ($item['price_type'] ?? 'hot') == 'hot' ? 'selected' : '' ?>>ร้อน <?= number_format($item['price_hot'], 0) ?>฿</option>
                <option value="cold" <?= ($item['price_type'] ?? 'hot') == 'cold' ? 'selected' : '' ?>>เย็น <?= number_format($item['price_cold'], 0) ?>฿</option>
                <option value="frappe" <?= ($item['price_type'] ?? 'hot') == 'frappe' ? 'selected' : '' ?>>ปั่น <?= number_format($item['price_frappe'], 0) ?>฿</option>
            </select>
        </td>
        <td class="text-center">
            <div class="input-group" style="width: 120px; margin: 0 auto;">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(<?= $item['cart_id'] ?>, -1)">-</button>
                <input type="number" class="form-control text-center" id="qty_<?= $item['cart_id'] ?>" value="<?= $item['qty'] ?>" min="1" max="99" onchange="updateQuantity(<?= $item['cart_id'] ?>, 0, this.value)">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(<?= $item['cart_id'] ?>, 1)">+</button>
            </div>
        </td>
        <td class="text-end item-total" id="total_<?= $item['cart_id'] ?>" 
            data-price-hot="<?= $item['price_hot'] ?>" 
            data-price-cold="<?= $item['price_cold'] ?>" 
            data-price-frappe="<?= $item['price_frappe'] ?>">
            <?= number_format($price * $item['qty'], 2) ?> บาท
        </td>
        <td>
            <textarea class="form-control form-control-sm" id="note_<?= $item['cart_id'] ?>" 
                      placeholder="เช่น หวานน้อย, ไม่ใส่น้ำแข็ง" 
                      rows="2" 
                      onchange="updateNote(<?= $item['cart_id'] ?>)"><?= htmlspecialchars($item['note'] ?? '') ?></textarea>
        </td>
        <td class="text-center">
            <a href="cart_delete.php?cart_id=<?= $item['cart_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ลบสินค้านี้ใช่ไหม?')">
                <i class="fas fa-trash"></i> ลบ
            </a>
        </td>
    </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-info">
                    <td colspan="4" class="text-end"><strong>รวมทั้งหมด:</strong></td>
                    <td colspan="2" class="text-end"><strong id="grand-total"><?= number_format($total, 2) ?> บาท</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-3">
            <a href="menu.php" class="btn btn-secondary"><< เลือกสินค้าเพิ่ม</a>
            <button type="submit" class="btn btn-success">สั่งซื้อสินค้า</button>
        </div>
        </form>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    
    <script>
    function updateQuantity(cartId, change, newValue = null) {
        let qtyInput = document.getElementById('qty_' + cartId);
        let currentQty = parseInt(qtyInput.value);
        
        if (newValue !== null) {
            currentQty = parseInt(newValue);
        } else {
            currentQty += change;
        }
        
        // ตรวจสอบขอบเขต
        if (currentQty < 1) currentQty = 1;
        if (currentQty > 99) currentQty = 99;
        
        qtyInput.value = currentQty;
        
        // อัปเดตราคารวมของสินค้านี้
        updateItemTotal(cartId, currentQty);
        
        // อัปเดตยอดรวมทั้งหมด
        updateGrandTotal();
        
        // ส่งข้อมูลไปอัปเดตฐานข้อมูล
        updateCartInDatabase(cartId, currentQty);
    }
    
    function updatePriceType(cartId) {
        let priceSelect = document.getElementById('price_type_' + cartId);
        let priceType = priceSelect.value;
        let qty = parseInt(document.getElementById('qty_' + cartId).value);
        
        // อัปเดตราคารวมของสินค้านี้
        updateItemTotal(cartId, qty, priceType);
        
        // อัปเดตยอดรวมทั้งหมด
        updateGrandTotal();
        
        // ส่งข้อมูลไปอัปเดตฐานข้อมูล
        updateCartPriceType(cartId, priceType);
    }
    
    function updateItemTotal(cartId, qty, priceType = null) {
        let totalElement = document.getElementById('total_' + cartId);
        let priceHot = parseFloat(totalElement.getAttribute('data-price-hot'));
        let priceCold = parseFloat(totalElement.getAttribute('data-price-cold'));
        let priceFrappe = parseFloat(totalElement.getAttribute('data-price-frappe'));
        
        if (priceType === null) {
            priceType = document.getElementById('price_type_' + cartId).value;
        }
        
        let price;
        switch(priceType) {
            case 'hot':
                price = priceHot;
                break;
            case 'cold':
                price = priceCold;
                break;
            case 'frappe':
                price = priceFrappe;
                break;
            default:
                price = priceHot;
        }
        
        let total = price * qty;
        totalElement.textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท';
    }
    
    function updateGrandTotal() {
        let itemTotals = document.querySelectorAll('.item-total');
        let grandTotal = 0;
        
        itemTotals.forEach(function(element) {
            let cartId = element.id.replace('total_', '');
            let priceType = document.getElementById('price_type_' + cartId).value;
            let priceHot = parseFloat(element.getAttribute('data-price-hot'));
            let priceCold = parseFloat(element.getAttribute('data-price-cold'));
            let priceFrappe = parseFloat(element.getAttribute('data-price-frappe'));
            let qty = parseInt(document.getElementById('qty_' + cartId).value);
            
            let price;
            switch(priceType) {
                case 'hot':
                    price = priceHot;
                    break;
                case 'cold':
                    price = priceCold;
                    break;
                case 'frappe':
                    price = priceFrappe;
                    break;
                default:
                    price = priceHot;
            }
            
            grandTotal += price * qty;
        });
        
        document.getElementById('grand-total').textContent = grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท';
    }
    
    function updateNote(cartId) {
        let noteTextarea = document.getElementById('note_' + cartId);
        let note = noteTextarea.value;
        
        // ส่งข้อมูลไปอัปเดตฐานข้อมูล
        updateCartNote(cartId, note);
    }
    
    function updateCartInDatabase(cartId, qty) {
        fetch('update_cart_quantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'cart_id=' + cartId + '&qty=' + qty
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('อัปเดตจำนวนสินค้าสำเร็จ');
            } else {
                console.error('เกิดข้อผิดพลาดในการอัปเดต');
                alert('เกิดข้อผิดพลาดในการอัปเดตจำนวนสินค้า');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        });
    }
    
    function updateCartPriceType(cartId, priceType) {
        fetch('update_cart_price_type.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'cart_id=' + cartId + '&price_type=' + priceType
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('อัปเดตประเภทราคาสำเร็จ');
            } else {
                console.error('เกิดข้อผิดพลาดในการอัปเดตประเภทราคา');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function updateCartNote(cartId, note) {
        fetch('update_cart_note.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'cart_id=' + cartId + '&note=' + encodeURIComponent(note)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('อัปเดตข้อความเพิ่มเติมสำเร็จ');
            } else {
                console.error('เกิดข้อผิดพลาดในการอัปเดตข้อความเพิ่มเติม');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // คำนวณยอดรวมเริ่มต้นเมื่อโหลดหน้า
    document.addEventListener('DOMContentLoaded', function() {
        updateGrandTotal();
    });
    </script>
</body>
</html>
