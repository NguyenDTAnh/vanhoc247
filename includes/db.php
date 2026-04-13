<?php
// file: includes/db.php
$base_url = "http://localhost/Vanhoc247/";
$conn = mysqli_connect("localhost", "root", "", "vanhoc247");

// Thêm dòng này để không bị lỗi font Tiếng Việt khi lưu vào DB
mysqli_set_charset($conn, "utf8mb4"); 
?>