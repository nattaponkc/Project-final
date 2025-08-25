<?php
require_once 'includes/session.php';
require_once 'config/condb.php';

// Debug ค่า Session
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- start product detail -->
<div class="container mt-3 ">
    <button type="button" class="btn btn-outline-success" onclick="window.location.href='menu.php';">กลับ</button>
    <h3 class="fw-bold mb-4 mt-3">รายละเอียดสินค้า</h3>
    <div class="row">

        <!-- รูปภาพหลัก -->
        <div class="col-12 col-sm-4 mb-3 text-center ">
            <a class="fancybox-buttons" data-fancybox-group="button" href="assets/product_img/<?= $rowProduct['product_image']; ?>">
                <img src="assets/product_img/<?= $rowProduct['product_image']; ?>"
                    class="main-product-img rounded shadow-sm">
            </a>
            <p class="mt-2 text-muted">รสชาติ<br>หวาน, หอม</p>

            <!-- แกลเลอรีรูปย่อ -->
            <div class="row mt-3">
                <?php foreach ($rsImg as $row) { ?>
                    <div class="col-6 col-sm-4 mb-2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="assets/product_gallery/<?= $row['product_image']; ?>">
                            <img src="assets/product_gallery/<?= $row['product_image']; ?>"
                                class="gallery-thumb rounded">
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- รายละเอียดสินค้า -->
        <div class="col-12 col-sm-8">
            <h4 class="fw-bold"><?= strtoupper($rowProduct['product_name']); ?></h4>

            <?php
            $can_order = isset($_SESSION['m_level']) && in_array($_SESSION['m_level'], ['member', 'staff', 'admin']);
            ?>




            <!-- ราคา -->
            <p>ร้อน
                <span class="text-danger fw-bold">
                    ราคา: <?= number_format($rowProduct['price_hot'], 2); ?> บาท
                </span>
                ปกติ <?= number_format($rowProduct['price_hot'] / 0.85, 0); ?> บาท
                <?php if ($can_order): ?>                    
                    <a href="add_to_cart.php?product_id=<?= $rowProduct['id'] ?>&type=hot&qty=1" class="btn btn-order">สั่งทำ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-order">สั่งทำ</a>
                <?php endif; ?>
            </p>

            <p>เย็น
                <span class="text-danger fw-bold">
                    ราคา: <?= number_format($rowProduct['price_cold'], 2); ?> บาท
                </span>
                ปกติ <?= number_format($rowProduct['price_cold'] / 0.85, 0); ?> บาท
                <?php if ($can_order): ?>
                    <a href="add_to_cart.php?product_id=<?= $rowProduct['id'] ?>&type=cold&qty=1" class="btn btn-order">สั่งทำ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-order">สั่งทำ</a>
                <?php endif; ?>
            </p>

            <p>ปั่น
                <span class="text-danger fw-bold">
                    ราคา: <?= number_format($rowProduct['price_frappe'], 2); ?> บาท
                </span>
                ปกติ <?= number_format($rowProduct['price_frappe'] / 0.85, 0); ?> บาท
                <?php if ($can_order): ?>
                    <a href="add_to_cart.php?product_id=<?= $rowProduct['id'] ?>&type=frappe&qty=1" class="btn btn-order">สั่งทำ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-order">สั่งทำ</a>
                <?php endif; ?>
            </p>

            <!-- คำอธิบาย -->
            <p class="mt-3"><?= nl2br($rowProduct['product_detail']); ?></p>
            <small class="text-muted">จำนวนการเข้าชม <?= $rowProduct['product_view']; ?> ครั้ง</small>



            <?php
            // ดึงข้อมูลคะแนนเฉลี่ยและจำนวนรีวิวจากฐานข้อมูล
            $sql_reviews = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
                FROM tbl_reviews 
                WHERE product_id = :product_id";
            $stmt_reviews = $condb->prepare($sql_reviews);
            $stmt_reviews->execute(['product_id' => $rowProduct['id']]);
            $review_data = $stmt_reviews->fetch(PDO::FETCH_ASSOC);


            // Debug ข้อมูลรีวิว
            // echo '<pre>';
            // print_r($review_data);
            // echo '</pre>';

            $avg_rating = $review_data['avg_rating'] ? number_format($review_data['avg_rating'], 1) : 0.0; // คะแนนเฉลี่ย
            $total_reviews = $review_data['total_reviews'] ?? 0; // จำนวนรีวิว
            ?>

            <!-- แสดงคะแนนรีวิว -->
            <div class="rating mb-3 d-flex align-items-center">
                <span class="text-warning me-2" style="font-size: 1.2rem;">
                    <?php if ($total_reviews > 0): ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $avg_rating ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                        <?php endfor; ?>
                    <?php else: ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="far fa-star"></i> <!-- ดาวว่าง -->
                        <?php endfor; ?>
                    <?php endif; ?>
                </span>
                <span class="text-muted"><?= $avg_rating ?> (<?= number_format($total_reviews) ?> reviews)</span>
            </div>



            <!-- ฟอร์มแสดงความคิดเห็น -->
            <form method="POST" action="" class="review-section mt-4">
                <h5 class="fw-bold">แสดงความคิดเห็น</h5>
                <input type="hidden" name="product_id" value="<?= $rowProduct['id']; ?>">
                <div class="mb-3">
                    <label for="rating" class="form-label">ให้คะแนน (1-5 ดาว)</label>
                    <select class="form-select" id="rating" name="rating" required>
                        <option value="5">⭐⭐⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="1">⭐</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">ความคิดเห็น</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="เขียนความคิดเห็นของคุณ..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ส่งความคิดเห็น</button>
            </form>




            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                // Debug ข้อมูลที่ส่งมาจากฟอร์ม
                // echo '<pre>';
                // print_r($_POST);
                // echo '</pre>';

                // รับข้อมูลจากฟอร์ม
                $product_id = $_POST['product_id'];
                $rating = $_POST['rating'];
                $comment = $_POST['comment'];

                // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
                if (isset($_SESSION['staff_id'])) {
                    $member_id = $_SESSION['staff_id']; // ใช้ staff_id แทน member_id

                    // เพิ่มความคิดเห็นลงในฐานข้อมูล
                    $sql_insert = "INSERT INTO tbl_reviews (product_id, member_id, rating, comment, created_at) 
                       VALUES (:product_id, :member_id, :rating, :comment, NOW())";
                    $stmt_insert = $condb->prepare($sql_insert);
                    $stmt_insert->execute([
                        'product_id' => $product_id,
                        'member_id' => $member_id,
                        'rating' => $rating,
                        'comment' => $comment,
                    ]);

                    echo '<script>
            Swal.fire({
                icon: "success",
                title: "ความคิดเห็นถูกบันทึกเรียบร้อยแล้ว",
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                 window.location.href = "detail.php?id=' . $product_id . '&view=show-product-detail";
            });
        </script>';
                } else {
                    echo '<script>
            Swal.fire({
                icon: "error",
                title: "กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น",
                showConfirmButton: false,
                timer: 1500
            });
        </script>';
                }
            }
            ?>


            <!-- แสดงความคิดเห็น -->
            <div class="existing-reviews mt-4">
                <h5 class="fw-bold">ความคิดเห็นจากผู้ใช้</h5>
                <?php
                $sql_comments = "SELECT r.review_id, r.rating, r.comment, r.created_at, m.name AS member_name
                 FROM tbl_reviews r
                 JOIN tbl_member m ON r.member_id = m.id
                 WHERE r.product_id = :product_id
                 ORDER BY r.created_at DESC";
                $stmt_comments = $condb->prepare($sql_comments);
                $stmt_comments->execute(['product_id' => $rowProduct['id']]);
                $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

                if (empty($comments)): ?>
                    <p class="text-muted">ยังไม่มีความคิดเห็น</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="review-item mb-3">
                            <p><strong><?= htmlspecialchars($comment['member_name']); ?></strong></p>
                            <p>คะแนน:
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= $comment['rating'] ? 'fas fa-star text-warning' : 'far fa-star text-warning'; ?>"></i>
                                <?php endfor; ?>
                            </p>
                            <p><?= htmlspecialchars($comment['comment']); ?></p>
                            <p class="text-muted"><?= date('d/m/Y H:i', strtotime($comment['created_at'])); ?></p>

                            <?php if (isset($_SESSION['m_level']) && $_SESSION['m_level'] === 'admin'): ?>
                                <!-- ฟอร์มลบความคิดเห็น -->
                                <form method="GET" action="delete_review.php" class="mt-2" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="review_id" value="<?= $comment['review_id']; ?>">
                                    <input type="hidden" name="act" value="delete">
                                     <input type="hidden" name="product_id" value="<?= $rowProduct['id']; ?>"> <!-- เพิ่มตรงนี้ -->
                                    <button type="submit" class="btn btn-danger btn-sm">ลบความคิดเห็น</button>
                                </form>
                                <script>
                                    function confirmDelete() {
                                        return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบความคิดเห็นนี้?');
                                    }
                                </script>                                
                            <?php endif; ?>
                            
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>


        </div>



    </div>
</div>

<!-- CSS -->
<style>
    .main-product-img {
        width: 100%;
        height: 300px;
        /* กำหนดความสูงคงที่ */
        object-fit: cover;
        /* ให้ภาพไม่บิดเบี้ยว */
    }

    .gallery-thumb {
        width: 100%;
        height: 80px;
        object-fit: cover;
    }

    .btn-order {
        background-color: #c6a15b;
        color: white;
        font-weight: bold;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .btn-order:hover {
        background-color: #b18d4d;
        color: white;
    }

    .rating {
        display: flex;
        align-items: center;
    }

    .rating .text-warning {
        font-size: 1.2rem;
    }

    .rating .text-muted {
        margin-left: 8px;
        font-size: 0.9rem;
    }
</style>
<!-- end product detail -->