<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    //เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
    require_once 'config/condb.php';
    //คิวรี่หมวดหมู่สินค้า
    $queryProductType = $condb->prepare("SELECT * FROM tbl_type  ");
    $queryProductType->execute();
    $rsprdt = $queryProductType->fetchAll();
    ?>

 <!-- start menu -->
 <div class="container-fluid">
     <div class="row">
         <div class="col-sm-12">

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
                                     <a class="nav-link" href="menu.php">เมนู</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="rooms.php">ห้องพัก</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="#about">เกี่ยวกับเรา</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link" href="#contact">ติดต่อ</a>
                                 </li>

                                 <li class="nav-item">
                                     <a class="nav-link" href="#contact">ติดต่อ</a>
                                 </li>


</ul>
                                    
 <ul class="navbar-nav ms-auto">
                                     <li class="nav-item">
                                        <form class="d-flex" action="search.php" method="GET">
                                            <input class="form-control me-2" type="search" name="query" placeholder="ค้นหา" aria-label="Search" style="width:180px; height:30px;">
                                            <button class="btn btn-outline-success btn-sm" type="submit" style="width:60px; height:30px;">ค้นหา</button>
                                        </form>
                                    </li>
 </ul>
                                    



                                     <ul class="navbar-nav ms-auto">
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

         </div>
     </div>
 </div>
 <!-- end menu -->