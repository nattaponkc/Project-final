<?php
session_start();

// ลบข้อมูล session ทั้งหมด
session_destroy();

// ลบ cookie ถ้ามี
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// redirect ไปหน้าแรก
header('Location: index.php');
exit;
?>