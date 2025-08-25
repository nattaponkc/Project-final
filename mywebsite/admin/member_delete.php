<?php
if(isset($_GET['id']) && $_GET['act']=='delete'){

             //trigger exception in a "try" block
              try {

$id = $_GET['id'];
//echo $id;
$stmtDelMember = $condb->prepare('DELETE FROM tbl_member WHERE id=:id');
$stmtDelMember->bindParam(':id', $id , PDO::PARAM_INT);
$stmtDelMember->execute();

$condb = null; //close connect db

//echo 'จำนวน row ที่ลบลได้' .$stmtDelMember->rowCount();
if($stmtDelMember->rowCount() == 1){
        echo '<script>
             setTimeout(function() {
              swal({
                  title: "ลบข้อมูลสำเร็จ",
                  type: "success"
              }, function() {
                  window.location = "member.php"; //หน้าที่ต้องการให้กระโดดไป
              });
            }, 1000);
        </script>';
        exit();
    } }//try
                    //catch exception
                    catch(Exception $e) {
                        //echo 'Message: ' .$e->getMessage();
                        // exit;
                        echo '<script>
                             setTimeout(function() {
                              swal({
                                  title: "เกิดข้อผิดพลาด",
                                  text: "กรุณาติดต่อผู้ดูแลระบบ",
                                  type: "error"
                              }, function() {
                                  window.location = "member.php"; //หน้าที่ต้องการให้กระโดดไป
                              });
                            }, 1000);
                        </script>';
                      } //catch
       } //isset
?>