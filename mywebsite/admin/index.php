<?php
session_start();
//session_destroy();
// print_r($_SESSION);

//สร้างเงื่อนไขตรวจสอบว่ามีการล็อคอินมาแล้วหรือยัง และเป็นสิทธ์ admin หรือไม่

//ถ้าไม่มีการล็อคอิน
if (empty($_SESSION['m_level']) && empty($_SESSION['staff_id'])){ 
    header('Location: ../logout.php'); //ดีดออกไป
}

//เช็คว่าเป็นแอดมินหรือไม่
if(isset($_SESSION['m_level']) && isset($_SESSION['staff_id']) && $_SESSION['m_level'] !='admin'){ 
    header('Location: ../logout.php'); //ดีดออกไป
}


include 'header_dashboard.php';
include 'navbar.php';
include 'sidebar_menu.php';
include 'index_dashboard.php';
include 'footer.php';
?>
