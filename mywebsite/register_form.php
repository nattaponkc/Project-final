 <?php
        //เช็ค input ที่ส่งมาจากฟอร์ม
        // echo '<pre>';
        // print_r($_POST);
        // exit;

        if (isset($_POST['username']) 
            && isset($_POST['password'])  
            && isset($_POST['title_name'])
            && isset($_POST['name']) 
            && isset($_POST['surname']) 
            && isset($_POST['address'])
            && isset($_POST['tel'])
            && isset($_POST['email'])                     
            && isset($_POST['action'])
            && $_POST['action']=='register' ) {

            //echo 'ถูกเงื่อนไขส่งข้อมูลมาได้'

            //trigger exception in a "try" block
              try {

            //ประกาศตัวแปรรับค่าจากฟอร์ม
            $username   = $_POST['username'];
            $password   = sha1($_POST['password']); // แนะนำใช้ password_hash แทน
            $title_name = $_POST['title_name'];
            $name       = htmlspecialchars($_POST['name']);
            $surname    = htmlspecialchars($_POST['surname']);
            $address    = htmlspecialchars($_POST['address']);
            $tel        = htmlspecialchars($_POST['tel']);
            $email      = htmlspecialchars($_POST['email']);
            $m_level    = 'member';     //กำหนดค่า m_level เป็น member             
            $dateCreate = date('Y-m-d H:i:s'); //วันที่สร้างข้อมูล
            

            //เช็ค Username ซ้ำ
            $stmtMemberDetail = $condb->prepare("SELECT username FROM tbl_member WHERE username=:username");
            
            // bindParam
            $stmtMemberDetail->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtMemberDetail->execute();
            $row = $stmtMemberDetail->fetch(PDO::FETCH_ASSOC);

            // //นับจำนวนการquery ถ้าได้ 1 username ซ้ำ
            // echo $stmtMemberDetail->rowCount();
            // echo '<hr>';
            if($stmtMemberDetail->rowCount() == 1){
                //echo 'Username ซ้ำ';
                        echo '<script>
                        setTimeout(function() {
                            swal({
                                title: "Username ซ้ำ !!",
                                text: "สมัครสมาชิกใหม่อีกครั้ง",
                                type: "error"
                            }, function() {
                                window.location = "register.php"; //หน้าที่ต้องการให้กลับไป
                            });
                        }, 1000);
                    </script>';


            }else{
                //echo 'ไม่มี username ซ้ำ';
                //sql insert
            $stmtInsertMember = $condb->prepare("INSERT INTO tbl_member 
                (
                    username,
                    password,
                    title_name,
                    name,
                    surname,
                    address,
                    tel,
                    email,
                    m_level,
                    dateCreate

                ) 
                VALUES 
                (
                    :username,
                    :password,
                    :title_name,
                    :name,
                    :surname,
                    :address,
                    :tel,
                    :email,
                    :m_level,
                    :dateCreate
                )
                ");

                // bindParam
                $stmtInsertMember->bindParam(':username', $username, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':password', $password, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':title_name', $title_name, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':name', $name, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':surname', $surname, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':address', $address, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':tel', $tel, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':email', $email, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':m_level', $m_level, PDO::PARAM_STR);
                $stmtInsertMember->bindParam(':dateCreate', $dateCreate, PDO::PARAM_STR);                
                
                $result = $stmtInsertMember->execute();

                    $condb = null; //close connect db

                if ($result) {
                    echo '<script>
                        setTimeout(function() {
                            swal({
                                title: "สมัครสมาชิกสำเร็จ",
                                text: "คลิก Ok เพื่อ Login",
                                type: "success"
                            }, function() {
                                window.location = "login.php"; //หน้าที่ต้องการกระโดดไป
                            });
                        }, 1000);
                    </script>';
                }

            }//เช็คข้อมูลซ้ำ

             } //try
                    //catch exception
                    catch(Exception $e) {
                        echo '<pre>';
    print_r($e->getMessage());
    echo '</pre>';
                        //echo 'Message: ' .$e->getMessage();
                        // exit;
                        echo '<script>
                             setTimeout(function() {
                              swal({
                                  title: "เกิดข้อผิดพลาด",
                                  type: "error"
                              }, function() {
                                  window.location = "register.php"; //หน้าที่ต้องการให้กระโดดไป
                              });
                            }, 1000);
                        </script>';
                      } //catch
         //exit();
        } //isset
    ?>

<!-- start form register -->

<div class="container mt-3 ">
    <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-7">
            <h4>Register form</h4>

            <form action="" method="post">

                <div class="form-group row mb-2">
                    <label class="col-sm-2">Username</label>
                    <div class="col-sm-7">
                        <input type="username" name="username" class="form-control" required placeholder="Username">
                    </div>
                </div>

                <div class="form-group row mb-2">
                   <label class="col-sm-2">Password</label>
                    <div class="col-sm-7">
                        <input type="password" name="password" class="form-control" required placeholder="Password">
                    </div>
                </div>

                                <div class="form-group row mb-2">
                                      <label class="col-sm-2">คำนำหน้า</label>
                                      <div class="col-sm-7">
                                        <select name="title_name" class="form-control" required>
                                        
                                        <option value="">-- เลือกข้อมูล --</option>
                                        <option value="นาย">-- นาย --</option>
                                        <option value="นาง">-- นาง --</option>
                                        <option value="นางสาว">-- นางสาว --</option>

                                        </select>
                                      </div>
                                  </div>

                                      <div class="form-group row mb-2">
                                          <label class="col-sm-2">ชื่อ</label>
                                          <div class="col-sm-7">
                                              <input type="text" name="name" class="form-control" required placeholder="ชื่อ">
                                          </div>
                                      </div>

                                      <div class="form-group row mb-2">
                                          <label class="col-sm-2">นามสกุล</label>
                                          <div class="col-sm-7">
                                              <input type="text" name="surname" class="form-control" required placeholder="นามสกุล">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">ที่อยู่</label>
                                          <div class="col-sm-7">
                                              <input type="text" name="address" class="form-control" required placeholder="ที่อยู่">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">โทร</label>
                                          <div class="col-sm-7">
                                              <input type="text" name="tel" class="form-control" required placeholder="โทร">
                                          </div>
                                      </div>

                                      <div class="form-group row">
                                          <label class="col-sm-2">email</label>
                                          <div class="col-sm-7">
                                              <input type="email" name="email" class="form-control" required placeholder="email">
                                          </div>
                                      </div>
                                 
                                      <div class="form-group row">                                       
                                          <label class="col-sm-2"></label>
                                          <div class="col-sm-4 mt-2">
                                              <button type="submit" name="action" value="register" class="btn btn-primary ">สมัครสมาชิก</button>
                                              <a href="index.php" class="btn btn-danger">ยกเลิก</a>
                                          </div>
                                      </div>
            </form>
        </div>

    </div>
</div>
<!-- end form register-->