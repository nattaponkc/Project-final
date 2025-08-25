<?php 
session_start();


 
require_once('config/condb.php');

include 'header.php';
include 'menu_top.php';
include 'slide.php';



// ดึงข่าวสารล่าสุด
$sql_announcement = "SELECT * FROM tbl_announcement WHERE is_active = 1 AND (end_date IS NULL OR end_date >= NOW()) ORDER BY created_at DESC LIMIT 3";
$stmt_announcement = $condb->prepare($sql_announcement);
$stmt_announcement->execute();
$announcements = $stmt_announcement->fetchAll(PDO::FETCH_ASSOC);


// ดึงสินค้าขายดี
$sql_top_products = "SELECT p.*, COUNT(oi.id) as order_count 
                     FROM tbl_product p 
                     LEFT JOIN tbl_order_item oi ON p.id = oi.product_id 
                     LEFT JOIN tbl_order o ON oi.order_id = o.order_id 
                     WHERE o.status != 'ยกเลิก' OR o.status IS NULL 
                     GROUP BY p.id 
                     ORDER BY order_count DESC 
                     LIMIT 6";
$stmt_top_products = $condb->prepare($sql_top_products);
$stmt_top_products->execute();
$top_products = $stmt_top_products->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายการห้องทั้งหมด
$sql_rooms = "SELECT * FROM tbl_room ORDER BY room_price ASC";
$stmt_rooms = $condb->prepare($sql_rooms);
$stmt_rooms->execute();
$rooms = $stmt_rooms->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add More Cafe' - ร้านคาเฟ่และห้องพักรายวัน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/product_img/123.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            color: white;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .product-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-3px);
        }
        .room-card {
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .announcement-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
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
</head>


    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">ยินดีต้อนรับสู่ Add More Cafe' & Sbay Home Stay</h1>
            <p class="lead mb-4">ร้านคาเฟ่และห้องพักรายวันที่ให้บริการด้วยความอบอุ่น</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="menu.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-coffee me-2"></i>ดูเมนู
                </a>
                <a href="rooms.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-bed me-2"></i>จองห้องพัก
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">บริการของเรา</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-coffee fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">ร้านคาเฟ่</h5>
                            <p class="card-text">กาแฟและเครื่องดื่มคุณภาพ พร้อมขนมหวานแสนอร่อย</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-bed fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">ห้องพักรายวัน</h5>
                            <p class="card-text">ห้องพักสะอาด สะดวกสบาย พร้อมสิ่งอำนวยความสะดวกครบครัน</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-wifi fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Wi-Fi ฟรี</h5>
                            <p class="card-text">อินเทอร์เน็ตความเร็วสูง ใช้งานได้ตลอด 24 ชั่วโมง</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcements Section -->
    <?php if (!empty($announcements)): ?>
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">ข่าวสารและโปรโมชั่น</h2>
            <div class="row g-4">
                <?php foreach ($announcements as $announcement): ?>
                <div class="col-md-4">
                    <div class="card h-100 position-relative">
                        <div class="card-body">
                            <span class="badge bg-<?php echo $announcement['announcement_type'] == 'โปรโมชั่น' ? 'danger' : 'primary'; ?> announcement-badge">
                                <?php echo $announcement['announcement_type']; ?>
                            </span>
                            <h5 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($announcement['content'], 0, 100)) . '...'; ?></p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y', strtotime($announcement['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
<?php   include 'product_index.php'; ?>
    
    <!-- Available Rooms Section -->
    <section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">รายการห้องพัก</h2>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card room-card h-100">
                     <img src="assets/room_img/<?php echo htmlspecialchars($room['room_image']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($room['room_detail']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold"><?php echo number_format($room['room_price']); ?> บาท/คืน</span>
                            <a href="rooms.php" class="btn btn-primary btn-sm">ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


    <!-- About Section -->
    <section class="py-5 bg-light" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>เกี่ยวกับ Add More Cafe'</h2>
                    <p class="lead">เราเป็นร้านคาเฟ่และห้องพักรายวันที่ให้บริการด้วยความอบอุ่นและเป็นกันเอง</p>
                    <p>เปิดให้บริการทุกวัน ตั้งแต่ 07:00 - 22:00 น.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>กาแฟคุณภาพสูง</li>
                        <li><i class="fas fa-check text-success me-2"></i>ห้องพักสะอาด สะดวกสบาย</li>
                        <li><i class="fas fa-check text-success me-2"></i>Wi-Fi ฟรี</li>
                        <li><i class="fas fa-check text-success me-2"></i>ที่จอดรถฟรี</li>
                    </ul>
                </div>
                <div class="col-md-6 mt-3">
                    <img src="assets/about/a1.jpg" class="img-fluid rounded" alt="เกี่ยวกับเรา" style="width:500px; height:400px; object-fit:cover;">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contact">
        <div class="container">
            <h2 class="text-center mb-5">ติดต่อเรา</h2>
            <div class="row">
                <div class="col-md-6">
                    <h5>ข้อมูลติดต่อ</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i>277 หมู่ 16 ต.แม่กา อ.เมือง จ.พะเยา, Phayao, Thailand </p>
                    <p><i class="fas fa-phone me-2"></i>089-852-2599</p>
                    <p><i class="fas fa-envelope me-2"></i>info@sabaistay.com</p>
                    <h5 class="mt-4">เวลาทำการ</h5>
                    <p>ทุกวัน 10:00 - 18:00 น.</p>
                </div>
                <div class="col-md-6">
                    <h5>ส่งข้อความถึงเรา</h5>
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="ชื่อ-นามสกุล">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="อีเมล">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="ข้อความ"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Add More Cafe'</h5>
                    <p>ร้านคาเฟ่และห้องพักรายวันที่ให้บริการด้วยความอบอุ่น</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>ติดตามเรา</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-line fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 Add More Cafe'. สงวนลิขสิทธิ์.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>