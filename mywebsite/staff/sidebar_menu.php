<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <img src="../assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light"> Hi <?= $memberData['name'];?> </span>
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
          <a href="member.php?act=edit" class="nav-link">
            <i class="nav-icon far fa-user"></i>
            <p>
              แก้ไขโปรไฟล์
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="member.php?act=password" class="nav-link">
            <i class="nav-icon far fa-user"></i>
            <p>
              แก้ไขรหัสผ่าน
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