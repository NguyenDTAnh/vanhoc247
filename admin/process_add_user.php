<?php
session_start();
require_once '../includes/db.php';

if (isset($_POST['btn_add'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu cho an toàn
    $role     = $_POST['role'];
    $status   = 'active';

    // Kiểm tra xem username hoặc email đã tồn tại chưa
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Lỗi: Tên đăng nhập hoặc Email đã tồn tại!'); window.location='users_list.php';</script>";
    } else {
        $sql = "INSERT INTO users (username, email, password, role, status, created_at) 
                VALUES ('$username', '$email', '$password', '$role', '$status', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: users_list.php?msg=success");
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }
}
?>