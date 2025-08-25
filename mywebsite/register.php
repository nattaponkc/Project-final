<?php 
session_start();
require_once('config/condb.php');

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $title_name = $_POST['title_name'] ?? '';
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $address = $_POST['address'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // ตรวจสอบข้อมูล
    if (empty($username) || empty($password) || empty($name) || empty($surname) || empty($tel) || empty($email)) {
        $error_message = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } elseif ($password !== $confirm_password) {
        $error_message = 'รหัสผ่านไม่ตรงกัน';
    } elseif (strlen($password) < 6) {
        $error_message = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    } else {
        try {
            // ตรวจสอบว่ามี username ซ้ำหรือไม่
            $sql_check = "SELECT id FROM tbl_member WHERE username = :username";
            $stmt_check = $condb->prepare($sql_check);
            $stmt_check->execute(['username' => $username]);
            
            if ($stmt_check->fetch()) {
                $error_message = 'ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว';
            } else {
                // ตรวจสอบว่ามี email ซ้ำหรือไม่
                $sql_check_email = "SELECT id FROM tbl_member WHERE email = :email";
                $stmt_check_email = $condb->prepare($sql_check_email);
                $stmt_check_email->execute(['email' => $email]);
                
                if ($stmt_check_email->fetch()) {
                    $error_message = 'อีเมลนี้มีอยู่ในระบบแล้ว';
                } else {
                    // บันทึกข้อมูลสมาชิกใหม่
                    $sql_insert = "INSERT INTO tbl_member (username, password, m_level, title_name, name, surname, address, tel, email, dateCreate) 
                                  VALUES (:username, :password, 'member', :title_name, :name, :surname, :address, :tel, :email, NOW())";
                    $stmt_insert = $condb->prepare($sql_insert);
                    $result = $stmt_insert->execute([
                        'username' => $username,
                        'password' => sha1($password),
                        'title_name' => $title_name,
                        'name' => $name,
                        'surname' => $surname,
                        'address' => $address,
                        'tel' => $tel,
                        'email' => $email
                    ]);
                    
                    if ($result) {
                        $success_message = 'สมัครสมาชิกเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบ';
                    } else {
                        $error_message = 'เกิดข้อผิดพลาดในการสมัครสมาชิก';
                    }
                }
            }
        } catch (Exception $e) {
            $error_message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - สบายโฮมสเตย์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: bold;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="register-header">
                        <h3><i class="fas fa-user-plus me-2"></i>สมัครสมาชิก</h3>
                        <p class="mb-0">สบายโฮมสเตย์</p>
                    </div>
                    
                    <div class="register-body">
                        <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <br><a href="login.php" class="alert-link">คลิกที่นี่เพื่อเข้าสู่ระบบ</a>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="registerForm">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="title_name" class="form-label">คำนำหน้า</label>
                                        <select class="form-select" id="title_name" name="title_name" required>
                                            <option value="">เลือก</option>
                                            <option value="นาย" <?php echo ($_POST['title_name'] ?? '') === 'นาย' ? 'selected' : ''; ?>>นาย</option>
                                            <option value="นาง" <?php echo ($_POST['title_name'] ?? '') === 'นาง' ? 'selected' : ''; ?>>นาง</option>
                                            <option value="นางสาว" <?php echo ($_POST['title_name'] ?? '') === 'นางสาว' ? 'selected' : ''; ?>>นางสาว</option>
                                            <option value="ดร." <?php echo ($_POST['title_name'] ?? '') === 'ดร.' ? 'selected' : ''; ?>>ดร.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">ชื่อ</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="surname" class="form-label">นามสกุล</label>
                                        <input type="text" class="form-control" id="surname" name="surname" 
                                               value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>ชื่อผู้ใช้
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                <div class="form-text">ชื่อผู้ใช้ต้องไม่ซ้ำกับผู้อื่น</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>อีเมล
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tel" class="form-label">
                                    <i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์
                                </label>
                                <input type="tel" class="form-control" id="tel" name="tel" 
                                       value="<?php echo htmlspecialchars($_POST['tel'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>ที่อยู่
                                </label>
                                <textarea class="form-control" id="address" name="address" rows="3" 
                                          placeholder="กรอกที่อยู่ของคุณ"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>รหัสผ่าน
                                        </label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="password-strength" id="passwordStrength"></div>
                                        <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>ยืนยันรหัสผ่าน
                                        </label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="form-text">กรอกรหัสผ่านอีกครั้งเพื่อยืนยัน</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agree" required>
                                <label class="form-check-label" for="agree">
                                    ฉันยอมรับ <a href="#" class="text-decoration-none">เงื่อนไขการใช้งาน</a> และ <a href="#" class="text-decoration-none">นโยบายความเป็นส่วนตัว</a>
                                </label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="fas fa-user-plus me-2"></i>สมัครสมาชิก
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">มีบัญชีอยู่แล้ว? <a href="login.php" class="text-decoration-none">เข้าสู่ระบบ</a></p>
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
    <script>
        // ตรวจสอบความแข็งแกร่งของรหัสผ่าน
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const colors = ['#ff4444', '#ff8800', '#ffaa00', '#00aa00', '#008800'];
            const labels = ['อ่อนมาก', 'อ่อน', 'ปานกลาง', 'แข็ง', 'แข็งมาก'];
            
            strengthBar.style.width = (strength * 20) + '%';
            strengthBar.style.backgroundColor = colors[strength - 1] || '#ddd';
            strengthBar.title = labels[strength - 1] || 'ไม่ระบุ';
        });
        
        // ตรวจสอบรหัสผ่านตรงกัน
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('รหัสผ่านไม่ตรงกัน');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>