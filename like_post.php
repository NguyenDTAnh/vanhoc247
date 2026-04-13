<?php
include 'includes/db.php';
session_start();

// Kiểm tra xem đã đăng nhập chưa và có post_id gửi lên không
if (isset($_SESSION['user_id']) && isset($_POST['post_id'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($_POST['post_id']); // Ép kiểu số nguyên cho an toàn

    // 1. Kiểm tra xem người dùng này đã like bài này chưa
    $check_query = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // 2. Nếu đã like rồi -> Xóa (Unlike)
        $delete_sql = "DELETE FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
        if (mysqli_query($conn, $delete_sql)) {
            echo "unliked";
        }
    } else {
        // 3. Nếu chưa like -> Thêm mới (Like)
        $insert_sql = "INSERT INTO likes (user_id, post_id) VALUES ('$user_id', '$post_id')";
        if (mysqli_query($conn, $insert_sql)) {
            echo "liked";
        }
    }
} else {
    echo "error_not_logged_in";
}
?>