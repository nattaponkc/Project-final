<?php
require_once 'includes/session.php';
require_once 'config/condb.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['staff_id'])) {
    echo '<script>
        swal({
            title: "เกิดข้อผิดพลาด",
            text: "กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น",
            type: "error"
        }).then(function() {
            window.location = "login.php"; // เปลี่ยนไปยังหน้าเข้าสู่ระบบ
        });
    </script>';
    exit;
}

// รับข้อมูลจากฟอร์ม
$product_id = $_POST['product_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? null;
$member_id = $_SESSION['staff_id']; // ใช้ ID ของผู้ใช้ที่เข้าสู่ระบบ

// ตรวจสอบข้อมูลที่ส่งมาว่าครบถ้วนหรือไม่
if (empty($product_id) || empty($rating) || empty($comment)) {
    echo '<script>
        swal({
            title: "เกิดข้อผิดพลาด",
            text: "กรุณากรอกข้อมูลให้ครบถ้วน",
            type: "error"
        }).then(function() {
            window.location = "product_detail.php?id=' . $product_id . '"; // กลับไปยังหน้ารายละเอียดสินค้า
        });
    </script>';
    exit;
}

// บันทึกความคิดเห็นลงในฐานข้อมูล
$sql = "INSERT INTO tbl_reviews (product_id, member_id, rating, comment, created_at) 
        VALUES (:product_id, :member_id, :rating, :comment, NOW())";
$stmt = $condb->prepare($sql);
$result = $stmt->execute([
    'product_id' => $product_id,
    'member_id' => $member_id,
    'rating' => $rating,
    'comment' => $comment
]);

// เงื่อนไขตรวจสอบการเพิ่มข้อมูล
if ($result) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
echo '<script>
    Swal.fire({
        title: "เพิ่มข้อมูลสำเร็จ",
        text: "ความคิดเห็นของคุณถูกบันทึกเรียบร้อยแล้ว",
        icon: "success"
    }).then(function() {
        window.location = "product_detail.php?id=' . $product_id . '";
    });
</script>';

} else {
    echo '<script>
        swal({
            title: "เกิดข้อผิดพลาด",
            text: "ไม่สามารถบันทึกความคิดเห็นได้",
            type: "error"
        }).then(function() {
            window.location = "product_detail.php?id=' . $product_id . '"; // กลับไปยังหน้ารายละเอียดสินค้า
        });
    </script>'; 
}
exit;
