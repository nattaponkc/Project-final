<?php
include 'header.php';
include 'navbar.php';
include 'sidebar_menu.php';

$act = (isset($_GET['act']) ? $_GET['act'] : '' );

// สร้างเงื่อนไขในการเรียกใช้ไฟล์
if($act == 'add'){
    include 'booking_form_add.php'; 
} else if($act == 'delete'){
    include 'booking_delete.php';
} else if($act == 'edit'){
    include 'booking_form_edit.php';
} else if($act == 'image'){
    include 'booking_from_upload_image.php';
} else {
    include 'booking_list.php';
}

include 'footer.php';
?>
