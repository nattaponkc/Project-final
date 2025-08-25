<?php
session_start();
require_once('config/condb.php');

// ดึงประเภทสินค้า
$sql_types = "SELECT * FROM tbl_type ORDER BY type_name";
$stmt_types = $condb->prepare($sql_types);
$stmt_types->execute();
$types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

// ฟิลเตอร์ตามประเภท
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';



// สร้าง SQL query
$sql_products = "SELECT p.*, t.type_name 
                 FROM tbl_product p 
                 JOIN tbl_type t ON p.ref_type_id = t.type_id 
                 WHERE 1=1";

$params = [];

if (!empty($type_filter)) {
    $sql_products .= " AND p.ref_type_id = :type_id";
    $params['type_id'] = $type_filter;
}

if (!empty($search)) {
    $sql_products .= " AND (p.product_name LIKE :search OR p.product_detail LIKE :search)";
    $params['search'] = "%$search%";
}

$sql_products .= " ORDER BY t.type_name, p.product_name";
$stmt_products = $condb->prepare($sql_products);
$stmt_products->execute($params);
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนู - สบายโฮมสเตย์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-3px);
        }

        .price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }

        .btn-primary {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }

        .btn-primary:hover {
            background-color: #34495e;
            border-color: #34495e;
        }

        .filter-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-home me-2"></i>AddMore Cafe'
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="menu.php">เมนู</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">ห้องพัก</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['staff_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo $_SESSION['m_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?php
                                                                    if ($_SESSION['m_level'] === 'admin') {
                                                                        echo '/mywebsite/admin/index.php';
                                                                    } elseif ($_SESSION['m_level'] === 'staff') {
                                                                        echo '/mywebsite/staff/index.php';
                                                                    } elseif ($_SESSION['m_level'] === 'member') {
                                                                        echo '/mywebsite/member/index.php';
                                                                    } else {
                                                                        echo '#';
                                                                    }
                                                                    ?>">
                                        โปรไฟล์
                                    </a>
                                </li>
                                <li><a class="dropdown-item" href="member/order.php">คำสั่งซื้อของฉัน</a></li>
                                <li><a class="dropdown-item" href="member/booking.php">การจองของฉัน</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <?php
                            // ตรวจสอบว่ามี Session การเข้าสู่ระบบหรือไม่
                            if (isset($_SESSION['staff_id'])) {
                                $member_id = $_SESSION['staff_id'];

                                // คิวรี่เพื่อดึงจำนวนสินค้าทั้งหมดในตะกร้าของสมาชิกคนนั้น
                                $sql_cart_count = "SELECT SUM(qty) AS total_items FROM tbl_cart WHERE member_id = :member_id AND is_active = 1";
                                $stmt_cart_count = $condb->prepare($sql_cart_count);
                                $stmt_cart_count->execute(['member_id' => $member_id]);
                                $result_cart_count = $stmt_cart_count->fetch(PDO::FETCH_ASSOC);

                                $cart_count = $result_cart_count['total_items'] ?? 0;
                            } else {
                                $cart_count = 0;
                            }
                            ?>
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">สมัครสมาชิก</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="text-white py-5" style="background-image: url('assets/banner/11.jpg'); background-size: cover; background-position: center;">
        <div class="container">
            <h1 class="text-center mb-3">
                <i class="fas fa-coffee me-2"></i>เมนูของเรา
            </h1>
            <p class="text-center lead">เลือกเมนูที่คุณชื่นชอบ</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="container mt-4">
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">ค้นหาเมนู</label>
                    <input type="text" class="form-control" id="search" name="search"
                        value="<?php echo htmlspecialchars($search); ?>" placeholder="ชื่อเมนู...">
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">ประเภท</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">ทุกประเภท</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type['type_id']; ?>"
                                <?php echo $type_filter == $type['type_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>ค้นหา
                    </button>
                    <a href="menu.php" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i>ล้าง
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container mt-4">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>ไม่พบเมนูที่ค้นหา</h4>
                <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือประเภท</p>
            </div>
        <?php else: ?>

            <!-- Group by Type -->
            <?php
            $grouped_products = [];
            foreach ($products as $product) {
                $grouped_products[$product['type_name']][] = $product;
            }
            ?>

            <?php foreach ($grouped_products as $type_name => $type_products): ?>
                <div class="mb-5">
                    <h3 class="mb-4">
                        <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($type_name); ?>
                    </h3>
                    <div class="row g-4">
                        <?php foreach ($type_products as $product): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card product-card">
                                    <div class="position-relative">
                                        <img src="assets/product_img/<?php echo htmlspecialchars($product['product_image']); ?>"
                                            class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>"

                                            style="height: 200px; object-fit: cover;">
                                        <span class="badge bg-primary price-badge">
                                            <?php
                                            $prices = array_filter([$product['price_hot'], $product['price_cold'], $product['price_frappe']], fn($p) => $p > 0);
                                            echo 'เริ่มต้น ' . (!empty($prices) ? number_format(min($prices)) . ' บาท' : 'ราคาไม่ระบุ');
                                            ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                        <p class="card-text text-muted">
                                            <?php echo htmlspecialchars(substr(strip_tags($product['product_detail']), 0, 100)) . '...'; ?>
                                        </p>

                                        <!-- Price Table -->
                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm">
                                                <tbody>
                                                    <?php if ($product['price_hot'] > 0): ?>
                                                        <tr>
                                                            <td><i class="fas fa-fire text-danger me-1"></i>ร้อน</td>
                                                            <td class="text-end"><?php echo number_format($product['price_hot']); ?> บาท</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php if ($product['price_cold'] > 0): ?>
                                                        <tr>
                                                            <td><i class="fas fa-snowflake text-info me-1"></i>เย็น</td>
                                                            <td class="text-end"><?php echo number_format($product['price_cold']); ?> บาท</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php if ($product['price_frappe'] > 0): ?>
                                                        <tr>
                                                            <td><i class="fas fa-blender text-success me-1"></i>ปั่น</td>
                                                            <td class="text-end"><?php echo number_format($product['price_frappe']); ?> บาท</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>

                                            </table>

                                            <!-- เพิ่มคะแนนดาว -->
                                            <div class="rating mb-3 d-flex text-center">
                                                <?php
                                                // ดึงข้อมูลคะแนนเฉลี่ยและจำนวนรีวิวจากฐานข้อมูล
                                                $sql_reviews = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
                                                FROM tbl_reviews 
                                                WHERE product_id = :product_id";
                                                $stmt_reviews = $condb->prepare($sql_reviews);
                                                $stmt_reviews->execute(['product_id' => $product['id']]);
                                                $review_data = $stmt_reviews->fetch(PDO::FETCH_ASSOC);

                                                $avg_rating = $review_data['avg_rating'] ? number_format($review_data['avg_rating'], 1) : 0.0; // คะแนนเฉลี่ย
                                                $total_reviews = $review_data['total_reviews'] ?? 0; // จำนวนรีวิว
                                                ?>
                                                <a href="detail.php?id=<?= $product['id']; ?>&ชื่อสินค้า=<?= urlencode($product['product_name']); ?>&ราคา_r=<?= $product['price_hot']; ?>&ราคา_c=<?= $product['price_cold']; ?>&ราคา_f=<?= $product['price_frappe']; ?>&view=show-product-detail" class="text-decoration-none">
                                                    <span class="text-warning" style="font-size: 1.2rem;">
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
                                                </a>
                                            </div>


                                        </div>




                                        <a href="detail.php?id=<?= $product['id']; ?>&ชื่อสินค้า=<?= urlencode($product['product_name']); ?>&ราคา_r=<?= $product['price_hot']; ?>&ราคา_c=<?= $product['price_cold']; ?>&ราคา_f=<?= $product['price_frappe']; ?>
                                        &view=show-product-detail" class="fas fa-image text-muted text-decoration-none" style="font-size: 1.3rem; margin-right: 0.5rem;"> </a>

                                        <?php if (isset($_SESSION['staff_id'])): ?>
                                            <button class="btn btn-primary w-100"
                                                onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')">
                                                <i class="fas fa-cart-plus me-1"></i>เพิ่มลงตะกร้า
                                            </button>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-sign-in-alt me-1"></i>เข้าสู่ระบบเพื่อสั่งซื้อ
                                            </a>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>สบายโฮมสเตย์</h5>
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
                <p>&copy; 2025 สบายโฮมสเตย์. สงวนลิขสิทธิ์.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId, productName) {
            // เปิด modal สำหรับเลือกประเภทและจำนวน
            const modal = `
                <div class="modal fade" id="addToCartModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">เพิ่มลงตะกร้า: ${productName}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addToCartForm">
                                    <input type="hidden" name="product_id" value="${productId}">
                                    <div class="mb-3">
                                        <label class="form-label">ประเภท</label>
                                        <select class="form-select" name="price_type" required>
                                            <option value="hot">ร้อน</option>
                                            <option value="cold">เย็น</option>
                                            <option value="frappe">ปั่น</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">จำนวน</label>
                                        <input type="number" class="form-control" name="qty" value="1" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">เพิ่มเติม (ไม่บังคับ)</label>
                                        <textarea class="form-control" name="note" rows="2" placeholder="เช่น ไม่หวาน, เพิ่มช็อต, ฯลฯ"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="button" class="btn btn-primary" onclick="submitAddToCart()">เพิ่มลงตะกร้า</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // ลบ modal เก่าถ้ามี
            const oldModal = document.getElementById('addToCartModal');
            if (oldModal) {
                oldModal.remove();
            }

            // เพิ่ม modal ใหม่
            document.body.insertAdjacentHTML('beforeend', modal);

            // แสดง modal
            const modalElement = new bootstrap.Modal(document.getElementById('addToCartModal'));
            modalElement.show();
        }

        function submitAddToCart() {
            const form = document.getElementById('addToCartForm');
            const formData = new FormData(form);

            fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('เพิ่มลงตะกร้าเรียบร้อยแล้ว!');
                        // ปิด modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addToCartModal'));
                        modal.hide();
                        // รีเฟรชหน้าเพื่ออัปเดตจำนวนในตะกร้า
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                });
        }
    </script>
</body>

</html>