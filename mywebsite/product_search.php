<?php 
if(isset($_GET['query'])){
    //คิวรี่ข้อมูลค้นหาสินค้า
    $queryproduct = $condb->prepare ("SELECT * FROM tbl_product WHERE `product_name` LIKE :query ORDER BY id DESC");
    // bindParam
    $queryproduct->bindValue(':query', '%'.$_GET['query'].'%', PDO::PARAM_STR);
    $queryproduct->execute();
    $rsproduct = $queryproduct->fetchAll();
} else {
    $rsproduct = [];
}
?>

<!-- start product -->
<div class="container mt-1">
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info" role="alert">
                <p style="font-size: 20pt;"> รายการสินค้าที่ค้นหา : <?= htmlspecialchars($_GET['query'] ?? ''); ?> </p>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach($rsproduct as $row){ 
            //ตัดช่องว่างและแทนที่ด้วย -
            $productName = str_replace(' ','-',$row['product_name']);
        ?>
        <div class="col-12 col-sm-3 mb-2">
            <div class="card" style="width: 100%;">
                <img src="assets/product_img/<?= htmlspecialchars($row['product_image']); ?>" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['product_name']); ?></h5>
                    <p class="card-text">ราคา <?= number_format($row['price_hot'],2); ?> บาท</p>
                    <a href="detail.php?id=<?= $row['id']; ?>&ชื่อสินค้า=<?= $productName; ?>&ราคา=<?= number_format($row['price_hot'],2); ?>-บาท&view=show-product-detail" class="btn btn-primary">รายละเอียด</a>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php
        //สร้างเงื่อนไขตรวจสอบการคิวรี่
        if(empty($rsproduct)){ 
            echo '<h4 class="text-center"> ไม่พบสินค้าที่ค้นหา </h4>';
        }
        ?>
    </div>
</div>
<!-- end product -->