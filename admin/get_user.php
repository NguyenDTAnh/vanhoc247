<?php
session_start();
require_once '../includes/db.php';

// Kiểm tra quyền (chỉ admin mới được lấy dữ liệu)
// ... thêm logic check session admin của mày ở đây ...
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "SELECT * FROM users WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Loại bỏ mật khẩu trước khi gửi về
        unset($user['password']);
        
        echo json_encode(['status' => 'success', 'data' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy người dùng này.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: Thiếu ID.']);
}
?>