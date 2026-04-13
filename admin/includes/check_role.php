<?php
// Bắt đầu phiên làm việc
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra: Nếu chưa đăng nhập HOẶC không phải là admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Đá về trang login ở thư mục gốc (dùng ../ để lùi lại 1 thư mục)
    header("Location: ../login.php");
    exit();
}
?>