<?php
include 'includes/db.php';
session_start();

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Lấy ID bài viết từ URL
if (isset($_GET['id'])) {
    $post_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // 3. Bảo mật: Chỉ cho phép xóa bài của CHÍNH MÌNH (trừ khi là Admin sau này)
    // Câu lệnh này kiểm tra cả post_id và user_id khớp nhau mới xóa
    $sql = "DELETE FROM posts WHERE id = '$post_id' AND user_id = '$user_id'";

    if (mysqli_query($conn, $sql)) {
        // Xóa xong quay lại trang profile với thông báo thành công
        header("Location: profile.php?msg=deleted");
    } else {
        echo "Lỗi xóa bài: " . mysqli_error($conn);
    }
} else {
    header("Location: profile.php");
}
exit();