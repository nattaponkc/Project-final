<?php 
    //เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
    require_once 'config/condb.php'; 
     //insert counter
                $insertCounter = $condb->prepare("INSERT INTO tbl_counter () VALUES ()");             
                $insertCounter->execute();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> ร้านคาเฟ่ แอดมอ </title>
     <meta content="Addmore คาเฟ่ แอดมอ สบายโฮมสเตย์" name="keywords">
    <meta content="ร้านคาเฟ่ แอดมอ ห้องพักรายวัน" name="description">
    <meta property="og:site_name" content=" ร้านคาเฟ่ แอดมอ ">
    <meta property="og:title" content=" ร้านคาเฟ่ แอดมอ " />
    <meta property="og:description" content="ร้านคาเฟ่ แอดมอ ห้องพักรายวัน" />
    <meta property="og:image" itemprop="image" content="assets/product_img/w.jpg">
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="ร้านคาเฟ่ แอดมอ" />
    <meta name="twitter:description" content="ร้านคาเฟ่ แอดมอ ห้องพักรายวัน" />
    <meta name="twitter:image" content="assets/product_img/w.jpg" />
    <link href="assets/bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- sweet alert -->
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

  </head>
<body>