<?php
//คิวรี่ข้อมูลสมาชิก
$queryMember = $condb->prepare("SELECT* FROM tbl_member");
$queryMember->execute();
$rsMember = $queryMember->fetchAll();

//echo 'pre';
//$queryMember->debugDumpParams();
//exit;

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>จัดการข้อมูลสมาชิก

            <a href="member.php?act=add" class="btn btn-primary">+ข้อมูล</a>

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
                    <th width="2%" class="text-center">ID</th>
                    <th width="10%">Username</th>
                    <th width="5%" class="text-center">Level</th>
                    <th width="5%" class="text-center">Title</th>
                    <th width="15%">Name</th>
                    <th width="10%">Surname</th>
                    <th width="9%">Address</th>
                    <th width="7%">Tel</th>
                    <th width="10%">Email</th>
                    <th width="7%" class="text-center">Date</th>
                    <th width="6%"  class="text-center" >แก้รหัส</th>
                    <th width="5%"  class="text-center" >แก้ไข</th>
                    <th width="3%"  class="text-center" >ลบ</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $i = 1; //start number
                  foreach ($rsMember as $row) { ?>
                    <tr>
                      <td align="center"><?php echo $i++ ?> </td>
                      <td><?= $row['username']; ?></td> <!-- Username -->
                      <td><?= $row['m_level']; ?></td>
                      <td><?= $row['title_name']; ?></td>
                      <td><?= $row['name']; ?></td>
                      <td><?= $row['surname']; ?></td>
                      <td><?= $row['address']; ?></td>
                      <td><?= $row['tel']; ?></td>
                      <td><?= $row['email']; ?></td>
                      <td><?= $row['dateCreate']; ?></td>
                                          
                      <td align="center">
                        <a href="member.php?id=<?= $row['id']; ?>&act=editPwd" class="btn btn-info btn-sm">แก้รหัส</a>
                      </td>
                      <td align="center">
                        <a href="member.php?id=<?= $row['id']; ?>&act=edit" class="btn btn-warning btn-sm">แก้ไข</a>
                      </td>
                      <td align="center">
                        <a href="member.php?id=<?= $row['id']; ?>&act=delete" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบข้อมูล??');">ลบ</a>
                      </td>
                    </tr>
                  <?php } ?>

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