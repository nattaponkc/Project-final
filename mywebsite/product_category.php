 <?php 
 if(isset($_GET['id'])){
            //คิวรี่ข้อมูลสินค้ามาแสดงตามหมวดหมู่
            $queryproduct = $condb->prepare ("SELECT * FROM tbl_product WHERE ref_type_id=:id ORDER BY id DESC ");
            // bindParam
             $queryproduct->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $queryproduct->execute();
            $rsproduct = $queryproduct->fetchAll();
 }else{
    exit;
 }

 ?>
 <!-- start product -->
     <div class="container mt-1 ">
        <div class="row"> 
            <h5><?=$_GET['cat'];?></h5>
        </div>
        <div class="row">

        <?php foreach($rsproduct as $row){ 
            //ตัดช่องว่างและแทนที่ด้วย -
            $productName = str_replace(' ','-',$row['product_name']);
            // echo $productName;
            ?>
            <div class="col-12 col-sm-3 mb-2">
                <div class="card" style="width: 100%;">
                    <img src="assets/product_img/<?=$row['product_image'];?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"><?=$row['product_name'];?></h5>
                        <p class="card-text">
                            ราคา ร้อน: <?= number_format($row['price_hot'], 2); ?> บาท<br>
                            ราคา เย็น: <?= number_format($row['price_cold'], 2); ?> บาท<br>
                            ราคา ปั่น: <?= number_format($row['price_frappe'], 2); ?> บาท
                        </p>
                        <a href="detail.php?id=<?= $row['id']; ?>&ชื่อสินค้า=<?= urlencode($productName); ?>&ราคา=ร้อน<?= $row['price_hot']; ?>-เย็น<?= $row['price_cold']; ?>-ปั่น<?= $row['price_frappe']; ?>บาท&view=show-product-detail" class="btn btn-primary">รายละเอียด</a>
                    </div>
                </div>
            </div>
            <?php } ?>

<?php
    //สร้างเงื่อนไขตรวจสอบการคิวรี่
   if($queryproduct->rowCount() == 0){ //คิวรี่ผิดพลาด
    echo '<h4 class="text-center"> ไม่มีสินค้าในหมวดหมู่ดังกล่าว </h4>';
   }
?>
        </div>
      </div>

     <!-- end product -->