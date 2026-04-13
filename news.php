<?php 
include 'includes/header.php'; 
include_once 'includes/db.php';

$current_cat = isset($_GET['category']) ? $_GET['category'] : 'Tất cả';
?>

<style>
    :root {
        --accent-orange: #ff6b6b;
        --accent-blue: #4facfe;
        --news-card-bg: rgba(255, 255, 255, 0.03);
    }

    body { 
        background-color: #0a0a0c; 
        color: #f0f0f0; 
        font-family: 'Inter', sans-serif;
    }

    /* 1. NEWS HERO SECTION - TỐI ƯU LẠI */
    .news-header {
        padding: 120px 0 40px;
        background: radial-gradient(circle at 10% 20%, rgba(79, 172, 254, 0.08) 0%, transparent 50%);
    }

    /* THANH TÌM KIẾM ĐỘT PHÁ */
    .search-wrapper {
        position: relative;
        max-width: 500px;
    }
    .search-input {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 100px;
        padding: 12px 25px 12px 50px;
        color: white;
        width: 100%;
        transition: 0.3s;
    }
    .search-input:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--accent-blue);
        box-shadow: 0 0 15px rgba(79, 172, 254, 0.2);
        outline: none;
    }
    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.4);
    }

    /* 2. CHIPS - FIX LỖI "SỰ KIỆN" */
    .news-filters-container {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap; /* Cho phép xuống hàng nếu màn hình quá nhỏ */
        margin-top: 20px;
    }
    
    .filter-chip {
        background: var(--news-card-bg);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #aaa;
        padding: 8px 22px;
        border-radius: 100px;
        white-space: nowrap; /* Giữ chữ trên 1 dòng */
        cursor: pointer;
        transition: 0.3s;
        font-weight: 500;
        display: inline-block;
    }
    .filter-chip.active, .filter-chip:hover {
        background: #fff; color: #000; border-color: #fff;
    }

    /* Giữ nguyên các Style Card cũ ở dưới... */
    .featured-news {
        position: relative; border-radius: 30px; overflow: hidden; height: 500px;
        border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: flex-end;
    }
    .featured-news img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
    .featured-overlay { position: relative; z-index: 2; padding: 50px; background: linear-gradient(to top, #0a0a0c 20%, transparent 100%); width: 100%; }
    .news-card { background: var(--news-card-bg); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; transition: 0.3s; height: 100%; overflow: hidden; }
    .news-thumb { height: 220px; object-fit: cover; width: 100%; }
    .trending-widget { background: #111; border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; padding: 25px; }
    .trend-number { font-size: 1.8rem; font-weight: 900; background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; opacity: 0.6; }
</style>

<div class="news-header">
    <div class="container">
        <div class="row align-items-end g-4">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold text-white mb-2">Tin tức <span class="text-info">&</span> Sự kiện</h1>
                <p class="text-white-50 fs-5 mb-4">Điểm tin giáo dục và nhịp đập văn học thế hệ mới.</p>
                
                <!-- THANH TÌM KIẾM MỚI -->
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Tìm kiếm tin tức, sự kiện...">
                </div>
            </div>
            
            <div class="col-lg-5 text-lg-end">
                <!-- BOX BỘ LỌC ĐÃ FIX LỖI -->
                <div class="news-filters-container justify-content-lg-end">
                    <a href="news.php" class="filter-chip text-decoration-none <?= ($current_cat == 'Tất cả') ? 'active' : '' ?>">Tất cả</a>
                    <a href="news.php?category=Kỳ thi THPT" class="filter-chip text-decoration-none <?= ($current_cat == 'Kỳ thi THPT') ? 'active' : '' ?>">Kỳ thi THPT</a>
                    <a href="news.php?category=Văn học 24/7" class="filter-chip text-decoration-none <?= ($current_cat == 'Văn học 24/7') ? 'active' : '' ?>">Văn học 24/7</a>
                    <a href="news.php?category=Thông báo" class="filter-chip text-decoration-none <?= ($current_cat == 'Thông báo') ? 'active' : '' ?>">Thông báo</a>
                    <a href="news.php?category=Sự kiện" class="filter-chip text-decoration-none <?= ($current_cat == 'Sự kiện') ? 'active' : '' ?>">Sự kiện</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 mt-4">
    <div class="row g-4">
        <!-- PHẦN NỘI DUNG GIỮ NGUYÊN NHƯ BẢN TRƯỚC -->
        <div class="col-lg-8">
            <?php 
                $feature_where = "";
                if($current_cat !== 'Tất cả' && !empty($current_cat)) {
                    $cat_esc = mysqli_real_escape_string($conn, $current_cat);
                    $feature_where = "WHERE category = '$cat_esc'";
                }
                $feature_sql = "SELECT * FROM news $feature_where ORDER BY created_at DESC LIMIT 1";
                $feature_res = mysqli_query($conn, $feature_sql);
                $feature = mysqli_fetch_assoc($feature_res);
                if($feature):
                    $img_val = $feature['image'];
                    if (!empty($img_val)) {
                        $feat_img = (strpos($img_val, 'http') === 0) ? $img_val : "assets/img/news/" . $img_val;
                    } else {
                        $feat_img = "https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=1200";
                    }
            ?>
            <div class="featured-news mb-5">
                <img src="<?= $feat_img ?>" alt="<?= htmlspecialchars($feature['title']) ?>">
                <div class="featured-overlay">
                    <span class="badge bg-danger mb-3 px-3 py-2 fw-bold">TIN NÓNG</span>
                    <h2 class="display-6 fw-bold text-white mb-3"><?= htmlspecialchars($feature['title']) ?></h2>
                    <p class="text-white-50 mb-4" style="max-width: 600px;"><?= htmlspecialchars($feature['summary']) ?></p>
                    <a href="news_detail.php?id=<?= $feature['id'] ?>" class="btn btn-light rounded-pill px-5 py-2 fw-bold">Đọc ngay</a>
                </div>
            </div>
            <?php else: ?>
            <div class="featured-news mb-5" style="background:#222; display:flex; justify-content:center; align-items:center;">
                <p class="text-muted">Chưa có bài viết nổi bật.</p>
            </div>
            <?php endif; ?>

            <div class="row g-4">
                <?php 
                // FETCH DATA FROM DATABASE (LẤY MỚI NHẤT TRỪ THẰNG ĐẦU TIÊN)
                $where_clause = "";
                if($current_cat !== 'Tất cả' && !empty($current_cat)) {
                    $cat_esc = mysqli_real_escape_string($conn, $current_cat);
                    $where_clause = "WHERE category = '$cat_esc'";
                }

                // Thực tế lấy từ Database (4 bài mới tiếp theo)
                $list_sql = "SELECT * FROM news $where_clause ORDER BY created_at DESC LIMIT 6 OFFSET 0";
                
                // Tránh trùng bài feature nếu đang ở Tất cả và page 1
                if($current_cat === 'Tất cả') {
                    $list_sql = "SELECT * FROM news ORDER BY created_at DESC LIMIT 6 OFFSET 1";
                }

                $list_res = mysqli_query($conn, $list_sql);
                
                if($list_res && mysqli_num_rows($list_res) > 0):
                    while($row = mysqli_fetch_assoc($list_res)): 
                        $img_val2 = $row['image'];
                        if (!empty($img_val2)) {
                            $img = (strpos($img_val2, 'http') === 0) ? $img_val2 : "assets/img/news/" . $img_val2;
                        } else {
                            $img = "https://picsum.photos/600/400?random=" . $row['id'];
                        }
                ?>
                <div class="col-md-6">
                    <div class="news-card">
                        <img src="<?= $img ?>" class="news-thumb" alt="<?= htmlspecialchars($row['title']) ?>">
                        <div class="p-4 d-flex flex-column" style="height: calc(100% - 220px);">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-info bg-opacity-10 text-info px-3"><?= htmlspecialchars($row['category'] ?? 'Khác') ?></span>
                                <span class="small text-white-50"><?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                            </div>
                            <h5 class="fw-bold mb-3 text-white"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="small text-white-50 mb-4 flex-grow-1"><?= htmlspecialchars($row['summary']) ?></p>
                            <a href="news_detail.php?id=<?= $row['id'] ?>" class="text-white fw-bold text-decoration-none small mt-auto">Xem thêm <i class="fas fa-chevron-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div class="col-12"><p class="text-secondary text-center my-4">Chưa có bài viết nào khác.</p></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="trending-widget mb-4">
                <h5 class="fw-bold mb-4 text-white"><i class="fas fa-bolt text-warning me-2"></i> Xu hướng</h5>
                <div class="d-flex flex-column gap-4">
                    <?php 
                    $trend_sql = "SELECT id, title FROM news ORDER BY id ASC LIMIT 5";
                    $trend_res = mysqli_query($conn, $trend_sql);
                    $i = 1;
                    if($trend_res && mysqli_num_rows($trend_res) > 0):
                        while($tr = mysqli_fetch_assoc($trend_res)):
                    ?>
                    <div class="d-flex gap-3 align-items-center">
                        <span class="trend-number">0<?= $i++; ?></span>
                        <a href="news_detail.php?id=<?= $tr['id'] ?>" class="text-white text-decoration-none fw-bold small lh-base"><?= htmlspecialchars($tr['title']) ?></a>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="text-muted small">Đang cập nhật xu hướng...</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="p-4 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-20 text-center">
                <h5 class="fw-bold text-white mb-3">Tham gia cộng đồng</h5>
                <p class="small text-white-50 mb-4">Nhận thông báo tin tức mới nhất qua Email.</p>
                <div class="input-group mb-2">
                    <input type="email" class="form-control bg-dark border-secondary text-white small" placeholder="Email...">
                    <button class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>