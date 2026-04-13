<?php
session_start();
include 'includes/db.php';

// 1. Nếu đã đăng nhập rồi thì kiểm tra role để đẩy về trang tương ứng
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php"); // Đường dẫn đúng vào thư mục admin
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = "";
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            // Lưu thông tin vào Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // 2. Điều hướng dựa trên quyền hạn
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php"); // Vào thẳng Dashboard Admin
            } else {
                header("Location: index.php"); // User thường về trang chủ
            }
            exit();
        } else {
            $error = "Mật khẩu không chính xác!";
        }
    } else {
        $error = "Tài khoản email này không tồn tại!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
    body {
        background-color: #050505 !important;
        margin: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .bg-visuals {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: -1;
    }
    .glow-1 {
        position: absolute;
        top: -10%; right: -5%;
        width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(245, 87, 108, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .glow-2 {
        position: absolute;
        bottom: -10%; left: -5%;
        width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(240, 147, 251, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .grid-pattern {
        position: absolute;
        width: 100%; height: 100%;
        background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), 
                          linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 50px 50px;
    }
    .login-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
    }
    .auth-card {
        background: rgba(20, 20, 22, 0.6);
        backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 30px;
        padding: 50px 40px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.8);
    }
    .navbar-brand-muse {
        font-size: 3rem;
        font-weight: 900;
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: block;
        text-align: center;
        margin-bottom: 5px;
        letter-spacing: -2px;
    }
    .form-label {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.4);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 10px;
        display: block;
    }
    .form-control {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 15px;
        padding: 15px 20px;
        color: #fff !important;
        width: 100%;
    }
    .form-control:focus {
        border-color: #f5576c !important;
        box-shadow: 0 0 20px rgba(245, 87, 108, 0.2);
        outline: none;
    }
    .btn-auth {
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        border: none;
        border-radius: 15px;
        padding: 16px;
        font-weight: 800;
        color: #fff;
        width: 100%;
        margin-top: 10px;
        cursor: pointer;
        text-transform: uppercase;
        transition: 0.3s;
    }
    .btn-auth:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
    }
    .alert-muse {
        background: rgba(220, 53, 69, 0.1);
        color: #ff6b6b;
        border-radius: 12px;
        padding: 12px;
        font-size: 0.85rem;
        text-align: center;
        margin-bottom: 25px;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }
</style>

<div class="bg-visuals">
    <div class="grid-pattern"></div>
    <div class="glow-1"></div>
    <div class="glow-2"></div>
</div>

<div class="login-wrapper">
    <div class="auth-card">
        <div class="text-center mb-5">
            <span class="navbar-brand-muse">MUSE</span>
            <h2 style="color: #fff; font-size: 1.8rem; font-weight: 800;">Chào mừng trở lại</h2>
        </div>

        <?php if($error): ?>
            <div class="alert-muse">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-4">
                <label class="form-label">Email tài khoản</label>
                <input type="email" name="email" class="form-control" placeholder="admin@muse.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn-auth">Đăng nhập ngay</button>
        </form>

        <div class="text-center mt-5">
            <p style="color: rgba(255,255,255,0.3); font-size: 0.9rem;">
                Bạn chưa có tài khoản? 
                <a href="register.php" style="color: #f5576c; text-decoration: none; font-weight: 800;">Gia nhập ngay</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>