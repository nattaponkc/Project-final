<?php include('../config/condb.php');
include('header.php');
 include('navbar.php');
include('sidebar_menu.php');?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	


  </head>
  <body>
  <div class="container">
  
  	<div class="row">
    	<div class="col-md-2">
        <b>  ADMIN  <br>
               	 
      </div>

      <div class="row mb-2">
          <div class="col-sm-6">
            <h1>จัดการข้อมูลธนาคาร
            <a href="add_bank.php" class="btn btn-primary">+ข้อมูล</a>
            </h1>
          </div>
        </div>

        <?php
        // ดึงข้อมูลธนาคารจากฐานข้อมูล
        $stmt = $condb->prepare("SELECT * FROM tbl_bank ORDER BY id DESC");
        $stmt->execute();
        $banks = $stmt->fetchAll();
        ?>

        <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-info">
            <tr>
              <th width="5%" class="text-center">No.</th>
              <th width="15%">Logo</th>
              <th width="20%">ธนาคาร</th>
              <th width="20%">เลขบัญชี</th>
              <th width="20%">ชื่อเจ้าของ บ/ช</th>
              <th width="10%">สาขา</th>
              <th width="10%">QR Code</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach($banks as $bank): ?>
            <tr>
              <td class="text-center"><?= $i++ ?></td>
              <td>
                <?php if(!empty($bank['bank_logo'])): ?>
                  <img src="../assets/bank_logo/<?= htmlspecialchars($bank['bank_logo']) ?>" width="60" height="60" style="object-fit:cover;">
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($bank['bank_name']) ?></td>
              <td><?= htmlspecialchars($bank['bank_account_number']) ?></td>
              <td><?= htmlspecialchars($bank['bank_account_name']) ?></td>
              <td><?= htmlspecialchars($bank['bank_branch']) ?></td>
              <td>
                <?php if(!empty($bank['bank_qrcode'])): ?>
                  <img src="../assets/bank_qrcode/<?= htmlspecialchars($bank['bank_qrcode']) ?>" width="60" height="60" style="object-fit:cover;">
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        </div>
            
      </div>
    </div>
 </div> 
  </body>
</html>
