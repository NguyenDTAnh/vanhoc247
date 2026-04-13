<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muse Community | VanHoc247</title>
    
    <!-- Bootstrap 5 & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">

    <style>
        :root {
            --nav-bg: rgba(5, 5, 5, 0.9);
            --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            --nav-border: rgba(255, 255, 255, 0.08);
            --text-gray: rgba(255, 255, 255, 0.65);
        }

        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--nav-border);
            padding: 12px 0;
            transition: all 0.3s ease;
        }

        /* LOGO CHỮ MUSE MỚI */
        .navbar-brand-muse {
            font-size: 1.8rem;
            font-weight: 900;
            background: var(--muse-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1.5px;
            text-decoration: none !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .nav-link {
            color: var(--text-gray) !important;
            font-weight: 500;
            font-size: 0.95rem;
            transition: 0.3s;
            padding: 8px 15px !important;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff !important;
        }

        /* Hiệu ứng gạch chân hiện đại */
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--muse-gradient);
            transition: 0.3s;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 25px;
        }

        /* Chat AI đặc biệt */
        .text-gradient-nav {
            background: var(--muse-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .badge-ai {
            font-size: 0.6rem;
            padding: 2px 5px;
            background: #00d2ff;
            color: #000;
            border-radius: 4px;
            margin-left: 4px;
            vertical-align: top;
            font-weight: 800;
        }

        /* User UI */
        .user-pill {
            background: rgba(255,255,255,0.05);
            padding: 5px 12px !important;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: 0.3s;
        }
        .user-pill:hover {
            background: rgba(255,255,255,0.1);
            border-color: #f5576c;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            background: var(--muse-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            color: white;
        }

        .btn-join-muse {
            background: var(--muse-gradient);
            border: none;
            color: white !important;
            font-weight: 700;
            border-radius: 100px;
            padding: 8px 25px !important;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.2);
        }

        .btn-join-muse:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }

        .dropdown-menu {
            border: 1px solid var(--nav-border) !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <!-- Đổi sang Logo chữ MUSE -->
            <a class="navbar-brand-muse" href="index.php">MUSE</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu đẩy ra giữa bằng mx-auto -->
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">Về chúng tôi</a></li>
                    <li class="nav-item"><a class="nav-link" href="courses.php">Bài giảng</a></li>
                    <li class="nav-item"><a class="nav-link" href="classroom.php">Lớp học</a></li>
                    <li class="nav-item"><a class="nav-link" href="news.php">Tin tức</a></li>
                    <li class="nav-item"><a class="nav-link active" href="forum.php">Diễn đàn</a></li>
                    
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-gradient-nav" href="ai_chat.php">
                            <i class="fas fa-robot me-1"></i>Chat AI <span class="badge-ai">PRO</span>
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 user-pill" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                                </div>
                                <span class="d-none d-md-inline text-white small"><?php echo $_SESSION['username']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeIn shadow-lg border-0 bg-dark mt-2 p-2">
                                <li><a class="dropdown-item text-white rounded" href="profile.php"><i class="fas fa-user-circle me-2 text-info"></i>Cá nhân</a></li>
                                <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item text-white rounded" href="admin/index.php"><i class="fas fa-cogs me-2 text-warning"></i>Quản trị</a></li>
                                    <li><a class="dropdown-item text-white rounded" href="classroom.php"><i class="fas fa-video me-2 text-danger"></i>Lớp học live</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item text-white rounded" href="my-courses.php"><i class="fas fa-book me-2 text-success"></i>Kho sách</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider border-secondary opacity-25"></li>
                                <li><a class="dropdown-item text-danger rounded" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Rời khỏi</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="nav-link text-white small" href="login.php">Đăng nhập</a>
                        <a class="btn btn-join-muse" href="register.php">Gia nhập</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Script Bootstrap 5 (Giữ nguyên) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>