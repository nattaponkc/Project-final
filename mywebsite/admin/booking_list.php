<?php
include('../config/condb.php');
include('header.php');
// include('navbar.php');
include('sidebar_menu.php');


//คิวรี่ข้อมูลห้อง
$queryRoom = $condb->prepare("SELECT * FROM tbl_room ORDER BY room_id DESC");
$queryRoom->execute();
$rsRoom = $queryRoom->fetchAll();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>จัดการข้อมูลห้องพัก
                        <a href="booking.php?act=add" class="btn btn-primary">+ข้อมูล</a>
                    </h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr class="table-info">
                                        <th width="5%" class="text-center">No.</th>
                                        <th width="10%">ภาพ</th>
                                        <th width="15%">ชื่อห้อง</th>
                                        <th width="20%">รายละเอียด</th>
                                        <!-- <th width="10%">ประเภทห้อง</th> -->
                                        <th width="5%" class="text-center">จำนวนห้อง</th>
                                        <th width="5%" class="text-center">จำนวนผู้เข้าพัก</th>
                                        <th width="5%" class="text-center">ฤดูกาล</th>
                                        <th width="5%" class="text-center">ราคา</th>
                                        <th width="7%" class="text-center">เงินมัดจำ</th>
                                        <th width="8%" class="text-center">สถานะ</th>
                                        <th width="10%" class="text-center">วันที่สร้าง</th>
                                        <th width="10%" class="text-center">ทีวี</th>
                                        <th width="8%" class="text-center">ห้องน้ำ</th>
                                        <th width="10%" class="text-center">แอร์/พัดลม</th>
                                        <th width="4%" class="text-center">+ภาพ</th>
                                        <th width="5%" class="text-center">แก้ไข</th>
                                        <th width="5%" class="text-center">ลบ</th>

                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $i = 1; //start number
                                    foreach ($rsRoom as $row) { ?>
                                        <tr>
                                            <td align="center"><?= $i++ ?></td>
                                            <td><img src="../assets/room_img/<?= $row['room_image']; ?>" width="70px"></td>
                                            <td><?= $row['room_name']; ?></td>
                                            <td><?= $row['room_detail']; ?></td>
                                            <!-- <td>< /$row['room_type_detail']; /td> -->
                                            <td align="center"><?= $row['room_qty']; ?></td>
                                            <td align="center"><?= $row['max_guests']; ?></td>
                                            <td align="center"><?= $row['season_type']; ?></td>
                                            <td align="right"><?= number_format($row['room_price'], 2); ?></td>
                                            <td align="right"><?= number_format($row['deposit_required'], 2); ?></td>
                                            <td align="center"><?= $row['room_status']; ?></td>
                                            <td align="center"><?= $row['date_create']; ?></td>
                                            <td align="center"><?= $row['has_tv'] == 1 ? 'มี' : 'ไม่มี'; ?></td>
                                            <td align="center"><?= $row['has_bathroom'] == 1 ? 'มี' : 'ไม่มี'; ?></td>
                                            <td align="center"><?= $row['air_or_fan'] == 'Air' ? 'เครื่องปรับอากาศ' : 'พัดลม'; ?></td>
                                            <td align="center">
                                                <a href="booking.php?id=<?= $row['room_id']; ?>&act=image" class="btn btn-info btn-sm">+ภาพ</a>
                                            </td>
                                            <td align="center">
                                                <a href="booking.php?act=edit&id=<?= $row['room_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                            </td>
                                            <td align="center">
                                                <a href="booking_delete.php?id=<?= $row['room_id']; ?>&act=delete" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบห้องพัก?');">ลบ</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                </tbody>




                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->