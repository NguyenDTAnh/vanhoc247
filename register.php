<?php
// 1. Khởi tạo session và kết nối DB
session_start();
include 'includes/db.php';

// Nếu đã đăng nhập thì không cho ở trang này nữa
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// 2. Xử lý logic Đăng ký (Giữ nguyên của mày)
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email này đã được sử dụng!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Đăng ký thành công! Đang chuyển hướng...";
                echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại!";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
    /* Tổng thể layout tối giản & hiện đại */
    body {
        background-color: #050505 !important;
        margin: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Hiệu ứng nền Grid và Glow lấp đầy khoảng trống */
    .bg-visuals {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    .grid-pattern {
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), 
                          linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    .glow-1 {
        position: absolute;
        top: -15%;
        left: -5%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(245, 87, 108, 0.12) 0%, transparent 70%);
        border-radius: 50%;
    }

    .glow-2 {
        position: absolute;
        bottom: -10%;
        right: -5%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(240, 147, 251, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Wrapper ép form vào giữa */
    .register-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
    }

    /* Glassmorphism Card */
    .auth-card {
        background: rgba(18, 18, 20, 0.65);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 30px;
        padding: 45px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.7);
    }

    .navbar-brand-muse {
        font-size: 2.8rem;
        font-weight: 900;
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: block;
        text-align: center;
        letter-spacing: -2px;
    }

    .form-label {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.4);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 14px;
        padding: 12px 18px;
        color: #fff !important;
        transition: 0.3s ease;
    }

    .form-control:focus {
        border-color: #f5576c !important;
        box-shadow: 0 0 15px rgba(245, 87, 108, 0.2);
        outline: none;
        background: rgba(255, 255, 255, 0.06) !important;
    }

    .btn-auth {
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        border: none;
        border-radius: 14px;
        padding: 15px;
        font-weight: 800;
        color: #fff;
        width: 100%;
        margin-top: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.4s;
    }

    .btn-auth:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(245, 87, 108, 0.4);
        filter: brightness(1.1);
    }

    /* Social Signup Section */
    .social-divider {
        display: flex;
        align-items: center;
        margin: 35px 0 20px;
        color: rgba(255,255,255,0.2);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .social-divider::before, .social-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.08);
        margin: 0 15px;
    }

    .social-btns-group {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-btn-item {
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        font-size: 1.3rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .social-btn-item:hover {
        transform: translateY(-5px) scale(1.1);
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .social-btn-item.google:hover { border-color: #ea4335; box-shadow: 0 5px 20px rgba(234, 67, 53, 0.2); }
    .social-btn-item.facebook:hover { border-color: #1877f2; box-shadow: 0 5px 20px rgba(24, 119, 242, 0.2); }
    .social-btn-item.apple:hover { border-color: #fff; box-shadow: 0 5px 20px rgba(255, 255, 255, 0.1); }

    .benefit-tag {
        display: inline-block;
        padding: 5px 15px;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 50px;
        font-size: 0.7rem;
        color: rgba(255,255,255,0.4);
        margin: 4px;
    }

    .alert-muse {
        border-radius: 12px;
        padding: 12px;
        font-size: 0.85rem;
        text-align: center;
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .alert-error { background: rgba(220, 53, 69, 0.15); color: #ff6b6b; border-color: rgba(220, 53, 69, 0.2); }
    .alert-success { background: rgba(25, 135, 84, 0.15); color: #75b798; border-color: rgba(25, 135, 84, 0.2); }
</style>

<!-- Phần nền lấp đầy khoảng trống -->
<div class="bg-visuals">
    <div class="grid-pattern"></div>
    <div class="glow-1"></div>
    <div class="glow-2"></div>
</div>

<div class="register-wrapper">
    <div class="auth-card animate__animated animate__fadeIn">
        <div class="text-center mb-4">
            <span class="navbar-brand-muse">MUSE</span>
            <h2 style="color: #fff; font-size: 1.7rem; font-weight: 800; letter-spacing: -0.5px;">Tạo tài khoản mới</h2>
            <div class="mt-3">
                <span class="benefit-tag"><i class="fas fa-bolt me-1 text-warning"></i> Muse AI</span>
                <span class="benefit-tag"><i class="fas fa-shield-alt me-1 text-info"></i> Bảo mật</span>
                <span class="benefit-tag"><i class="fas fa-heart me-1 text-danger"></i> Miễn phí</span>
            </div>
        </div>

        <!-- Thông báo Error/Success -->
        <?php if($error): ?>
            <div class="alert-muse alert-error animate__animated animate__shakeX">
                <i class="fas fa-circle-exclamation me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert-muse alert-success">
                <i class="fas fa-circle-check me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên người dùng</label>
                <input type="text" name="username" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email học viên</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Xác nhận</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" name="register" class="btn-auth">Đăng ký ngay</button>
        </form>

        <!-- Social Signup -->
        <div class="social-signup-section text-center">
            <div class="social-divider">
                <span>Hoặc đăng ký nhanh với</span>
            </div>

            <div class="social-btns-group">
                <a href="#" class="social-btn-item google" title="Google">
                    <i class="fab fa-google"></i>
                </a>
                <a href="#" class="social-btn-item facebook" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-btn-item apple" title="Apple">
                    <i class="fab fa-apple"></i>
                </a>
            </div>
        </div>

        <div class="text-center mt-5">
            <p style="color: rgba(255,255,255,0.3); font-size: 0.9rem;">
                Đã có tài khoản Muse? 
                <a href="login.php" style="color: #f093fb; text-decoration: none; font-weight: 800;">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>