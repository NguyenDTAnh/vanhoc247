<?php
session_start();
require_once '../includes/db.php';

if (isset($_POST['btn_add_course'])) {
    // 1. Lấy dữ liệu từ Form
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $cat   = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Giả sử mày đã lưu ID admin vào Session khi Login
    // Nếu chưa có hệ thống Login, hãy tạm để $author_id = 1; (ID của mày trong bảng users)
    $author_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

    // 2. Xử lý File Ảnh
    $image_name = $_FILES['image']['name'];
    $image_tmp  = $_FILES['image']['tmp_name'];
    
    // Tạo tên file duy nhất để không bị trùng ảnh (ví dụ: 162534_anh.jpg)
    $new_image_name = time() . '_' . $image_name;
    $target_path = "../uploads/" . $new_image_name;

    // 3. Kiểm tra và Upload
    if (move_uploaded_file($image_tmp, $target_path)) {
        // Nếu upload ảnh lên thư mục thành công, mới bắt đầu lưu vào Database
        $sql = "INSERT INTO courses (title, description, image, category, author_id) 
                VALUES ('$title', '$desc', '$new_image_name', '$cat', '$author_id')";
        
        if (mysqli_query($conn, $sql)) {
            // Thành công thì quay về danh sách khóa học
            header("Location: courses_list.php?msg=success");
            exit();
        } else {
            echo "Lỗi Database: " . mysqli_error($conn);
        }
    } else {
        echo "Lỗi: Không thể upload ảnh. Hãy kiểm tra thư mục uploads!";
    }
}
?>