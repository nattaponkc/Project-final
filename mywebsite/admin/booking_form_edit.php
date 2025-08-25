<?php

//คิวรี่รายละเอียดห้องพัก single row
$stmtRoomDetail = $condb->prepare("SELECT * FROM tbl_room WHERE room_id=:id");
$stmtRoomDetail->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$stmtRoomDetail->execute();
$rowRoom = $stmtRoomDetail->fetch(PDO::FETCH_ASSOC);

if ($stmtRoomDetail->rowCount() == 0) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด",
                type: "error"
            }, function() {
                window.location = "booking_list.php";
            });
        }, 1000);
    </script>';
    exit;
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>ฟอร์มแก้ไขข้อมูลห้องพัก</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-body">
                        <div class="card card-primary">
                            <!-- form start -->
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label class="col-sm-2">ชื่อห้อง</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="room_name" class="form-control" required placeholder="ชื่อห้อง" value="<?php echo $rowRoom['room_name']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">รายละเอียดห้อง</label>
                                        <div class="col-sm-10">
                                            <textarea name="room_detail" id="summernote"><?php echo $rowRoom['room_detail']; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">จำนวนห้อง</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="room_qty" class="form-control" min="0" max="999" value="<?php echo $rowRoom['room_qty']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">ราคาห้อง</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="room_price" class="form-control" min="0" max="999999" value="<?php echo $rowRoom['room_price']; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">ภาพห้อง</label>
                                        <div class="col-sm-4">
                                            ภาพเก่า <br>
                                            <img src="../assets/room_img/<?php echo $rowRoom['room_image']; ?>" width="200px">
                                            <br> <br>
                                            เลือกภาพใหม่
                                            <br>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="room_image" class="custom-file-input" id="exampleInputFile" accept="image/*">
                                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                                </div>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Upload</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">มีทีวี</label>
                                        <div class="col-sm-4">
                                            <select name="has_tv" class="form-control" required>
                                                <option value="1" <?= $rowRoom['has_tv'] == 1 ? 'selected' : ''; ?>>มี</option>
                                                <option value="0" <?= $rowRoom['has_tv'] == 0 ? 'selected' : ''; ?>>ไม่มี</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">มีห้องน้ำ</label>
                                        <div class="col-sm-4">
                                            <select name="has_bathroom" class="form-control" required>
                                                <option value="1" <?= $rowRoom['has_bathroom'] == 1 ? 'selected' : ''; ?>>มี</option>
                                                <option value="0" <?= $rowRoom['has_bathroom'] == 0 ? 'selected' : ''; ?>>ไม่มี</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">เครื่องปรับอากาศหรือพัดลม</label>
                                        <div class="col-sm-4">
                                            <select name="air_or_fan" class="form-control" required>
                                                <option value="Air" <?= $rowRoom['air_or_fan'] == 'Air' ? 'selected' : ''; ?>>เครื่องปรับอากาศ</option>
                                                <option value="Fan" <?= $rowRoom['air_or_fan'] == 'Fan' ? 'selected' : ''; ?>>พัดลม</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-4">
                                            <input type="hidden" name="room_id" value="<?php echo $rowRoom['room_id']; ?>">
                                            <input type="hidden" name="oldImg" value="<?php echo $rowRoom['room_image']; ?>">
                                            <button type="submit" class="btn btn-primary">บันทึก</button>
                                            <a href="booking_list.php" class="btn btn-danger">ยกเลิก</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
if (isset($_POST['room_name']) && isset($_POST['room_price'])) {
    try {
        $room_name = $_POST['room_name'];
        $room_detail = $_POST['room_detail'];
        $room_qty = $_POST['room_qty'];
        $room_price = $_POST['room_price'];
        $has_tv = $_POST['has_tv'];
        $has_bathroom = $_POST['has_bathroom'];
        $air_or_fan = $_POST['air_or_fan'];
        $room_id = $_POST['room_id'];
        $upload = $_FILES['room_image']['name'];

        if ($upload == '') {
            $stmtUpdateRoom = $condb->prepare("UPDATE tbl_room SET
                room_name = :room_name,
                room_detail = :room_detail,
                room_qty = :room_qty,
                room_price = :room_price,
                has_tv = :has_tv,
                has_bathroom = :has_bathroom,
                air_or_fan = :air_or_fan
                WHERE room_id = :room_id");

            $stmtUpdateRoom->bindParam(':room_name', $room_name, PDO::PARAM_STR);
            $stmtUpdateRoom->bindParam(':room_detail', $room_detail, PDO::PARAM_STR);
            $stmtUpdateRoom->bindParam(':room_qty', $room_qty, PDO::PARAM_INT);
            $stmtUpdateRoom->bindParam(':room_price', $room_price, PDO::PARAM_STR);
            $stmtUpdateRoom->bindParam(':has_tv', $has_tv, PDO::PARAM_INT);
            $stmtUpdateRoom->bindParam(':has_bathroom', $has_bathroom, PDO::PARAM_INT);
            $stmtUpdateRoom->bindParam(':air_or_fan', $air_or_fan, PDO::PARAM_STR);
            $stmtUpdateRoom->bindParam(':room_id', $room_id, PDO::PARAM_INT);
            $stmtUpdateRoom->execute();

            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "บันทึกข้อมูลสำเร็จ",
                        type: "success"
                    }, function() {
                        window.location = "booking_list.php";
                    });
                }, 1000);
            </script>';
        } else {
            $date1 = date("Ymd_His");
            $numrand = mt_rand();
            $typefile = strrchr($_FILES['room_image']['name'], ".");

            if ($typefile == '.jpg' || $typefile == '.jpeg' || $typefile == '.png') {
                unlink('../assets/room_img/' . $_POST['oldImg']);
                $path = "../assets/room_img/";
                $newname = $numrand . $date1 . $typefile;
                $path_copy = $path . $newname;
                move_uploaded_file($_FILES['room_image']['tmp_name'], $path_copy);

                $stmtUpdateRoom = $condb->prepare("UPDATE tbl_room SET
                    room_name = :room_name,
                    room_detail = :room_detail,
                    room_qty = :room_qty,
                    room_price = :room_price,
                    room_image = :room_image,
                    has_tv = :has_tv,
                    has_bathroom = :has_bathroom,
                    air_or_fan = :air_or_fan
                    WHERE room_id = :room_id");

                $stmtUpdateRoom->bindParam(':room_name', $room_name, PDO::PARAM_STR);
                $stmtUpdateRoom->bindParam(':room_detail', $room_detail, PDO::PARAM_STR);
                $stmtUpdateRoom->bindParam(':room_qty', $room_qty, PDO::PARAM_INT);
                $stmtUpdateRoom->bindParam(':room_price', $room_price, PDO::PARAM_STR);
                $stmtUpdateRoom->bindParam(':room_image', $newname, PDO::PARAM_STR);
                $stmtUpdateRoom->bindParam(':has_tv', $has_tv, PDO::PARAM_INT);
                $stmtUpdateRoom->bindParam(':has_bathroom', $has_bathroom, PDO::PARAM_INT);
                $stmtUpdateRoom->bindParam(':air_or_fan', $air_or_fan, PDO::PARAM_STR);
                $stmtUpdateRoom->bindParam(':room_id', $room_id, PDO::PARAM_INT);
                $stmtUpdateRoom->execute();

                echo '<script>
                    setTimeout(function() {
                        swal({
                            title: "บันทึกข้อมูลสำเร็จ",
                            type: "success"
                        }, function() {
                            window.location = "booking_list.php";
                        });
                    }, 1000);
                </script>';
            } else {
                echo '<script>
                    setTimeout(function() {
                        swal({
                            title: "คุณอัพโหลดไฟล์ไม่ถูกต้อง",
                            type: "error"
                        }, function() {
                            window.location = "booking_form_edit.php?id=' . $room_id . '";
                        });
                    }, 1000);
                </script>';
            }
        }
    } catch (Exception $e) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    type: "error"
                }, function() {
                    window.location = "booking_list.php";
                });
            }, 1000);
        </script>';
    }
}
?>