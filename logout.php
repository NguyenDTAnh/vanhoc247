<?php
session_start();
session_unset();    // Xóa tất cả các biến session
session_destroy();  // Hủy session hoàn toàn
header("Location: index.php"); // Quay về trang chủ
exit();
?>