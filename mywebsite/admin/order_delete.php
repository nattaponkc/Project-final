<?php
include('../config/condb.php'); // กำหนด path ให้ถูกต้องตามโครงสร้างโฟลเดอร์ของคุณ

if (isset($_GET['id']) && $_GET['act'] == 'delete') {
    try {
        $id = $_GET['id'];

        // ดึงชื่อไฟล์ slip_image
        $stmtOrderDetail = $condb->prepare("SELECT slip_image FROM tbl_order WHERE order_id=?");
        $stmtOrderDetail->execute([$id]);
        $row = $stmtOrderDetail->fetch(PDO::FETCH_ASSOC);

        if ($stmtOrderDetail->rowCount() == 0) {
            // ไม่พบข้อมูล
            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "เกิดข้อผิดพลาด",
                        type: "error"
                    }, function() {
                        window.location = "order.php";
                    });
                }, 1000);
            </script>';
        } else {
            // ลบข้อมูลในฐานข้อมูล
            $stmtDelOrder = $condb->prepare('DELETE FROM tbl_order WHERE order_id=:id');
            $stmtDelOrder->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelOrder->execute();

            if ($stmtDelOrder->rowCount() == 1) {
                // ถ้ามีไฟล์ slip_image ให้ลบ
                if (!empty($row['slip_image']) && file_exists('../assets/slips/' . $row['slip_image'])) {
                    unlink('../assets/slips/' . $row['slip_image']);
                }

                echo '<script>
                    setTimeout(function() {
                        swal({
                            title: "ลบข้อมูลสำเร็จ",
                            type: "success"
                        }, function() {
                            window.location = "order.php";
                        });
                    }, 1000);
                </script>';
            }
        }

        $condb = null; // ปิดการเชื่อมต่อ DB
    } catch (Exception $e) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    type: "error"
                }, function() {
                    window.location = "order.php";
                });
            }, 1000);
        </script>';
    }
}
?>
