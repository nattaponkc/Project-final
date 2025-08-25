<?php 

if (isset($_SESSION['staff_id'])): 
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="index.php" class="nav-link">Home</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="../index.php" class="nav-link">หน้าสินค้า</a>
    </li>
  </ul>
  
  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
                                         <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                             <i class="fas fa-user me-ๅ"></i>  <?php echo $_SESSION['m_name']; ?>
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
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="../logout.php">ออกจากระบบ</a></li>
      </ul>
    </li>
  </ul>
</nav>
<!-- /.navbar -->

<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
