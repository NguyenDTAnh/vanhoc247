<?php
session_start();
require_once '../includes/db.php';

if (isset($_POST['btn_edit'])) {
    // Lấy dữ liệu và làm sạch
    $id       = mysqli_real_escape_string($conn, $_POST['user_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'];

    // Kiểm tra xem username hoặc email có bị trùng với người khác không
    $check_query = "SELECT id FROM users WHERE (username='$username' OR email='$email') AND id != '$id' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Trùng thì báo lỗi và quay lại
        echo "<script>alert('Lỗi: Tên đăng nhập hoặc Email đã tồn tại!'); window.location='users_list.php';</script>";
        exit();
    }

    // Logic xử lý mật khẩu
    $password_update = "";
    if (!empty($password)) {
        // Nếu có nhập mật khẩu mới thì mã hóa và thêm vào câu UPDATE
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_update = ", password='$hashed_password'";
    }

    // Câu lệnh UPDATE
    $sql = "UPDATE users SET 
            username='$username', 
            email='$email', 
            role='$role'
            $password_update
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Thành công thì quay lại kèm thông báo
        header("Location: users_list.php?msg=edited");
    } else {
        // Thất bại thì báo lỗi
        echo "Lỗi: " . mysqli_error($conn);
    }
} else {
    // Không phải submit form thì đuổi về
    header("Location: users_list.php");
}
?>