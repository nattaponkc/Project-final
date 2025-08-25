<?php
include('../config/condb.php'); // กำหนด path ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ

if(isset($_GET['id']) && $_GET['act']=='delete'){

    //trigger exception in a "try" block
              try {

$id = $_GET['id'];
//echo $id;

 //single row query แสดงแค่ 1 รายการ จะเอาชื่อไฟล์ภาพไปลบ
      $stmtDelRoom = $condb->prepare("SELECT room_image FROM tbl_room WHERE room_id=?");
      $stmtDelRoom->execute([$_GET['id']]);
      $row = $stmtDelRoom->fetch(PDO::FETCH_ASSOC);

    //แสดงชื่อไฟล์ถาพ
    //   echo 'image name'. $row['room_image'];
    //   exit;


    //แสดงจำนวนคิวรี่ที่ได้ row
    // echo $stmtDelRoom->rowCount();
    // exit;

    if($stmtDelRoom->rowCount() == 0){
        //echo 'เด้งออกไป';
            echo '<script>
                setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    type: "error"
                }, function() {
                    window.location = "booking.php"; //หน้าที่ต้องการให้กระโดดไป
                });
                }, 1000);
            </script>';

    }else{
        //echo 'ส่งไปลบข้อมูลและภาพได้';


        //sql delete
$stmtDelRoom = $condb->prepare('DELETE FROM tbl_room WHERE room_id=:id');
$stmtDelRoom->bindParam(':id', $id , PDO::PARAM_INT);
$stmtDelRoom->execute();  //Del=delete

$condb = null; //close connect db

//echo 'จำนวน row ที่ลบลได้' .$stmtDelProduct->rowCount();
if($stmtDelRoom->rowCount() == 1){
    $image_path = '../assets/room_img/' . $row['room_image'];
    if (!empty($row['room_image']) && file_exists($image_path)) {
        unlink($image_path);
    }
    echo '<script>
         setTimeout(function() {
          swal({
              title: "ลบข้อมูลสำเร็จ",
              type: "success"
          }, function() {
              window.location = "booking.php";
          });
        }, 1000);
    </script>';
    exit();
} //if
    
  } //row count

     } //try
                    //catch exception
                    catch(Exception $e) {
                        //echo 'Message: ' .$e->getMessage();
                        echo '<script>
                             setTimeout(function() {
                              swal({
                                  title: "เกิดข้อผิดพลาด",
                                  type: "error"
                              }, function() {
                                  window.location = "booking.php"; //หน้าที่ต้องการให้กระโดดไป
                              });
                            }, 1000);
                        </script>';
                      } //catch

} //isset
?>