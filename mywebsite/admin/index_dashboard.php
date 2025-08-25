<?php
// =====================================================
// CHART DATA QUERIES (ส่วนกราฟเดิม)
// =====================================================
// จำนวนออเดอร์
$stmtCountOrder = $condb->prepare("SELECT COUNT(*) as totalOrder FROM tbl_order");
$stmtCountOrder->execute();
$rowOrder = $stmtCountOrder->fetch(PDO::FETCH_ASSOC);

// จำนวนห้องพัก
$stmtCountRoom = $condb->prepare("SELECT COUNT(*) as totalRoom FROM tbl_room");
$stmtCountRoom->execute();
$rowRoom = $stmtCountRoom->fetch(PDO::FETCH_ASSOC);

// จำนวนจอง
$stmtCountBooking = $condb->prepare("SELECT COUNT(*) as totalBooking FROM tbl_booking");
$stmtCountBooking->execute();
$rowBooking = $stmtCountBooking->fetch(PDO::FETCH_ASSOC);

// จำนวนผู้เข้าชม
$stmtCountcounter = $condb->prepare("SELECT COUNT(*) as totalView FROM tbl_counter");
$stmtCountcounter->execute();
$rowC = $stmtCountcounter->fetch(PDO::FETCH_ASSOC);

// จำนวนสมาชิก
$stmtCountMember = $condb->prepare("SELECT COUNT(*) as totalMember FROM tbl_member");
$stmtCountMember->execute();
$rowM = $stmtCountMember->fetch(PDO::FETCH_ASSOC);

// จำนวนสินค้า
$stmtCountPrd = $condb->prepare("SELECT COUNT(*) as totalProduct FROM tbl_product");
$stmtCountPrd->execute();
$rowP = $stmtCountPrd->fetch(PDO::FETCH_ASSOC);

// จำนวนผู้เข้าเว็บไซต์แยกตามวัน
$queryViewByDay = $condb->prepare("SELECT DATE_FORMAT(c_date,'%d/%m/%Y') as datesave, COUNT(*) as total 
FROM tbl_counter 
GROUP BY DATE_FORMAT(c_date,'%Y-%m-%d') 
ORDER BY DATE_FORMAT(c_date,'%Y-%m-%d') DESC;");
$queryViewByDay->execute();
$rsVd = $queryViewByDay->fetchAll();

$report_data = array();
foreach ($rsVd as $rs) {
$report_data[]= '
{
  name:'.'"'.$rs['datesave'].'"'.',
  y:'.$rs['total'].',
  drilldown:'.'"'.$rs['datesave'].'"'.',
}';
}
$report_data = implode(",", $report_data);

// จำนวนผู้ใช้แยกตามเดือน
$queryViewByMonth = $condb->prepare("SELECT MONTHNAME(c_date) as monthNames, COUNT(*) as totalByMonth 
FROM tbl_counter 
GROUP BY MONTH(c_date) 
ORDER BY DATE_FORMAT(c_date, '%Y-%m') DESC; ");
$queryViewByMonth->execute();
$rsVM = $queryViewByMonth->fetchAll();

$report_data_month = array();
foreach ($rsVM as $rs) {
$report_data_month[]= '
{
  name:'.'"'.$rs['monthNames'].'"'.',
  y:'.$rs['totalByMonth'].',
  drilldown:'.'"'.$rs['monthNames'].'"'.',
}';
}
$report_data_month = implode(",", $report_data_month);

// จำนวนผู้ใช้แยกตามปี
$queryViewByYear = $condb->prepare("SELECT YEAR(c_date) as years, COUNT(*) as totalByYear 
FROM tbl_counter 
GROUP BY YEAR(c_date) 
ORDER BY YEAR(c_date) DESC;");
$queryViewByYear->execute();
$rsVy = $queryViewByYear->fetchAll();

$report_data_year = array();
foreach ($rsVy as $rs) {
$report_data_year[]= '
{
  name:'.'"'.$rs['years'].'"'.',
  y:'.$rs['totalByYear'].',
  drilldown:'.'"'.$rs['years'].'"'.',
}';
}
$report_data_year = implode(",", $report_data_year);

?>

<!-- ===================================================== -->
<!-- DASHBOARD LAYOUT -->
<!-- ===================================================== -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">แดชบอร์ด</h1>
        </div>
        <div class="col-sm-6">
          <div class="float-right">
            <button type="button" class="btn btn-outline-secondary btn-sm me-2">
              <i class="fas fa-download"></i> ส่งออก
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-print"></i> พิมพ์
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- ===================================================== -->
      <!-- STATISTICS CARDS -->
      <!-- ===================================================== -->
      <div class="row mb-4">
        <!-- Card: คำสั่งซื้อวันนี้ -->
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                  <h6 class="text-muted text-uppercase mb-1 small">เข้าชมเว็บไซต์</h6>
                  <h3 class="mb-1 fw-bold text-dark"><?=$rowC['totalView'];?></h3>
                  <p class="text-muted mb-0 small">รวม: <?php echo $rowC['totalView'] ?? 0; ?> </p>


                </div>
                <div class="ms-3">
                  <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-shopping-cart text-white fs-4"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

 <!-- Card: ออเดอร์ทั้งหมด -->
<div class="col-xl-3 col-md-6 mb-4">
  <div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <h6 class="text-muted text-uppercase mb-1 small">ออเดอร์ทั้งหมด</h6>
          <h3 class="mb-1 fw-bold text-dark"><?php echo $rowOrder['totalOrder'] ?? 0; ?></h3>
          <p class="text-muted mb-0 small">รวม: - บาท</p>
        </div>
        <div class="ms-3">
          <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-calendar-check text-white fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


       <!-- Card: สมาชิกทั้งหมด -->
<div class="col-xl-3 col-md-6 mb-4">
  <div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <h6 class="text-muted text-uppercase mb-1 small">สมาชิกทั้งหมด</h6>
          <h3 class="mb-1 fw-bold text-dark"><?php echo $rowM['totalMember'] ?? 0; ?></h3>
          <p class="text-muted mb-0 small">ลูกค้าที่ลงทะเบียน</p>
        </div>
        <div class="ms-3">
          <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-users text-white fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

       <!-- Card: จำนวนสินค้า -->
<div class="col-xl-3 col-md-6 mb-4">
  <div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <h6 class="text-muted text-uppercase mb-1 small">จำนวนสินค้า</h6>
          <h3 class="mb-1 fw-bold text-dark"><?php echo $rowP['totalProduct'] ?? 0; ?></h3>
          <p class="text-muted mb-0 small">ทั้งหมด</p> 
        </div>
        <div class="ms-3">
          <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-box-open text-white fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

   <!-- Card: จำนวนห้องพัก -->
<div class="col-xl-3 col-md-6 mb-4">
  <div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <h6 class="text-muted text-uppercase mb-1 small">จำนวนห้องพัก</h6>
          <h3 class="mb-1 fw-bold text-dark"><?php echo $rowRoom['totalRoom'] ?? 0; ?></h3>
          <p class="text-muted mb-0 small">ทั้งหมด</p> 
        </div>
        <div class="ms-3">
          <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-box-open text-white fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Card: จำนวนการจอง -->
<div class="col-xl-3 col-md-6 mb-4">
  <div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <h6 class="text-muted text-uppercase mb-1 small">จำนวนการจอง</h6>
          <h3 class="mb-1 fw-bold text-dark"><?php echo $rowBooking['totalBooking'] ?? 0; ?></h3>
          <p class="text-muted mb-0 small">ทั้งหมด</p> 
        </div>
        <div class="ms-3">
          <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-box-open text-white fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

      <!-- ===================================================== -->
      <!-- RECENT ACTIVITIES -->
      <!-- ===================================================== -->
      <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4"></div>
    <div class="col-xl-3 col-md-6 mb-4"></div>  
</div>
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
              <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>คำสั่งซื้อล่าสุด
              </h6>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="border-0">เลขที่</th>
                      <th class="border-0">ลูกค้า</th>
                      <th class="border-0">สถานะ</th>
                      <th class="border-0">ราคา</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($recent_orders)): ?>
                      <?php foreach ($recent_orders as $order): ?>
                      <tr>
                        <td class="border-0"><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td class="border-0"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td class="border-0">
                          <span class="badge bg-warning text-dark">
                            <?php echo htmlspecialchars($order['status']); ?>
                          </span>
                        </td>
                        <td class="border-0"><?php echo number_format($order['total_amount']); ?> บาท</td>
                      </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center text-muted border-0">ไม่มีข้อมูล</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Bookings -->
        <div class="col-lg-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
              <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-calendar-check me-2 text-success"></i>การจองล่าสุด
              </h6>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="border-0">เลขที่</th>
                      <th class="border-0">ลูกค้า</th>
                      <th class="border-0">ห้อง</th>
                      <th class="border-0">สถานะ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($recent_bookings)): ?>
                      <?php foreach ($recent_bookings as $booking): ?>
                      <tr>
                        <td class="border-0"><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td class="border-0"><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td class="border-0"><?php echo htmlspecialchars($booking['room_name'] ?? '-'); ?></td>
                        <td class="border-0">
                          <span class="badge bg-warning text-dark">
                            <?php echo htmlspecialchars($booking['payment_status'] ?? 'รอชำระ'); ?>
                          </span>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center text-muted border-0">ไม่มีข้อมูล</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================================================== -->
      <!-- TOP PRODUCTS -->
      <!-- ===================================================== -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
              <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-chart-line me-2 text-info"></i>สินค้าขายดี
              </h6>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="border-0">อันดับ</th>
                      <th class="border-0">ชื่อสินค้า</th>
                      <th class="border-0">จำนวนที่ขาย</th>
                      <th class="border-0">สถานะ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($top_products)): ?>
                      <?php foreach ($top_products as $index => $product): ?>
                      <tr>
                        <td class="border-0">
                          <span class="badge bg-<?php echo $index < 3 ? 'primary' : 'secondary'; ?> rounded-pill">
                            <?php echo $index + 1; ?>
                          </span>
                        </td>
                        <td class="border-0 fw-bold"><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td class="border-0"><?php echo $product['order_count']; ?> ครั้ง</td>
                        <td class="border-0">
                          <?php if ($index < 3): ?>
                          <span class="badge bg-success">ขายดี</span>
                          <?php else: ?>
                          <span class="badge bg-info">ปกติ</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center text-muted border-0">ไม่มีข้อมูล</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ===================================================== -->
      <!-- CHARTS SECTION (ส่วนกราฟเดิม) -->
      <!-- ===================================================== -->
      
      <!-- Website Visit Charts -->
      <div class="row mt-4">
        <div class="col-sm-12">
          <figure class="highcharts-figure">
            <div id="container"></div>
            <p class="highcharts-description">.</p>
          </figure>
          <script>
            Highcharts.chart('container', {
              chart: { type: 'line' },
              title: { text: 'จำนวนการเข้าชมเว็บไซต์แยกตามวัน' },
              subtitle: { text: 'รวมทั้งสิ้น <?=$rowC['totalView'];?> ครั้ง ' },
              accessibility: { announceNewData: { enabled: true } },
              xAxis: { type: 'category' },
              yAxis: { title: { text: 'จำนวนการเข้าชมเว็บไซต์' } },
              legend: { enabled: false },
              plotOptions: {
                series: {
                  borderWidth: 0,
                  dataLabels: { enabled: true, format: '{point.y:.0f} ครั้ง' }
                }
              },
              tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f} ครั้ง</b> of total<br/>'
              },
              series: [{
                name: "จำนวนการเข้าชมเว็บไซต์",
                colorByPoint: true,
                data: [<?= $report_data;?>]
              }]
            });
          </script>
        </div>

        <div class="col-sm-8">
          <figure class="highcharts-figure">
            <div id="container2"></div>
            <p class="highcharts-description">.</p>
          </figure>
          <script>
            Highcharts.chart('container2', {
              chart: { type: 'column' },
              title: { text: 'จำนวนการเข้าชมเว็บไซต์แยกตามเดือน' },
              subtitle: { text: 'รวมทั้งสิ้น <?=$rowC['totalView'];?> ครั้ง ' },
              accessibility: { announceNewData: { enabled: true } },
              xAxis: { type: 'category' },
              yAxis: { title: { text: 'จำนวนการเข้าชมเว็บไซต์' } },
              legend: { enabled: false },
              plotOptions: {
                series: {
                  borderWidth: 0,
                  dataLabels: { enabled: true, format: '{point.y:.0f} ครั้ง' }
                }
              },
              tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f} ครั้ง</b> of total<br/>'
              },
              series: [{
                name: "จำนวนการเข้าชมเว็บไซต์",
                colorByPoint: true,
                data: [<?= $report_data_month;?>]
              }]
            });
          </script>
        </div>

        <div class="col-sm-4">
          <figure class="highcharts-figure">
            <div id="container3"></div>
            <p class="highcharts-description">.</p>
          </figure>
          <script>
            Highcharts.chart('container3', {
              chart: { type: 'column' },
              title: { text: 'จำนวนการเข้าชมเว็บไซต์แยกตามปี' },
              subtitle: { text: 'รวมทั้งสิ้น <?=$rowC['totalView'];?> ครั้ง ' },
              accessibility: { announceNewData: { enabled: true } },
              xAxis: { type: 'category' },
              yAxis: { title: { text: 'จำนวนการเข้าชมเว็บไซต์' } },
              legend: { enabled: false },
              plotOptions: {
                series: {
                  borderWidth: 0,
                  dataLabels: { enabled: true, format: '{point.y:.0f} ครั้ง' }
                }
              },
              tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f} ครั้ง</b> of total<br/>'
              },
              series: [{
                name: "จำนวนการเข้าชมเว็บไซต์",
                colorByPoint: true,
                data: [<?= $report_data_year;?>]
              }]
            });
          </script>
        </div>
      </div>

      <!-- Order and Booking Charts -->
      <div class="row">
        <div class="col-sm-6">
          <figure class="highcharts-figure">
            <div id="order_chart"></div>
          </figure>
          <script>
            Highcharts.chart('order_chart', {
              chart: { type: 'column' },
              title: { text: 'จำนวนคำสั่งซื้อรายวัน' },
              subtitle: { text: 'รวมทั้งหมดจากระบบคำสั่งซื้อ' },
              xAxis: { type: 'category' },
              yAxis: { title: { text: 'จำนวนคำสั่งซื้อ' } },
              legend: { enabled: false },
              plotOptions: {
                series: {
                  borderWidth: 0,
                  dataLabels: { enabled: true, format: '{point.y:.0f} รายการ' }
                }
              },
              tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> รายการ<br/>'
              },
              series: [{
                name: "คำสั่งซื้อ",
                colorByPoint: true,
                data: [<?= $order_data_day ?>]
              }]
            });
          </script>
        </div>

        <div class="col-sm-6">
          <figure class="highcharts-figure">
            <div id="booking_chart"></div>
          </figure>
          <script>
            Highcharts.chart('booking_chart', {
              chart: { type: 'column' },
              title: { text: 'การจองห้องพักรายวัน' },
              subtitle: { text: 'รวมทั้งหมดจากระบบจองห้องพัก' },
              xAxis: { type: 'category' },
              yAxis: { title: { text: 'จำนวนการจอง' } },
              legend: { enabled: false },
              plotOptions: {
                series: {
                  borderWidth: 0,
                  dataLabels: { enabled: true, format: '{point.y:.0f} ครั้ง' }
                }
              },
              tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> ครั้ง<br/>'
              },
              series: [{
                name: "การจองห้องพัก",
                colorByPoint: true,
                data: [<?= $booking_data_day ?>]
              }]
            });
          </script>
        </div>
      </div>

    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->