<?php 
include '../includes/db.php'; 
// Đảm bảo mày đã có session_start() và kiểm tra quyền admin ở đây
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Vanhoc247</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin_style.css"> <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; }
        .nav-link { color: rgba(255,255,255,.75); padding: 12px 20px; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .main-content { padding: 30px; }
        .card { border: none; border-radius: 12px; }
        .table thead th { background-color: #f8f9fa; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; color: #6c757d; border-bottom: none; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
            <div class="p-4 text-center">
                <img src="../assets/img/logo.png" alt="Logo" style="height: 40px; filter: brightness(0) invert(1);">
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i> Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_courses.php"><i class="fas fa-book me-2"></i> Bài giảng</a></li>
                <li class="nav-item"><a class="nav-link active" href="manage_news.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users me-2"></i> Người dùng</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Thoát</a></li>
            </ul>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h3 fw-bold">Quản lý Tin tức</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_news.php" class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm">
                        <i class="fas fa-plus me-1"></i> Đăng tin mới
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary-subtle text-primary p-3 rounded-3 me-3">
                                <i class="fas fa-file-alt fa-lg"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Tổng số bài viết</small>
                                <span class="h4 fw-bold mb-0">
                                    <?php 
                                    $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM news");
                                    echo mysqli_fetch_assoc($count)['total'];
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Ảnh bìa</th>
                                    <th>Tiêu đề & Tóm tắt</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "SELECT * FROM news ORDER BY created_at DESC";
                                $result = mysqli_query($conn, $sql);
                                if($result && mysqli_num_rows($result) > 0):
                                    while($row = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted small">#<?= $row['id']; ?></td>
                                    <td>
                                        <img src="../assets/img/news/<?= $row['image']; ?>" class="rounded-2" style="width: 60px; height: 40px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/60x40'">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= mb_strimwidth($row['title'], 0, 60, "..."); ?></div>
                                        <small class="text-muted"><?= mb_strimwidth($row['summary'], 0, 80, "..."); ?></small>
                                    </td>
                                    <td><small><?= date('d/m/Y', strtotime($row['created_at'])); ?></small></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="edit_news.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-secondary border-0"><i class="fas fa-edit"></i></a>
                                            <a href="delete_news.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Xác nhận xóa bài viết?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Không tìm thấy bài viết nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>