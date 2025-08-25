<?php
session_start();
require_once('config/condb.php');


// ดึงห้องพักทั้งหมด
$sql_rooms = "SELECT * FROM tbl_room ORDER BY room_price ASC";
$stmt_rooms = $condb->prepare($sql_rooms);
$stmt_rooms->execute();
$rooms = $stmt_rooms->fetchAll(PDO::FETCH_ASSOC);

// ฟิลเตอร์
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$price_min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
$price_max = isset($_GET['price_max']) ? $_GET['price_max'] : '';

// กรองข้อมูล
$filtered_rooms = $rooms;
if (!empty($status_filter)) {
    $filtered_rooms = array_filter($filtered_rooms, function ($room) use ($status_filter) {
        return $room['status'] === $status_filter;
    });
}

if (!empty($price_min)) {
    $filtered_rooms = array_filter($filtered_rooms, function ($room) use ($price_min) {
        return $room['room_price'] >= $price_min;
    });
}

if (!empty($price_max)) {
    $filtered_rooms = array_filter($filtered_rooms, function ($room) use ($price_max) {
        return $room['room_price'] <= $price_max;
    });
}

// Update room availability based on selected date
if (isset($_GET['check_date'])) {
    $check_date = $_GET['check_date'];

    // Query to check room status based on tbl_booking
    $sql_check_status = "
        SELECT room_id, status 
        FROM tbl_booking 
        WHERE :check_date BETWEEN check_in_date AND check_out_date 
        AND status IN ('pending', 'confirmed', 'checked_in', 'รอดำเนินการ')
    ";
    $stmt_check_status = $condb->prepare($sql_check_status);
    $stmt_check_status->execute([':check_date' => $check_date]);
    $booked_rooms = $stmt_check_status->fetchAll(PDO::FETCH_ASSOC);

    foreach ($filtered_rooms as &$room) {
        $room['status'] = 'ว่าง'; // Default status

        foreach ($booked_rooms as $booked_room) {
            if ($room['room_id'] == $booked_room['room_id']) {
                $room['status'] = 'ไม่ว่าง'; // Set status to "ไม่ว่าง" for booked rooms
                break;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ห้องพัก - สบายโฮมสเตย์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .room-card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .room-card:hover {
            transform: translateY(-5px);
        }

        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
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

        .amenity-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-home me-2"></i>สบายโฮมสเตย์
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
                        <a class="nav-link" href="menu.php">เมนู</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="rooms.php">ห้องพัก</a>
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
                <i class="fas fa-bed me-2"></i>ห้องพักของเรา
            </h1>
            <p class="text-center lead">ห้องพักสะอาด สะดวกสบาย พร้อมสิ่งอำนวยความสะดวกครบครัน</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="container mt-4">
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">สถานะห้อง</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">ทุกสถานะ</option>
                        <option value="ว่าง" <?php echo $status_filter === 'ว่าง' ? 'selected' : ''; ?>>ว่าง</option>
                        <option value="ไม่ว่าง" <?php echo $status_filter === 'ไม่ว่าง' ? 'selected' : ''; ?>>ไม่ว่าง</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="price_min" class="form-label">ราคาต่ำสุด</label>
                    <input type="number" class="form-control" id="price_min" name="price_min"
                        value="<?php echo htmlspecialchars($price_min); ?>" placeholder="0">
                </div>
                <div class="col-md-3">
                    <label for="price_max" class="form-label">ราคาสูงสุด</label>
                    <input type="number" class="form-control" id="price_max" name="price_max"
                        value="<?php echo htmlspecialchars($price_max); ?>" placeholder="10000">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>ค้นหา
                    </button>
                    <a href="rooms.php" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-1"></i>ล้าง
                    </a>
                </div>

                <!-- Add date selection form -->
                <div class="container mt-2">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4 mt-2">
                            <label for="check_date" class="form-label">เลือกวันที่</label>
                            <input type="date" class="form-control" id="check_date" name="check_date"
                                value="<?php echo isset($_GET['check_date']) ? htmlspecialchars($_GET['check_date']) : ''; ?>"
                                style="width: 200px;">
                        </div>
                        <div class="col-md-4 d-flex align-items-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>ตรวจสอบ
                            </button>
                        </div>
                    </form>
                </div>



            </form>
        </div>
    </div>



    <!-- Rooms Section -->
    <div class="container mt-4">
        <?php if (empty($filtered_rooms)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>ไม่พบห้องพักที่ค้นหา</h4>
                <p class="text-muted">ลองเปลี่ยนเงื่อนไขการค้นหา</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($filtered_rooms as $room): ?>
                    <?php
                    // กำหนดค่าเริ่มต้นให้ status หากไม่มีการกำหนดค่า
                    $room_status = isset($room['status']) ? $room['status'] : 'กรุณาเลือกวันที่';
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card room-card">
                            <div class="position-relative">
                                <img src="assets/room_img/<?php echo htmlspecialchars($room['room_image']); ?>"
                                    class="card-img-top" alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                                    style="height: 250px; object-fit: cover;">

                                <!-- ป้ายสถานะห้องพัก -->
                                <span class="badge bg-<?php echo $room_status === 'ว่าง' ? 'success' : 'danger'; ?> status-badge">
                                    <?php echo htmlspecialchars($room_status); ?>
                                </span>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars(strip_tags($room['room_detail'])); ?></p>

                                <!-- ข้อมูลห้อง -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-users amenity-icon"></i>
                                            สูงสุด <?php echo $room['max_guests']; ?> คน
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-thermometer-half amenity-icon"></i>
                                            <?php echo $room['season_type'] === 'low' ? 'Low Season' : 'High Season'; ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- สิ่งอำนวยความสะดวก -->
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <a href="detailrooms.php?room_id=<?= $room['room_id']; ?>" class="fas fa-image text-muted text-decoration-none" style="font-size: 1.3rem; margin-right: 0.5rem;"></a>
                                        <i class="fas fa-wifi amenity-icon"></i>Wi-Fi

                                        <?php if ($room['has_tv'] == 1): ?>
                                            <i class="fas fa-tv amenity-icon"></i>TV
                                        <?php endif; ?>
                                        <?php if ($room['air_or_fan'] == 'Air'): ?>
                                            <i class="fas fa-snowflake amenity-icon"></i>แอร์
                                        <?php elseif ($room['air_or_fan'] == 'Fan'): ?>
                                            <i class="fas fa-fan amenity-icon"></i>พัดลม
                                        <?php endif; ?>
                                        <?php if ($room['has_bathroom'] == 1): ?>
                                            <i class="fas fa-bath amenity-icon"></i>ห้องน้ำในตัว
                                        <?php endif; ?>
                                    </small>
                                </div>

                                <!-- ราคาและปุ่มจอง -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-primary fw-bold fs-5"><?php echo number_format($room['room_price']); ?> บาท</span>
                                        <small class="text-muted d-block">ต่อคืน</small>
                                    </div>

                                    <?php if ($room_status === 'ว่าง'): ?>
                                        <button class="btn btn-primary"
                                            onclick="bookRoom(<?php echo $room['room_id']; ?>, '<?php echo htmlspecialchars($room['room_name']); ?>', <?php echo $room['room_price']; ?>)">
                                            <i class="fas fa-calendar-plus me-1"></i>จองเลย
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-times me-1"></i>ไม่ว่าง
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>



    <!-- Display availability results -->
    <?php if (isset($availability)): ?>
        <div class="container mt-4">
            <h4>ผลการตรวจสอบวันที่: <?php echo htmlspecialchars($check_date); ?></h4>
            <ul>
                <?php foreach ($availability as $room): ?>
                    <li>ห้อง ID: <?php echo $room['room_id']; ?> - จำนวนการจอง: <?php echo $room['bookings']; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Booking Policy Section -->
    <section class="py-5 bg-light mt-5">
        <div class="container">
            <h3 class="text-center mb-4">นโยบายการจอง</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-credit-card fa-2x text-primary mb-3"></i>
                            <h5>การชำระเงิน</h5>
                            <p class="text-muted">ต้องชำระเงินล่วงหน้า 100% หรือ 50% สำหรับการจองจำนวนมาก</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-times fa-2x text-warning mb-3"></i>
                            <h5>การยกเลิก</h5>
                            <p class="text-muted">ยกเลิกภายใน 1 สัปดาห์ คืนเงิน (เฉพาะ Low Season)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x text-success mb-3"></i>
                            <h5>Check-in/Check-out</h5>
                            <p class="text-muted">Check-in: 14:00 น. | Check-out: 12:00 น.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
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
        function bookRoom(roomId, roomName, roomPrice) {
            // เปิด modal สำหรับจองห้อง
            const modal = `
                <div class="modal fade" id="bookingModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">จองห้อง: ${roomName}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="bookingForm">
                                    <input type="hidden" name="room_id" value="${roomId}">
                                    <input type="hidden" name="room_price" value="${roomPrice}">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">วันที่ Check-in</label>
                                                <input type="date" class="form-control" name="check_in_date" required 
                                                       min="${new Date().toISOString().split('T')[0]}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">วันที่ Check-out</label>
                                                <input type="date" class="form-control" name="check_out_date" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">จำนวนคน</label>
                                                <input type="number" class="form-control" name="guest_count" value="1" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">วิธีการชำระเงิน</label>
                                                <select class="form-select" name="payment_method" required>
                                                    <option value="โอนเงินเต็ม">โอนเงินเต็ม</option>
                                                    <option value="โอนเงินครึ่ง">โอนเงินครึ่ง</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ (ไม่บังคับ)</label>
                                        <textarea class="form-control" name="note" rows="3" 
                                                  placeholder="เช่น ต้องการห้องที่เงียบ, ต้องการเตียงเสริม, ฯลฯ"></textarea>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h6>ข้อมูลการชำระเงิน</h6>
                                        <p class="mb-1">ราคาต่อคืน: ${roomPrice.toLocaleString()} บาท</p>
                                        <p class="mb-1">จำนวนคืน: <span id="nights">1</span> คืน</p>
                                        <p class="mb-0 fw-bold">ยอดรวม: <span id="total">${roomPrice.toLocaleString()}</span> บาท</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">แนบสลิปโอนเงิน</label>
                                                <input type="file" class="form-control" name="slip_image" required accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                               
                                                    <?php
                                                    require_once('config/condb.php');
                                                    $stmtBank = $condb->prepare("SELECT * FROM tbl_bank ORDER BY id ASC");
                                                    $stmtBank->execute();
                                                    $banks = $stmtBank->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($banks as $bank): ?>
                                                       
                                                    <?php endforeach; ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <h6>ข้อมูลธนาคาร</h6>
                                        <div class="row">
                                            <?php foreach ($banks as $bank): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <p class="mb-1 text-center"><strong>ธนาคาร:</strong> <?= $bank['bank_name'] ?></p>
                                                        <img src="assets/bank_logo/<?= $bank['bank_logo'] ?>" alt="<?= $bank['bank_name'] ?>" class="img-fluid mb-2 d-block mx-auto" style="width: 100px; height: 100px; object-fit: cover;">
                                                        <p class="mb-1"><strong>ชื่อบัญชี:</strong> <?= $bank['bank_account_name'] ?></p>
                                                        <p class="mb-1"><strong>เลขบัญชี:</strong> <?= $bank['bank_account_number'] ?></p>
                                                        <img src="assets/bank_qrcode/<?= $bank['bank_qrcode'] ?>" alt="QR Code" class="img-fluid d-block mx-auto" style="width: 150px; height: 150px; object-fit: cover;">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="button" class="btn btn-primary" onclick="submitBooking()">ยืนยันการจอง</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // ลบ modal เก่าถ้ามี
            const oldModal = document.getElementById('bookingModal');
            if (oldModal) {
                oldModal.remove();
            }
            // เพิ่ม modal ใหม่
            document.body.insertAdjacentHTML('beforeend', modal);
            // แสดง modal
            const modalElement = new bootstrap.Modal(document.getElementById('bookingModal'));
            modalElement.show();
            // เพิ่ม event listener สำหรับคำนวณราคา
            const checkInDate = document.querySelector('input[name="check_in_date"]');
            const checkOutDate = document.querySelector('input[name="check_out_date"]');

            function calculateTotal() {
                if (checkInDate.value && checkOutDate.value) {
                    const checkIn = new Date(checkInDate.value);
                    const checkOut = new Date(checkOutDate.value);
                    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

                    if (nights > 0) {
                        document.getElementById('nights').textContent = nights;
                        document.getElementById('total').textContent = (nights * roomPrice).toLocaleString();
                    }
                }
            }
            checkInDate.addEventListener('change', calculateTotal);
            checkOutDate.addEventListener('change', calculateTotal);
        }

        function submitBooking() {
            const form = document.getElementById('bookingForm');
            const formData = new FormData(form);

            fetch('book_room.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(' จองห้องเรียบร้อยแล้ว! ');
                        // ปิด modal
                        window.location.href = 'booking_success.php?booking_id=' + data.booking_id;
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