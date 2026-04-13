<?php
session_start();
require_once '../includes/db.php'; 

// Chỉ lấy những người là admin
$sql = "SELECT * FROM users WHERE role = 'admin' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<main class="main-content">
    <h2 class="fw-800 mb-4">Ban Quản Trị (Admin)</h2>
    </main>