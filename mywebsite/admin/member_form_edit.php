   <?php
    if(isset($_GET['id']) && $_GET['act'] == 'edit'){

      //single row query แสดงแค่ 1 รายการ
      $stmtMemberDetail = $condb->prepare("SELECT * FROM tbl_member WHERE id=?");
      $stmtMemberDetail->execute([$_GET['id']]);
      $row = $stmtMemberDetail->fetch(PDO::FETCH_ASSOC);

    
        // echo '<pre>';
        // print_r($row);
        // exit;
        // echo $stmtMemberDetail->rowCount();
        // exit;

      //ถ้าคิวรี่ผิดพลาดให้หยุดการทำงาน
      if($stmtMemberDetail->rowCount() !=1){
          exit();
      }
    }//isset
    ?>
  
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <div class="container-fluid">
              <div class="row mb-2">
                  <div class="col-sm-6">
                      <h1>ฟอร์มแก้ไขข้อมูลสมาชิก</h1>
                  </div>
              </div>
          </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
          <div class="row">
              <div class="col-md-12">
                  <div class="card card-outline card-info">
                      <div class="card-body">
                          <div class="card card-primary">
                              <!-- form start -->
                              <form action="" method="post">
                                  <div class="card-body">

                                   <div class="form-group row">
                                      <label class="col-sm-2">สิทธ์การใช้งาน</label>
                                      <div class="col-sm-2">
                                        <select name="m_level" class="form-control" required>
                                            <option value="<?php echo $row['m_level'];?>">-- <?php echo $row['m_level'];?> --</option>
                                        <option disabled="">-- เลือกข้อมูลใหม่ --</option>
                                        <option value="admin">-- admin --</option>
                                        <option value="staff">-- staff --</option>
                                        <option value="member">-- member --</option>
                                        </select>
                                      </div>
                                  </div>

                                  <div class="form-group row">
                                          <label class="col-sm-2">Username</label>
                                          <div class="col-sm-4">
                                              <input type="username" name="username" class="form-control" value="<?php echo $row['username'];?>" required>
                                          </div>
                                      </div>

                                    <div class="form-group row">
                                      <label class="col-sm-2">คำนำหน้า</label>
                                      <div class="col-sm-2">
                                        <select name="title_name" class="form-control" required>

                                        <option value="<?php echo $row['title_name'];?>">-- <?php echo $row['title_name'];?> --</option>
                                        <option disabled>-- เลือกข้อมูลใหม่ --</option>
                                        <option value="นาย">-- นาย --</option>
                                        <option value="นาง">-- นาง --</option>
                                        <option value="นางสาว">-- นางสาว --</option>

                                        </select>
                                      </div>
                                  </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">ชื่อ</label>
                                          <div class="col-sm-4">
                                              <input type="text" name="name" class="form-control" required placeholder="ชื่อ" value="<?php echo $row['name'];?>">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">นามสกุล</label>
                                          <div class="col-sm-4">
                                              <input type="text" name="surname" class="form-control" required placeholder="นามสกุล" value="<?php echo $row['surname'];?>">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">ที่อยู่</label>
                                          <div class="col-sm-4">
                                              <input type="text" name="address" class="form-control" required placeholder="ที่อยู่" value="<?php echo $row['address'];?>">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">โทร</label>
                                          <div class="col-sm-4">
                                              <input type="text" name="tel" class="form-control" required placeholder="โทร" value="<?php echo $row['tel'];?>">
                                          </div>
                                      </div>

                                       <div class="form-group row">
                                          <label class="col-sm-2">email</label>
                                          <div class="col-sm-4">
                                              <input type="text" name="email" class="form-control" required placeholder="email" value="<?php echo $row['email'];?>">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2"></label>
                                          <div class="col-sm-4">                                        
                                             <input type="hidden" name="id" value="<?php echo $row['id'];?>">
                                              <button type="submit" class="btn btn-primary ">บันทึก</button>
                                              <a href="member.php" class="btn btn-danger">ยกเลิก</a>
                                          </div>
                                      </div>

                                  </div> <!-- /.card-body -->

                              </form>
                            <?php
                            //   echo '<pre>';
                            //   print_r($_POST);
                            //   exit;
                            ?>  

                           </div>
                      </div>
                  </div>
              </div>
              <!-- /.col-->
          </div>
          <!-- ./row -->
      </section>
      <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php 
                // echo '<pre>';
                // print_r($_POST);
                //exit;

                if(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['surname'])){

                //echo 'เข้ามาในเงื่อนไขได้';
                //exit;

                //trigger exception in a "try" block
              try {

                //ประกาศตัวแปรรับค่าจากฟอร์ม
                $id = $_POST['id'];
                $username   = $_POST['username'];
                $title_name = $_POST['title_name'];
                $name       = $_POST['name'];
                $surname = $_POST['surname'];
                $m_level = $_POST['m_level'];
                $username = $_POST['username'];
                $address    = $_POST['address'];
                $tel        = $_POST['tel'];
                $email      = $_POST['email'];
                date_default_timezone_set('Asia/Bangkok');
                $dateCreate = date('Y-m-d H:i:s');

                //sql update
                $stmtUpdate = $condb->prepare("UPDATE  tbl_member SET 
                username=:username,
                title_name=:title_name,
                name=:name,
                surname=:surname,
                m_level=:m_level,              
                address=:address,
                tel=:tel,
                email=:email,
                dateCreate=:dateCreate
                WHERE id=:id 
                ");
                //bindParam
                $stmtUpdate->bindParam(':id', $id , PDO::PARAM_INT);
                $stmtUpdate->bindParam(':username', $username , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':title_name', $title_name , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':name', $name , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':surname', $surname , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':m_level', $m_level , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':address', $address , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':tel', $tel , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':email', $email , PDO::PARAM_STR);
                $stmtUpdate->bindParam(':dateCreate', $dateCreate , PDO::PARAM_STR);
                

                $result = $stmtUpdate->execute();

                $condb = null; //close connect db

                if($result){
                echo '<script>
                    setTimeout(function() {
                    swal({
                        title: "แก้ไขข้อมูลสำเร็จ",
                        type: "success"
                    }, function() {
                        window.location = "member.php"; 
                    });
                    }, 1000);
                </script>';
                }

                 }//try
                    //catch exception
                    catch(Exception $e) {
                        //echo 'Message: ' .$e->getMessage();
                        // exit;
                        echo '<script>
                             setTimeout(function() {
                              swal({
                                  title: "เกิดข้อผิดพลาด",
                                  text: "กรุณาติดต่อผู้ดูแลระบบ/Username ซ้ำ",
                                  type: "error"
                              }, function() {
                                  window.location = "member.php"; //หน้าที่ต้องการให้กระโดดไป
                              });
                            }, 1000);
                        </script>';
                      } //catch
            
         } //isset

?>



