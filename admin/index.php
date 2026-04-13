<?php
session_start();
require_once '../includes/db.php';

// 1. Đếm học viên
$res_students = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'student'");
$students_count = mysqli_fetch_assoc($res_students)['total'] ?? 0;

// 2. Đếm khóa học
$res_courses = mysqli_query($conn, "SELECT COUNT(*) as total FROM courses");
$courses_count = mysqli_fetch_assoc($res_courses)['total'] ?? 0;

// 3. Đếm bài viết (Fix lỗi dòng 16 - kiểm tra bảng thực tế)
$res_forum = mysqli_query($conn, "SELECT COUNT(*) as total FROM posts");
$pending_posts = ($res_forum) ? mysqli_fetch_assoc($res_forum)['total'] : 0;

// 4. Lấy người dùng mới
$recent_activities = mysqli_query($conn, "SELECT username, created_at FROM users ORDER BY id DESC LIMIT 5");

$stats = [
    'students' => number_format($students_count),
    'courses'  => $courses_count,
    'pending_posts' => $pending_posts,
    'live_now' => 2 
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MUSE OS | Trung Tâm Điều Hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg-body: #050508; --accent: #3d8bff; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.08); }
        body { 
            background: var(--bg-body); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; overflow-x: hidden; min-height: 100vh; position: relative; 
        }
        /* Gradient Background lan tỏa */
        body::before { content: ""; position: fixed; top: -10%; right: -5%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(61, 139, 255, 0.15) 0%, rgba(5, 5, 8, 0) 70%); z-index: -1; }
        body::after { content: ""; position: fixed; bottom: -10%; left: 5%; width: 700px; height: 700px; background: radial-gradient(circle, rgba(168, 85, 247, 0.1) 0%, rgba(5, 5, 8, 0) 70%); z-index: -1; }
        
        body::-webkit-scrollbar { display: none; }
        .main-content { margin-left: 280px; padding: 50px; }
        .stat-card { background: var(--glass); backdrop-filter: blur(25px); border: 1px solid var(--border); border-radius: 24px; padding: 25px; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--accent); }
        .progress { background: rgba(255,255,255,0.05); height: 6px; border-radius: 10px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content">
        <header class="mb-5">
            <h1 class="fw-800">Trung Tâm Điều Hành</h1>
            <p style="color: #64748b;">Hệ thống đang hoạt động ổn định. Chào mừng quay trở lại!</p>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Học Viên</div>
                    <div class="h3 fw-800 m-0"><?php echo $stats['students']; ?></div>
                    <div class="small text-success mt-2"><i class="fas fa-caret-up"></i> Tăng trưởng thực</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Khóa Học Hiện Có</div>
                    <div class="h3 fw-800 m-0"><?php echo $stats['courses']; ?></div>
                    <div class="small text-dim mt-2">Dữ liệu từ database</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Thảo luận mới</div>
                    <div class="h3 fw-800 m-0"><?php echo $stats['pending_posts']; ?></div>
                    <div class="small text-warning mt-2">Bài viết diễn đàn</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color: #f43f5e;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Đang trực tuyến</div>
                    <div class="h3 fw-800 m-0" style="color: #f43f5e;"><?php echo $stats['live_now']; ?> Live</div>
                    <div class="small text-dim mt-2">Phòng học ảo Muse</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="stat-card">
                    <h5 class="fw-800 mb-4">Người dùng vừa gia nhập</h5>
                    <?php while($user = mysqli_fetch_assoc($recent_activities)): ?>
                    <div class="d-flex align-items-center p-3 border-bottom border-secondary border-opacity-25 gap-3">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #a855f7, #3d8bff); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px;"><?php echo strtoupper(substr($user['username'], 0, 2)); ?></div>
                        <div class="flex-grow-1">
                            <div class="fw-800 small"><?php echo $user['username']; ?></div>
                            <div style="font-size: 11px; color: #64748b;"><?php echo date('H:i - d/m/Y', strtotime($user['created_at'])); ?></div>
                        </div>
                        <span class="badge bg-success" style="font-size: 9px;">ACTIVE</span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5 class="fw-800 mb-4">Chỉ số hệ thống</h5>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small mb-1"><span>Dung lượng Server</span><span>45%</span></div>
                        <div class="progress"><div class="progress-bar bg-primary" style="width: 45%"></div></div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small mb-1"><span>Tỉ lệ hoàn thành</span><span>82%</span></div>
                        <div class="progress"><div class="progress-bar bg-success" style="width: 82%"></div></div>
                    </div>
                    <div class="text-center mt-4">
                        <p class="small text-secondary">Phiên bản MuseOS v2.0.4</p>
                        <button class="btn btn-sm btn-outline-secondary w-100">Kiểm tra cập nhật</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>