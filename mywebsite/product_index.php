 <?php 
 //เชื่อมต่อฐานข้อมูล
 require_once('config/condb.php');

 //คิวรี่ข้อมูลสินค้ามาแสดงหน้าแรก
$queryproduct = $condb->prepare ("SELECT * FROM tbl_product ORDER BY id DESC ");
$queryproduct->execute();
$rsproduct = $queryproduct->fetchAll();

 ?>
 
 <!-- Popular Products Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">รายการสินค้า</h2>
            <div class="row g-4">
                <?php foreach ($rsproduct as $product): ?>
                <div class="col-md-4 col-lg-2">
                    <div class="card product-card h-100">
                        <img src="assets/product_img/<?php echo htmlspecialchars($product['product_image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                            <p class="card-text">
                                <small class="text-muted">
                                   <span class="badge bg-primary price-badge">
                                            <?php
                                            $prices = array_filter([$product['price_hot'], $product['price_cold'], $product['price_frappe']], fn($p) => $p > 0);
                                            echo 'เริ่มต้น ' . (!empty($prices) ? number_format(min($prices)) . ' บาท' : 'ราคาไม่ระบุ');
                                            ?>
                                        </span>
                                </small>
                            </p>
                            <a href="menu.php" class="btn btn-sm btn-primary">ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

 
 <!-- start product -->
   
<style>
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
     <!-- end product -->