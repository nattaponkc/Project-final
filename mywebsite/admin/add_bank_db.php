
<?php
include('../config/condb.php');
error_reporting(error_reporting() & ~E_NOTICE);
date_default_timezone_set('Asia/Bangkok');
$date1 = date("Ymd_His");
$numrand = mt_rand();

$bank_name = $_POST['b_name'];
$bank_account_number = $_POST['b_number'];
$bank_branch = isset($_POST['bn_name']) ? $_POST['bn_name'] : '';
$bank_account_name = $_POST['b_owner'];

// อัปโหลดโลโก้
$upload_logo = $_FILES['b_logo'];
$logo_name = '';
if ($upload_logo['name'] != '') {
	$type = strrchr($upload_logo['name'], ".");
	$logo_name = 'imgb' . $numrand . $date1 . $type;
	$path = '../assets/bank_logo/';
	$path_copy = $path . $logo_name;
	move_uploaded_file($upload_logo['tmp_name'], $path_copy);
}

// อัปโหลด qrcode

$upload_qrcode = $_FILES['bank_qrcode'];
$qrcode_name = '';
if ($upload_qrcode && $upload_qrcode['name'] != '') {
	$type_qr = strrchr($upload_qrcode['name'], ".");
	$qrcode_name = 'qrcode' . $numrand . $date1 . $type_qr;
	$path_qr = '../assets/bank_qrcode/';
	$path_copy_qr = $path_qr . $qrcode_name;
	move_uploaded_file($upload_qrcode['tmp_name'], $path_copy_qr);
}


$sql = "INSERT INTO tbl_bank (bank_name, bank_branch, bank_account_name, bank_account_number, bank_logo, bank_qrcode)
		VALUES (:bank_name, :bank_branch, :bank_account_name, :bank_account_number, :bank_logo, :bank_qrcode)";
$stmt = $condb->prepare($sql);
$result = $stmt->execute([
	'bank_name' => $bank_name,
	'bank_branch' => $bank_branch,
	'bank_account_name' => $bank_account_name,
	'bank_account_number' => $bank_account_number,
	'bank_logo' => $logo_name,
	'bank_qrcode' => $qrcode_name
]);

if ($result) {
	echo "<script>alert('เพิ่มข้อมูลเรียบร้อยแล้ว'); window.location='list_bank.php';</script>";
} else {
	echo "<script>alert('ERROR!'); window.location='list_bank.php';</script>";
}
?>