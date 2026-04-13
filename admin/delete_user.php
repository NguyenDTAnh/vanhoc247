<?php
session_start();
require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: users_list.php?msg=deleted");
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
} else {
    header("Location: users_list.php");
}
?>