<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/session.php';
require_once 'config/condb.php';

// ตรวจสอบค่าที่ส่งมา
if (!isset($_GET['review_id']) || !isset($_GET['act']) || $_GET['act'] != 'delete' || !isset($_GET['product_id'])) {
    echo '<script>
        Swal.fire({
            title: "ข้อมูลไม่ถูกต้อง",
            text: "กรุณาตรวจสอบข้อมูลที่ส่งมา",
            icon: "error"
        }).then(() => {
            window.location.href = "menu.php"; // กลับไปหน้าเมนูหลัก
        });
    </script>';
    exit();
}
// ดึงค่าจาก URL
$review_id = $_GET['review_id'];
$product_id = $_GET['product_id'];
// ตรวจสอบสิทธิ์ผู้ใช้งาน
if (!isset($_SESSION['m_level']) || $_SESSION['m_level'] !== 'admin') {
    echo '<script>
        Swal.fire({
            title: "สิทธิ์ไม่ถูกต้อง",
            text: "คุณไม่มีสิทธิ์ในการลบความคิดเห็น",
            icon: "error"
        }).then(() => {
            window.location.href = "detail.php?id=' . $product_id . '&view=show-product-detail";
        });
    </script>';
    exit();
}
try {
    // ลบความคิดเห็น
    $stmtDelReview = $condb->prepare('DELETE FROM tbl_reviews WHERE review_id = :review_id');
    $stmtDelReview->bindParam(':review_id', $review_id, PDO::PARAM_INT);
    $stmtDelReview->execute();
    $condb = null; // ปิดการเชื่อมต่อ
    if ($stmtDelReview->rowCount() == 1) {
        // ลบสำเร็จ
        echo '<script>
            setTimeout(function() {
                Swal.fire({
                    title: "ลบความคิดเห็นสำเร็จ",
                    icon: "success"
                }).then(() => {
                    window.location.href = "detail.php?id=' . $product_id . '&view=show-product-detail";
                });
            }, 500);
        </script>';
        exit();
    } else {
        // ไม่พบความคิดเห็น
        echo '<script>
            Swal.fire({
                title: "ไม่พบความคิดเห็นที่ต้องการลบ",
                icon: "error"
            }).then(() => {
                window.location.href = "detail.php?id=' . $product_id . '&view=show-product-detail";
            });
        </script>';
        exit();
    }
} catch (Exception $e) {
    echo '<script>
        Swal.fire({
            title: "เกิดข้อผิดพลาด",
            text: "กรุณาติดต่อผู้ดูแลระบบ",
            icon: "error"
        }).then(() => {
            window.location.href = "detail.php?id=' . $product_id . '&view=show-product-detail";
        });
    </script>';
    exit();
}
?>
