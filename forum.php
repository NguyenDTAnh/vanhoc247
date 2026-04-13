<?php include 'includes/header.php'; ?>
<?php 
require_once 'includes/db.php'; 

// Xử lý Form Submit Bài Viết
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_content'])) {
    $content = mysqli_real_escape_string($conn, trim($_POST['post_content']));
    if(!empty($content)) {
        // Tạm hardcode Tên user (Nếu em có Session đăng nhập, thay bằng: $_SESSION['username'])
        $author = "Gen Z Hay Chữ";
        $color = substr(md5(rand()), 0, 6); // Random màu avt để nhìn cho ảo
        
        $sql = "INSERT INTO forum_posts (author_name, avatar_color, content) VALUES ('$author', '$color', '$content')";
        mysqli_query($conn, $sql);
        
        // Trick: Dùng JS redirect để tránh bị resubmit form khi người dùng ấn F5
        echo "<script>window.location.href='forum.php';</script>";
        exit();
    }
}

// Lấy dữ liệu thật từ DB (từ mới nhất đến cũ nhất)
$res = mysqli_query($conn, "SELECT * FROM forum_posts ORDER BY created_at DESC");
$real_posts = [];
if($res) {
    while($r = mysqli_fetch_assoc($res)) {
        $real_posts[] = $r;
    }
}
?>
<style>
    :root {
        --bg-black: #050505;
        --card-bg: #121212;
        --border-color: #262626;
        --accent-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        --insta-gradient: linear-gradient(45deg, #f9ce34, #ee2a7b, #6228d7);
    }

    body { 
        background-color: var(--bg-black); 
        color: #fff; 
        font-family: 'Inter', -apple-system, sans-serif; 
        overflow-x: hidden; 
    }

    /* 1. HERO HEADER: TIÊU ĐỀ MUSE */
    .forum-hero {
        padding: 100px 0 20px;
        text-align: center;
        background: radial-gradient(circle at top, rgba(245, 87, 108, 0.08), transparent 60%);
    }
    .muse-title {
        font-size: 3.5rem; 
        font-weight: 900;
        background: var(--accent-gradient);
        -webkit-background-clip: text; 
        -webkit-text-fill-color: transparent;
        letter-spacing: -2px;
        margin-bottom: 5px;
    }

    /* 2. STORY BAR */
    .story-section { 
        border-bottom: 1px solid var(--border-color); 
        margin-bottom: 30px; 
    }
    .story-bar { 
        display: flex; gap: 20px; overflow-x: auto; padding: 15px 0; 
        scrollbar-width: none; justify-content: center; 
    }
    .story-bar::-webkit-scrollbar { display: none; }
    
    .story-item { display: flex; flex-direction: column; align-items: center; min-width: 80px; cursor: pointer; }
    .ring-wrapper {
        width: 62px; height: 62px; padding: 2px; border-radius: 50%;
        background: var(--insta-gradient); margin-bottom: 8px;
    }
    .ring-wrapper img { 
        width: 100%; height: 100%; border-radius: 50%; border: 2px solid #000; object-fit: cover; 
    }

    /* 3. TRIPLE COLUMN LAYOUT */
    .forum-grid {
        display: grid;
        grid-template-columns: 260px 1fr 320px;
        gap: 30px;
        margin-top: 20px;
        align-items: start;
    }

    /* CỘT TRÁI (LEFT SIDEBAR) */
    .left-sidebar .nav-link {
        color: #888; padding: 12px 15px; border-radius: 12px;
        transition: 0.3s; display: flex; align-items: center; gap: 12px;
        text-decoration: none; font-weight: 500;
    }
    .left-sidebar .nav-link:hover, .left-sidebar .nav-link.active {
        background: rgba(255,255,255,0.05); color: #fff;
    }
    .left-sidebar .nav-link i { font-size: 1.1rem; width: 20px; }

    /* CỘT GIỮA (MAIN FEED) */
    .main-feed { width: 100%; max-width: 650px; margin: 0 auto; }
    
    .quick-box {
        background: var(--card-bg); border: 1px solid var(--border-color);
        border-radius: 20px; padding: 18px; margin-bottom: 25px;
    }
    .post-thread { 
        border-bottom: 1px solid var(--border-color); 
        padding: 25px 0; 
        transition: 0.2s;
    }
    .post-thread:hover { background: rgba(255,255,255,0.01); }

    .thread-line-container { display: flex; flex-direction: column; align-items: center; }
    .line { width: 2px; flex-grow: 1; background: var(--border-color); margin: 8px 0; border-radius: 2px; }

    /* CỘT PHẢI (SIDEBAR) */
    .right-sidebar { position: sticky; top: 100px; }
    .side-card {
        background: var(--card-bg); border-radius: 24px; 
        border: 1px solid var(--border-color); padding: 24px; margin-bottom: 20px;
    }

    .btn-follow {
        background: #fff; color: #000; border-radius: 100px; 
        font-weight: 700; font-size: 0.75rem; padding: 5px 15px; border: none;
    }

    .trend-item { padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.03); }
    .trend-item:last-child { border: none; }

    @media (max-width: 1200px) {
        .forum-grid { grid-template-columns: 1fr; }
        .left-sidebar, .right-sidebar { display: none; }
        .main-feed { max-width: 100%; }
    }
</style>

<!-- HEADER SECTION -->
<div class="forum-hero">
    <div class="container text-center">
        <h1 class="muse-title">Muse Community</h1>
        <p class="text-white-50">Cộng đồng Văn học lớn nhất dành cho Gen Z</p>
    </div>
</div>

<!-- STORY BAR (FIXED KEY ERROR) -->
<div class="story-section">
    <div class="container">
        <div class="story-bar">
            <?php 
            $stories = [
                ['n' => 'Sự kiện', 'c' => 'f9ce34'],
                ['n' => 'Thầy Dũng', 'c' => '4facfe'],
                ['n' => '2k8 Team', 'c' => 'f5576c'],
                ['n' => 'Tài liệu', 'c' => '333'],
                ['n' => 'Góc Thơ', 'c' => 'f093fb'],
                ['n' => 'Luyện đề', 'c' => '00f2fe']
            ];
            foreach($stories as $s): ?>
            <div class="story-item">
                <div class="ring-wrapper">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s['n']); ?>&background=<?php echo $s['c']; ?>&color=fff">
                </div>
                <small class="text-white-50" style="font-size: 0.7rem;"><?php echo $s['n']; ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container-fluid px-lg-5">
    <div class="forum-grid">
        
        <!-- CỘT TRÁI: ĐIỀU HƯỚNG -->
        <aside class="left-sidebar sticky-top" style="top: 100px;">
            <nav class="d-flex flex-column gap-1">
                <a href="#" class="nav-link active"><i class="fas fa-home"></i> Trang chủ</a>
                <a href="#" class="nav-link"><i class="fas fa-fire"></i> Phổ biến</a>
                <a href="#" class="nav-link"><i class="fas fa-bookmark"></i> Đã lưu</a>
                <a href="#" class="nav-link"><i class="fas fa-user-circle"></i> Hồ sơ</a>
                <hr class="border-secondary opacity-25">
                <p class="small text-white-25 ps-3 text-uppercase" style="font-size: 0.65rem;">Chủ đề của bạn</p>
                <a href="#" class="nav-link text-white-50 small"># PhanTichLop12</a>
                <a href="#" class="nav-link text-white-50 small"># HocVanMoiNgay</a>
                <a href="#" class="nav-link text-white-50 small"># NghiLuanXaHoi</a>
            </nav>
        </aside>

        <!-- CỘT GIỮA: FEED CHÍNH -->
        <main class="main-feed">
            <!-- Composer -->
            <form action="" method="POST" class="quick-box d-flex align-items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=U&background=f5576c&color=fff" class="rounded-circle" width="40">
                <input type="text" name="post_content" required autocomplete="off" class="bg-transparent border-0 text-white flex-grow-1" placeholder="Bạn đang nghĩ gì?" style="outline:none;">
                <button type="submit" class="btn btn-sm px-4 fw-bold rounded-pill text-white shadow-sm" style="background: var(--accent-gradient);">Đăng</button>
            </form>

            <!-- Posts Loop -->
            <?php if(empty($real_posts)): ?>
                <div class="text-center py-5 text-white-50">
                    <i class="fas fa-feather fa-3x mb-3 opacity-50"></i>
                    <p>Hãy là người đầu tiên bóc tem Forum này!</p>
                </div>
            <?php else: ?>
                <?php foreach($real_posts as $post): ?>
                <div class="post-thread">
                    <div class="d-flex gap-3">
                        <div class="thread-line-container">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($post['author_name']); ?>&background=<?php echo $post['avatar_color']; ?>&color=fff" class="rounded-circle" width="46" height="46">
                            <div class="line"></div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 small"><?php echo htmlspecialchars($post['author_name']); ?> <i class="fas fa-check-circle text-primary ms-1" style="font-size: 0.6rem;"></i></h6>
                                <span class="text-white-50 small" style="font-size: 0.7rem;"><?php echo date('H:i d/m', strtotime($post['created_at'])); ?></span>
                            </div>
                            <p class="mt-2 mb-3 text-white-90 fs-6 fw-light lh-base">
                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                            </p>
                            <div class="d-flex gap-4 text-white-50">
                                <span class="small cursor-pointer"><i class="far fa-heart me-1"></i> <?php echo $post['likes']; ?></span>
                                <span class="small cursor-pointer"><i class="far fa-comment me-1"></i> <?php echo $post['comments']; ?></span>
                                <span class="small cursor-pointer"><i class="fas fa-retweet me-1"></i></span>
                                <span class="small cursor-pointer"><i class="far fa-paper-plane"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <!-- CỘT PHẢI: XU HƯỚNG & GỢI Ý -->
        <aside class="right-sidebar">
            <div class="side-card">
                <h6 class="fw-bold mb-4 small">Gợi ý theo dõi</h6>
                <?php $users = ['van_mau_club', 'tieng_viet_giau_dep', 'thi_thu_online'];
                foreach($users as $u): ?>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name=<?php echo $u; ?>&background=random" class="rounded-circle" width="34">
                        <span class="fw-bold" style="font-size: 0.8rem;"><?php echo $u; ?></span>
                    </div>
                    <button class="btn-follow">Theo dõi</button>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="side-card">
                <h6 class="fw-bold mb-3 small">Xu hướng Muse 🔥</h6>
                <div class="trend-item">
                    <div class="text-white-50" style="font-size: 0.65rem;">Văn học 12</div>
                    <div class="fw-bold small">#PhanTichSong</div>
                    <div class="text-white-50" style="font-size: 0.65rem;">1.8k bài viết</div>
                </div>
                <div class="trend-item">
                    <div class="text-white-50" style="font-size: 0.65rem;">Đang thịnh hành</div>
                    <div class="fw-bold small">#OnThiTHPT2026</div>
                    <div class="text-white-50" style="font-size: 0.65rem;">4.5k bài viết</div>
                </div>
                <div class="trend-item">
                    <div class="text-white-50" style="font-size: 0.65rem;">Thơ ca</div>
                    <div class="fw-bold small">#XuanQuynh</div>
                    <div class="text-white-50" style="font-size: 0.65rem;">920 bài viết</div>
                </div>
            </div>

            <div class="px-3">
                <p class="text-white-25" style="font-size: 0.7rem;">
                    © 2026 Muse Community. <br>
                    Phát triển bởi VanHoc247 Team.
                </p>
            </div>
        </aside>

    </div>
</div>

<?php include 'includes/footer.php'; ?>