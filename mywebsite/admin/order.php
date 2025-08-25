<?php
session_start();
require_once('../config/condb.php');
include('header.php');
include('navbar.php');
include('sidebar_menu.php');

$act = (isset($_GET['act']) ? $_GET['act'] : '' );

// สร้างเงื่อนไขในการเรียกใช้ไฟล์
if ($act == 'delete') {
    include 'order_delete.php';
} else if ($act == 'detail') {
    include 'order_detail.php';
} else if ($act == 'reply') {
    include 'order_reply.php';
} else if ($act == 'status_update') {
    include 'order_status_update.php';
} else {
    // กรณีไม่ตรงกับเงื่อนไขด้านบน
    include 'order_list.php';   
}

include 'footer.php';
?>
