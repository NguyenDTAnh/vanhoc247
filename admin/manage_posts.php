<?php 
require_once __DIR__ . '/../includes/db.php'; 

// Xử lý xóa bài viết nếu có yêu cầu
if (isset($_GET['delete_id'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql_delete = "DELETE FROM posts WHERE id = '$id_to_delete'";
    if (mysqli_query($conn, $sql_delete)) {
        header("Location: manage_posts.php?msg=deleted");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bài viết - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fc; font-family: 'Inter', sans-serif; }
        .sidebar-wrapper { min-width: 280px; background: #1a1c23; min-height: 100vh; }
        .table-box { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar-wrapper">
            <?php include 'includes/sidebar.php'; ?>
        </div>

        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Quản lý bài viết</h2>
                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success py-2 px-4 mb-0 rounded-pill">Đã xóa bài viết thành công!</div>
                <?php endif; ?>
            </div>

            <div class="table-box p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Người đăng</th>
                            <th>Nội dung</th>
                            <th>Ngày đăng</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td style="max-width: 400px;"><?php echo htmlspecialchars($row['content']); ?></td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="manage_posts.php?delete_id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-danger rounded-pill px-3" 
                                   onclick="return confirm('Mày có chắc chắn muốn xóa bài này không?')">
                                    <i class="fas fa-trash-alt me-1"></i> Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>