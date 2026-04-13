<?php
// // file: includes/db.php
// $base_url = "http://localhost/Vanhoc247/";
// $conn = mysqli_connect("localhost", "root", "", "vanhoc247");

// Laravel Valet(Vì dùng macos nên cần kết nối kiểu nàu, bỏ đi nếu dùng win và uncomment dòng 3, 4)
$base_url = "http://localhost/Vanhoc247/";
$conn = mysqli_connect("127.0.0.1", "root", "123456", "vanhoc247");

// Thêm dòng này để không bị lỗi font Tiếng Việt khi lưu vào DB
mysqli_set_charset($conn, "utf8mb4"); 
?>