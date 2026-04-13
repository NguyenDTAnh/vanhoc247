<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['save_post'])) {
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    // Bảo mật: Chỉ cho phép cập nhật nếu bài này thuộc về chính chủ
    $sql = "UPDATE posts SET title = '$title', content = '$content' 
            WHERE id = '$post_id' AND user_id = '$user_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: profile.php?msg=updated");
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
exit();