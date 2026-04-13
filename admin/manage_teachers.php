<?php
session_start();
require_once '../includes/db.php'; 

// Chỉ lấy những người là teacher
$sql = "SELECT * FROM users WHERE role = 'teacher' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<main class="main-content">
    <h2 class="fw-800 mb-4">Danh sách Giáo viên</h2>
    </main>