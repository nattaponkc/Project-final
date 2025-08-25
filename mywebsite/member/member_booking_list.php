<?php


// ดึงค่า staff_id จาก session
$member_id = $_SESSION['staff_id'] ?? null;


// ดึงคำสั่งซื้อทั้งหมดของผู้ใช้
$stmt = $condb->prepare("SELECT * FROM tbl_order WHERE member_id = :member_id ORDER BY order_date DESC");
$stmt->execute(['member_id' => $member_id]);
$orders = $stmt->fetchAll();
?>



<style>
  .card-body {
    margin-left: 50px; /* ขยับตารางไปทางขวา */
  }

  .text-center-cell {
    text-align: center; /* จัดข้อความให้อยู่ตรงกลางเฉพาะเซลล์ที่มี class นี้ */
  }
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>จัดการข้อมูลสินค้า</h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>


  
<div class="card-body">
 <table id="ordersTable" class="table table-bordered table-hover">
    <tr  class="table-info">
        <th width="2%" class="text-center">No.</th>
        <th width="5%" class="text-center">รหัสคำสั่งซื้อ</th>
        <th width="5%" class="text-center">เวลาสั่ง</th>
        <th width="5%" class="text-center">ยอดรวม</th>
        <th width="5%" class="text-center">เพิ่มเติม</th>
        <th width="5%" class="text-center">สถานะ</th>
        <th width="5%" class="text-center">รายละเอียด</th>
    </tr>

    <?php $i = 1; ?>
<?php foreach ($orders as $row): ?>
    <tr>
        <td class="text-center"><?= $i++ ?></td> <!-- ลำดับ -->
        <td class="text-center"><?= $row['order_id'] ?></td>
        <td class="text-center"><?= $row['order_date'] ?></td>
        <td class="text-center"><?= $row['total_amount'] ?></td>
        <td><?= htmlspecialchars($row['customer_note']) ?></td>
        <td>
            <?php
            switch ($row['status']) {
                case 'รอดำเนินการ':
                    echo '<span style="color:orange;">รอดำเนินการ</span>';
                    break;
                case 'กำลังทำ':
                    echo '<span style="color:blue;">กำลังทำ</span>';
                    break;
                case 'เสร็จแล้ว':
                    echo '<span style="color:green;">เสร็จแล้ว</span>';
                    break;
                default:
                    echo htmlspecialchars($row['status']);
            }
            ?>
        </td>
        <td class="text-center">
            <a href="member_order_detail.php?order_id=<?= $row['order_id'] ?>">ดู</a>
        </td>
    </tr>
<?php endforeach; ?>

 </table>
</div>
