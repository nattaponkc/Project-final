
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>ฟอร์มแก้ไขรหัสผ่าน</h1>
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
                                <?php
                                // ตรวจสอบว่ามี session และดึงข้อมูลสมาชิกจากฐานข้อมูล
                                if (isset($_SESSION['staff_id'])) {
                                    $stmt = $condb->prepare("SELECT * FROM tbl_member WHERE id = :id");
                                    $stmt->execute(['id' => $_SESSION['staff_id']]);
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                    if (!$row) {
                                        echo '<script>alert("ไม่พบข้อมูลสมาชิก"); window.location = "member.php";</script>';
                                        exit;
                                    }
                                } else {
                                    echo '<script>alert("กรุณาเข้าสู่ระบบก่อน"); window.location = "login.php";</script>';
                                    exit;
                                }
                                ?>

                                <div class="form-group row">
                                    <label class="col-sm-2">Username</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username'] ?? ''); ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2">ชื่อ-สกุล</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars(($row['title_name'] ?? '') . ' ' . ($row['name'] ?? '') . ' ' . ($row['surname'] ?? '')); ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2">New Password</label>
                                    <div class="col-sm-4">
                                        <input type="password" name="NewPassword" class="form-control" required placeholder="รหัสผ่านใหม่">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2">Confirm Password</label>
                                    <div class="col-sm-4">
                                        <input type="password" name="ConfirmPassword" class="form-control" required placeholder="ยืนยันรหัสผ่าน">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2"></label>
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-primary">แก้ไขรหัสผ่าน</button>
                                        <a href="member.php" class="btn btn-danger">ยกเลิก</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col-->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
if (isset($_SESSION['staff_id']) && isset($_POST['NewPassword']) && isset($_POST['ConfirmPassword'])) {
    try {
        // รับค่าจากฟอร์ม
        $NewPassword = $_POST['NewPassword'];
        $ConfirmPassword = $_POST['ConfirmPassword'];

        // ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
        if ($NewPassword !== $ConfirmPassword) {
            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "รหัสผ่านไม่ตรงกัน",
                        text: "กรุณากรอกรหัสผ่านใหม่อีกครั้ง",
                        type: "error"
                    }, function() {
                        window.location = "member_form_edit_password.php";
                    });
                }, 1000);
            </script>';
        } else {
            // เข้ารหัสรหัสผ่านใหม่
            $password = sha1($NewPassword);

            // อัปเดตรหัสผ่านในฐานข้อมูล
            $stmtUpdate = $condb->prepare("UPDATE tbl_member SET password = :password WHERE id = :id");
            $stmtUpdate->bindParam(':password', $password, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':id', $_SESSION['staff_id'], PDO::PARAM_INT);

            $result = $stmtUpdate->execute();

            if ($result) {
                echo '<script>
                    setTimeout(function() {
                        swal({
                            title: "แก้ไขรหัสผ่านสำเร็จ",
                            type: "success"
                        }, function() {
                            window.location = "index.php";
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
                    text: "กรุณาติดต่อผู้ดูแลระบบ",
                    type: "error"
                }, function() {
                    window.location = "member_form_edit_password.php";
                });
            }, 1000);
        </script>';
    }
}
?>
        <script src="../assets/plugins/sweetalert2/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="../assets/plugins/sweetalert2/sweetalert2.min.css">
        <script>
            $(document).ready(function() {
                // ตรวจสอบการแจ้งเตือน
                <?php if (isset($_SESSION['message'])): ?>
                    Swal.fire({
                        title: 'สำเร็จ',
                        text: '<?= $_SESSION['message'] ?>',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    });
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
            });
        </script>
        <script src="../assets/plugins/jquery/jquery.min.js"></script>          