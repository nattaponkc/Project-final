
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>ฟอร์มแก้ไขโปรไฟล์</h1>
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

                                    <?php
                                    // ตรวจสอบว่ามี session และดึงข้อมูลสมาชิกจากฐานข้อมูล
                                    if (isset($_SESSION['staff_id'])) {
                                        $stmt = $condb->prepare("SELECT * FROM tbl_member WHERE id = :id");
                                        $stmt->execute(['id' => $_SESSION['staff_id']]);
                                        $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

                                        if (!$memberData) {
                                            echo '<script>alert("ไม่พบข้อมูลสมาชิก"); window.location = "member.php";</script>';
                                            exit;
                                        }
                                    } else {
                                        echo '<script>alert("กรุณาเข้าสู่ระบบก่อน"); window.location = "login.php";</script>';
                                        exit;
                                    }
                                    ?>

                                    <div class="form-group row">
                                        <label class="col-sm-2">สิทธ์การใช้งาน</label>
                                        <div class="col-sm-2">
                                            <select name="m_level" class="form-control" disabled>
                                                <option value="<?= htmlspecialchars($memberData['m_level'] ?? ''); ?>">
                                                    <?= isset($memberData['m_level']) ? '-- ' . htmlspecialchars($memberData['m_level']) . ' --' : '-- ไม่พบข้อมูล --'; ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">Email/Username</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($memberData['username'] ?? ''); ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">คำนำหน้า</label>
                                        <div class="col-sm-2">
                                            <select name="title_name" class="form-control" required>
                                                <option value="<?= htmlspecialchars($memberData['title_name'] ?? ''); ?>">
                                                    <?= isset($memberData['title_name']) ? '-- ' . htmlspecialchars($memberData['title_name']) . ' --' : '-- ไม่พบข้อมูล --'; ?>
                                                </option>
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
                                            <input type="text" name="name" class="form-control" required placeholder="ชื่อ" value="<?= htmlspecialchars($memberData['name'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">นามสกุล</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="surname" class="form-control" required placeholder="นามสกุล" value="<?= htmlspecialchars($memberData['surname'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary">บันทึก</button>
                                            <a href="member.php" class="btn btn-danger">ยกเลิก</a>
                                        </div>
                                    </div>

                                </div> <!-- /.card-body -->
                            </form>

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
if (isset($_SESSION['staff_id']) && isset($_POST['name']) && isset($_POST['surname'])) {
    try {
        // รับค่าจากฟอร์ม
        $title_name = $_POST['title_name'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $username = $_POST['username'];

        // อัปเดตข้อมูลในฐานข้อมูล
        $stmtUpdate = $condb->prepare("UPDATE tbl_member SET 
            title_name = :title_name,
            name = :name,
            surname = :surname,
            username = :username
            WHERE id = :id
        ");
        $stmtUpdate->bindParam(':id', $_SESSION['staff_id'], PDO::PARAM_INT);
        $stmtUpdate->bindParam(':title_name', $title_name, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':name', $name, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':surname', $surname, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':username', $username, PDO::PARAM_STR);

        $result = $stmtUpdate->execute();

        if ($result) {
            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "แก้ไขข้อมูลสำเร็จ",
                        type: "success"
                    }, function() {
                        window.location = "member.php?act=edit";
                    });
                }, 1000);
            </script>';
        }
    } catch (Exception $e) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    text: "กรุณาติดต่อผู้ดูแลระบบ/Username ซ้ำ",
                    type: "error"
                }, function() {
                    window.location = "member.php?act=edit";
                });
            }, 1000);
        </script>';
    }
}
?>