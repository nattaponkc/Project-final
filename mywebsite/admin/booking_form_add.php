<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../config/condb.php'); // เชื่อมต่อฐานข้อมูล

// ดึงประเภทห้องพักทั้งหมด
$stmtRoomType = $condb->prepare("SELECT * FROM tbl_room_type ORDER BY room_type_id ASC");
$stmtRoomType->execute();
$rsRoomType = $stmtRoomType->fetchAll();
?>

<!-- ฟอร์มเพิ่มข้อมูลห้องพัก -->
<div class="content-wrapper">
  <section class="content-header">
      <div class="container-fluid">
          <div class="row mb-2">
              <div class="col-sm-6">
                  <h1>ฟอร์มเพิ่มข้อมูลห้องพัก</h1>
              </div>
          </div>
      </div>
  </section>

  <section class="content">
      <div class="row">
          <div class="col-md-12">
              <div class="card card-outline card-info">
                  <div class="card-body">
                      <div class="card card-primary">
                          <form action="" method="post" enctype="multipart/form-data">
                              <div class="card-body">

                                  <div class="form-group row">
                                      <label class="col-sm-2">ประเภทห้อง</label>
                                      <div class="col-sm-3">
                                          <select name="ref_room_type_id" class="form-control" required>
                                              <option value="">-- เลือกข้อมูล --</option>
                                              <?php foreach($rsRoomType as $row){ ?>
                                                  <option value="<?php echo $row['room_type_id'];?>">-- <?php echo $row['room_type_name'];?> --</option>
                                              <?php } ?>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">ชื่อห้อง</label>
                                      <div class="col-sm-6">
                                          <input type="text" name="room_name" class="form-control" required>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">รายละเอียดห้อง</label>
                                      <div class="col-sm-10">
                                          <textarea name="room_detail" id="summernote"></textarea>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">จำนวนห้อง</label>
                                      <div class="col-sm-4">
                                          <input type="number" name="room_qty" class="form-control" value="1" min="1" required>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">ราคาห้อง</label>
                                      <div class="col-sm-4">
                                          <input type="number" name="room_price" class="form-control" step="0.01" min="0" required>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">จำนวนผู้เข้าพักสูงสุด</label>
                                      <div class="col-sm-4">
                                          <input type="number" name="max_guests" class="form-control" value="1" min="1" required>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">ประเภทฤดูกาล</label>
                                      <div class="col-sm-4">
                                          <select name="season_type" class="form-control" required>
                                              <option value="low">Low</option>
                                              <option value="high">High</option>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">เงินมัดจำที่ต้องการ</label>
                                      <div class="col-sm-4">
                                          <input type="number" name="deposit_required" class="form-control" step="0.01" min="0" required>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">ภาพห้อง</label>
                                      <div class="col-sm-4">
                                          <div class="input-group">
                                              <div class="custom-file">
                                                  <input type="file" name="room_image" class="custom-file-input" required accept="image/*">
                                                  <label class="custom-file-label">Choose file</label>
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
                                              <option value="1">มี</option>
                                              <option value="0">ไม่มี</option>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">มีห้องน้ำ</label>
                                      <div class="col-sm-4">
                                          <select name="has_bathroom" class="form-control" required>
                                              <option value="1">มี</option>
                                              <option value="0">ไม่มี</option>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2">เครื่องปรับอากาศหรือพัดลม</label>
                                      <div class="col-sm-4">
                                          <select name="air_or_fan" class="form-control" required>
                                              <option value="Air">เครื่องปรับอากาศ</option>
                                              <option value="Fan">พัดลม</option>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                      <label class="col-sm-2"></label>
                                      <div class="col-sm-4">
                                          <button type="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
                                          <a href="booking_list.php" class="btn btn-danger">ยกเลิก</a>
                                      </div>
                                  </div>
                              </div>
                          </form>

                          <?php
                          if (isset($_POST['room_name']) && isset($_POST['ref_room_type_id']) && isset($_POST['room_price']) && isset($_POST['max_guests']) && isset($_POST['season_type']) && isset($_POST['deposit_required'])) {
                              try {
                                  $ref_room_type_id = $_POST['ref_room_type_id'];
                                  $room_name = $_POST['room_name'];
                                  $room_detail = $_POST['room_detail'];
                                  $room_qty = $_POST['room_qty'];
                                  $room_price = $_POST['room_price'];
                                  $max_guests = $_POST['max_guests'];
                                  $season_type = $_POST['season_type'];
                                  $deposit_required = $_POST['deposit_required'];

                                  $date1 = date("Ymd_His");
                                  $numrand = mt_rand();

                                  // ตรวจสอบไฟล์ภาพ
                                  if (isset($_FILES['room_image']) && $_FILES['room_image']['name'] != '') {
                                      $upload = $_FILES['room_image']['name'];
                                      $typefile = strrchr($upload, ".");
                                      $allowed_types = ['.jpg', '.jpeg', '.png'];

                                      if (in_array(strtolower($typefile), $allowed_types)) {
                                          $path = "../assets/room_img/";
                                          $newname = $numrand . $date1 . $typefile;
                                          $path_copy = $path . $newname;

                                          if (move_uploaded_file($_FILES['room_image']['tmp_name'], $path_copy)) {
                                              // Insert ห้องพัก
                                              $stmtInsertRoom = $condb->prepare("INSERT INTO tbl_room
                                                  (ref_room_type_id, room_name, room_detail, room_qty, room_price, room_image, max_guests, season_type, deposit_required, has_tv, has_bathroom, air_or_fan)
                                                  VALUES (:ref_room_type_id, :room_name, :room_detail, :room_qty, :room_price, :room_image, :max_guests, :season_type, :deposit_required, :has_tv, :has_bathroom, :air_or_fan)
                                              ");
                                              $stmtInsertRoom->bindParam(':ref_room_type_id', $ref_room_type_id, PDO::PARAM_INT);
                                              $stmtInsertRoom->bindParam(':room_name', $room_name, PDO::PARAM_STR);
                                              $stmtInsertRoom->bindParam(':room_detail', $room_detail, PDO::PARAM_STR);
                                              $stmtInsertRoom->bindParam(':room_qty', $room_qty, PDO::PARAM_INT);
                                              $stmtInsertRoom->bindParam(':room_price', $room_price, PDO::PARAM_STR);
                                              $stmtInsertRoom->bindParam(':room_image', $newname, PDO::PARAM_STR);
                                              $stmtInsertRoom->bindParam(':max_guests', $max_guests, PDO::PARAM_INT);
                                              $stmtInsertRoom->bindParam(':season_type', $season_type, PDO::PARAM_STR);
                                              $stmtInsertRoom->bindParam(':deposit_required', $deposit_required, PDO::PARAM_STR);
                                              $stmtInsertRoom->bindParam(':has_tv', $_POST['has_tv'], PDO::PARAM_INT);
                                              $stmtInsertRoom->bindParam(':has_bathroom', $_POST['has_bathroom'], PDO::PARAM_INT);
                                              $stmtInsertRoom->bindParam(':air_or_fan', $_POST['air_or_fan'], PDO::PARAM_STR);

                                              $resultRoom = $stmtInsertRoom->execute();

                                              if ($resultRoom) {
                                                  // ดึง id ห้องที่เพิ่งเพิ่ม
                                                  $last_room_id = $condb->lastInsertId();

                                                  // Insert ภาพห้องลง tbl_room_image (ถ้าคุณมีตารางนี้)
                                                  $stmtInsertImg = $condb->prepare("INSERT INTO tbl_room_image (ref_room_id, room_image) VALUES (:ref_room_id, :room_image)");
                                                  $stmtInsertImg->bindParam(':ref_room_id', $last_room_id, PDO::PARAM_INT);
                                                  $stmtInsertImg->bindParam(':room_image', $newname, PDO::PARAM_STR);
                                                  $stmtInsertImg->execute();

                                                  echo '<script>
                                                      setTimeout(function() {
                                                          swal({
                                                              title: "เพิ่มห้องพักสำเร็จ",
                                                              icon: "success"
                                                          }, function() {
                                                              window.location = "booking_list.php";
                                                          });
                                                      }, 1000);
                                                  </script>';
                                              } else {
                                                  throw new Exception("เพิ่มข้อมูลห้องพักล้มเหลว");
                                              }
                                          } else {
                                              throw new Exception("อัปโหลดไฟล์ล้มเหลว");
                                          }
                                      } else {
                                          echo '<script>
                                              setTimeout(function() {
                                                  swal({
                                                      title: "ไฟล์ไม่ถูกต้อง (.jpg/.jpeg/.png เท่านั้น)",
                                                      icon: "error"
                                                  }, function() {
                                                      window.location = "booking_form_add.php";
                                                  });
                                              }, 1000);
                                          </script>';
                                      }
                                  } else {
                                      echo '<script>
                                          setTimeout(function() {
                                              swal({
                                                  title: "กรุณาเลือกไฟล์ภาพ",
                                                  icon: "warning"
                                              });
                                          }, 1000);
                                      </script>';
                                  }
                              } catch (Exception $e) {
                                  echo '<script>
                                      setTimeout(function() {
                                          swal({
                                              title: "เกิดข้อผิดพลาด: ' . $e->getMessage() . '",
                                              icon: "error"
                                          }, function() {
                                              window.location = "booking_form_add.php";
                                          });
                                      }, 1000);
                                  </script>';
                              }
                          }
                          ?>

                      </div>
                  </div>
              </div>
          </div>
      </div>
  </section>
</div>
