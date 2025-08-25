<?php 
require_once 'includes/session.php'; 
require_once 'config/condb.php';

// Fetch room details
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$sqlRoom = "SELECT * FROM tbl_room WHERE room_id = :room_id";
$stmtRoom = $condb->prepare($sqlRoom);
$stmtRoom->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$stmtRoom->execute();
$rowRoom = $stmtRoom->fetch(PDO::FETCH_ASSOC);

// Fetch room gallery images
$sqlGallery = "SELECT * FROM tbl_room_image WHERE ref_room_id = :room_id";
$stmtGallery = $condb->prepare($sqlGallery);
$stmtGallery->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$stmtGallery->execute();
$rsImg = $stmtGallery->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- start room detail -->
<div class="container mt-4">
    <button onclick="window.location.href='rooms.php';" class="btn btn-secondary mt-2">กลับ</button>
    <h3 class="fw-bold mb-4 mt-4">รายละเอียดห้องพัก</h3>
    <div class="row">

        <!-- Main Image -->
        <div class="col-12 col-sm-4 mb-3 text-center">
            <a class="fancybox-buttons" data-fancybox-group="button" href="assets/room_img/<?= $rowRoom['room_image']; ?>">
                <img src="assets/room_img/<?= $rowRoom['room_image']; ?>"
                    class="main-product-img rounded shadow-sm">
            </a>

            <!-- Gallery Thumbnails -->
            <div class="row mt-3">
                <?php foreach ($rsImg as $row) { ?>
                    <div class="col-6 col-sm-4 mb-2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="assets/room_gallery/<?= $row['room_image']; ?>">
                            <img src="assets/room_gallery/<?= $row['room_image']; ?>"
                                class="gallery-thumb rounded">
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Room Details -->
        <div class="col-12 col-sm-8">
            <h4 class="fw-bold"><?= htmlspecialchars($rowRoom['room_name']); ?></h4>

            <!-- Room Amenities -->
            <p class="mt-3">
                <strong>สิ่งอำนวยความสะดวก:</strong><br>
                <?php if ($rowRoom['has_tv']): ?>
                    <i class="fas fa-tv amenity-icon"></i> TV<br>
                <?php endif; ?>
                <?php if ($rowRoom['air_or_fan'] == 'Air'): ?>
                    <i class="fas fa-snowflake amenity-icon"></i> แอร์<br>
                <?php elseif ($rowRoom['air_or_fan'] == 'Fan'): ?>
                    <i class="fas fa-fan amenity-icon"></i> พัดลม<br>
                <?php endif; ?>
                <?php if ($rowRoom['has_bathroom']): ?>
                    <i class="fas fa-bath amenity-icon"></i> ห้องน้ำในตัว<br>
                <?php endif; ?>
                <i class="fas fa-wifi amenity-icon"></i> Wi-Fi<br>
                <i class="fas fa-smoking-ban amenity-icon"></i> ห้ามสูบบุหรี่
            </p>

            <!-- Room Price -->
            <p class="mt-3">
                <strong>ราคา:</strong>
                <span class="text-danger fw-bold">
                    <?= number_format($rowRoom['room_price'], 2); ?> บาท/คืน
                </span>
            </p>

            <!-- Room Description -->
            <p class="mt-3">
                <strong>รายละเอียด:</strong><br>
                <?= nl2br(htmlspecialchars($rowRoom['room_detail'])); ?>
            </p>

            <!-- Booking Button -->
            <?php if ($rowRoom['room_status'] === 'ว่าง'): ?>
                <a href="book_room.php?room_id=<?= $rowRoom['room_id']; ?>" class="btn btn-primary">
                    <i class="fas fa-calendar-plus me-1"></i> จองเลย
                </a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>
                    <i class="fas fa-times me-1"></i> ไม่ว่าง
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    .main-product-img {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }

    .gallery-thumb {
        width: 100%;
        height: 80px;
        object-fit: cover;
    }

    .btn-primary {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }

    .btn-primary:hover {
        background-color: #34495e;
        border-color: #34495e;
    }
</style>
<!-- end room detail -->
