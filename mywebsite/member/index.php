<?php
session_start(); // อย่าลืมบรรทัดนี้!


// include ส่วนประกอบหน้าเว็บ
include 'header.php';           // session + css + login check
include 'navbar.php';          // top bar
include 'sidebar_menu.php';    // side menu
include 'index_main.php';      // dashboard content
include 'footer.php';          // ปิด wrapper และโหลด JS
?>

