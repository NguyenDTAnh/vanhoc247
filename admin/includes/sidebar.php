<?php 
$current_page = basename($_SERVER['PHP_SELF']); 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_role = $_SESSION['role'] ?? 'student'; 
?>
<style>
    aside.sidebar { 
        width: 280px; position: fixed; height: 100vh; 
        background: rgba(8, 8, 12, 0.85); backdrop-filter: blur(20px); 
        border-right: 1px solid rgba(255,255,255,0.08); z-index: 1000;
        overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;
    }
    aside.sidebar::-webkit-scrollbar { display: none; }
    .nav-link-muse { display: flex; align-items: center; padding: 12px 20px; color: #94a3b8; text-decoration: none; gap: 12px; transition: 0.3s; border-radius: 10px; margin: 2px 15px; font-weight: 600; font-size: 14px; }
    .nav-link-muse:hover, .nav-link-muse.active { background: rgba(255, 255, 255, 0.05); color: #fff; }
    .nav-link-muse.active { color: #3d8bff; border: 1px solid rgba(61, 139, 255, 0.2); }
    .menu-label { display: block; padding: 20px 35px 8px; font-size: 10px; font-weight: 800; color: #475569; letter-spacing: 1.5px; text-transform: uppercase; }
    .has-submenu::after { content: '\f105'; font-family: 'Font Awesome 6 Free'; font-weight: 900; margin-left: auto; transition: 0.3s; font-size: 10px; }
    .has-submenu:not(.collapsed)::after { transform: rotate(90deg); color: #3d8bff; }
    .submenu { list-style: none; padding-left: 0; margin: 5px 15px; background: rgba(255,255,255,0.02); border-radius: 10px; }
    .submenu a { padding: 10px 15px 10px 45px; display: block; color: #64748b; font-size: 13px; text-decoration: none; transition: 0.3s; }
    .submenu a:hover { color: #fff; }
    .badge-muse { font-size: 9px; padding: 2px 6px; border-radius: 5px; margin-left: auto; }
</style>

<aside class="sidebar">
    <div class="sidebar-header p-4 d-flex align-items-center">
        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #a855f7, #3d8bff); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
            <i class="fas fa-graduation-cap" style="color: #fff; font-size: 18px;"></i>
        </div>
        <div style="font-size: 20px; font-weight: 800; color: #fff; letter-spacing: -1px;">MUSE<span style="color: #3d8bff;">OS</span></div>
    </div>

    <nav class="sidebar-menu pb-5">
        <a href="../index.php" class="nav-link-muse" style="background: rgba(61, 139, 255, 0.1); color: #3d8bff; margin-bottom: 15px;">
            <i class="fas fa-external-link-alt"></i> Trở về trang web
        </a>

        <a href="index.php" class="nav-link-muse <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <?php if ($user_role === 'admin'): ?>
        <a href="#userSub" data-bs-toggle="collapse" class="nav-link-muse has-submenu collapsed">
            <i class="fas fa-users"></i> Quản lý người dùng
        </a>
        <div class="collapse submenu" id="userSub">
            <a href="users_list.php">• Danh sách người dùng</a>
            <a href="roles.php">• Phân quyền & Profile</a>
            <a href="user_logs.php">• Nhật ký hoạt động</a>
        </div>
        <?php endif; ?>

        <?php if ($user_role === 'admin' || $user_role === 'teacher'): ?>
        <a href="#courseSub" data-bs-toggle="collapse" class="nav-link-muse has-submenu collapsed">
            <i class="fas fa-book"></i> Quản lý khóa học
        </a>
        <div class="collapse submenu" id="courseSub">
            <a href="courses_list.php">• Danh sách lớp học</a>
            <a href="course_assign.php">• Phân công giáo viên</a>
        </div>
        <?php endif; ?>

        <a href="#contentSub" data-bs-toggle="collapse" class="nav-link-muse has-submenu collapsed">
            <i class="fas fa-photo-video"></i> Nội dung bài giảng
        </a>
        <div class="collapse submenu" id="contentSub">
            <a href="videos.php"><i class="fas fa-play-circle me-2"></i> Video bài giảng</a>
            <a href="documents.php"><i class="fas fa-file-pdf me-2"></i> Tài liệu (PDF)</a>
        </div>

        <a href="lives.php" class="nav-link-muse">
            <i class="fas fa-broadcast-tower"></i> Livestream <span class="badge bg-danger badge-muse">LIVE</span>
        </a>

        <a href="ai_config.php" class="nav-link-muse"><i class="fas fa-robot"></i> Trợ lý học tập AI</a>

        <a href="forum.php" class="nav-link-muse">
            <i class="fas fa-comments"></i> Diễn đàn <span class="badge bg-primary badge-muse">12</span>
        </a>

        <a href="#quizSub" data-bs-toggle="collapse" class="nav-link-muse has-submenu collapsed">
            <i class="fas fa-edit"></i> Bài tập & Kiểm tra
        </a>
        <div class="collapse submenu" id="quizSub">
            <a href="quizzes.php">• Kho đề thi trắc nghiệm</a>
            <a href="grading.php">• Chấm điểm & Kết quả</a>
        </div>

        <?php if ($user_role === 'admin'): ?>
        <span class="menu-label">HỆ THỐNG</span>

        <a href="analytics.php" class="nav-link-muse"><i class="fas fa-chart-line"></i> Phân tích & Báo cáo</a>

        <a href="notifications.php" class="nav-link-muse"><i class="fas fa-bell"></i> Thông báo</a>

        <a href="billing.php" class="nav-link-muse"><i class="fas fa-wallet"></i> Gói học & VIP</a>

        <a href="settings.php" class="nav-link-muse"><i class="fas fa-cog"></i> Cài đặt hệ thống</a>
        <a href="security.php" class="nav-link-muse"><i class="fas fa-shield-alt"></i> Bảo mật & Nhật ký</a>
        <?php endif; ?>
    </nav>
</aside>