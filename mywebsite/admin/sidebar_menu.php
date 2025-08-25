<?php
//คิวรี่ข้อมูลของคนที่ผ่านการล็อคอิน
$memberDetail = $condb->prepare("SELECT name FROM tbl_member WHERE id=:id");
//bindParam
$memberDetail->bindParam(':id', $_SESSION['staff_id'], PDO::PARAM_INT);
$memberDetail->execute();
$memberData = $memberDetail->fetch(PDO::FETCH_ASSOC);

// echo '<pre>';
// print_r($memberData);


?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <img src="../assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light"> Admin <?= $memberData['name']; ?> </span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
              with font-awesome or any other icon font library -->

        <li class="nav-item">
          <a href="index.php" class="nav-link">
            <i class="nav-icon fas fa-home"></i>
            <p>
              หน้าหลัก
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="index.php" class="nav-link">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="form.php" class="nav-link">
            <i class="nav-icon far fa-window-maximize"></i>
            <p>
              ฟอร์ม
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="datatable.php" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              ตาราง
            </p>
          </a>
        </li>

        <!-- <li class="nav-item">
          <a href="admin.php" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>
              จัดการ Admin
            </p>
          </a>
        </li> -->

        <li class="nav-item">
          <a href="type.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              จัดการหมวดหมู่สินค้า
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="product.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              จัดการข้อมูลสินค้า
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="booking.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>จัดการห้องพัก</p>
          </a>
        </li>


        <li class="nav-item">
          <a href="order.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>จัดการคำสั่งซื้อ</p>
          </a>
        </li>



        <li class="nav-item">
          <a href="booking_order_list.php" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>รายการจองห้องพัก</p>
          </a>
        </li>


        <li class="nav-item">
          <a href="member.php" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              จัดการสมาชิก
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="list_bank.php" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              ธนาคาร
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="../logout.php" class="nav-link">
            <i class="nav-icon fas fa-lock"></i>
            <p>
              ออกจากระบบ
            </p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>