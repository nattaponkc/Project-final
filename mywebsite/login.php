<?php
session_start();
require_once('config/condb.php');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            $sql = "SELECT * FROM tbl_member WHERE username = :username";
            $stmt = $condb->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && sha1($password) === $user['password']) {
                // เก็บข้อมูลใน session
                $_SESSION['staff_id'] = $user['id'];
                $_SESSION['m_name'] = $user['name'];
                $_SESSION['m_level'] = $user['m_level'];
                
                
                
                // redirect ตามระดับผู้ใช้
                if ($user['m_level'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error_message = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        } catch (Exception $e) {
            $error_message = 'เกิดข้อผิดพลาดในการเข้าสู่ระบบ';
        }
    } else {
        $error_message = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - สบายโฮมสเตย์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login_custom.css">
</head>
<body>

 <!-- เมล็ดกาแฟลอย -->
    <div class="coffee-bean"></div>
    <div class="coffee-bean"></div>
    <div class="coffee-bean"></div>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h3><i class="fas fa-home me-2"></i>Add More Cafe'</h3>
                        <p class="mb-0">เข้าสู่ระบบ</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>ชื่อผู้ใช้
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>รหัสผ่าน
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">
                                    จดจำฉัน
                                </label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                                </button>
                            </div>
                        </form>
                        
                        <div class="social-login text-center">
                            <p class="text-muted mb-3">หรือ</p>
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook me-2"></i>เข้าสู่ระบบด้วย Facebook
                                </a>
                                <a href="#" class="btn btn-outline-danger">
                                    <i class="fab fa-google me-2"></i>เข้าสู่ระบบด้วย Google
                                </a>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <p class="mb-2">ยังไม่มีบัญชี? <a href="register.php" class="text-decoration-none">สมัครสมาชิก</a></p>
                            <p class="mb-0"><a href="#" class="text-decoration-none">ลืมรหัสผ่าน?</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="text-white text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>กลับไปหน้าแรก
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login_custom.js" defer></script>
</body>
</html>