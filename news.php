<?php include 'includes/header.php'; ?>

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
                    <div class="filter-chip active">Tất cả</div>
                    <div class="filter-chip">Kỳ thi THPT</div>
                    <div class="filter-chip">Văn học 24/7</div>
                    <div class="filter-chip">Thông báo</div>
                    <div class="filter-chip">Sự kiện</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 mt-4">
    <div class="row g-4">
        <!-- PHẦN NỘI DUNG GIỮ NGUYÊN NHƯ BẢN TRƯỚC -->
        <div class="col-lg-8">
            <div class="featured-news mb-5">
                <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=1200" alt="Main News">
                <div class="featured-overlay">
                    <span class="badge bg-danger mb-3 px-3 py-2 fw-bold">TIN NÓNG</span>
                    <h2 class="display-6 fw-bold text-white mb-3">Bộ Giáo dục công bố định hướng ôn tập môn Văn 2026</h2>
                    <p class="text-white-50 mb-4" style="max-width: 600px;">Những thay đổi quan trọng trong cách ra đề nghị luận xã hội và phương pháp chấm điểm bài viết sáng tạo.</p>
                    <a href="#" class="btn btn-light rounded-pill px-5 py-2 fw-bold">Đọc ngay</a>
                </div>
            </div>

            <div class="row g-4">
                <?php for($i=0; $i<4; $i++): ?>
                <div class="col-md-6">
                    <div class="news-card">
                        <img src="https://picsum.photos/600/400?random=<?php echo $i; ?>" class="news-thumb" alt="News">
                        <div class="p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-info bg-opacity-10 text-info px-3">Học tập</span>
                                <span class="small text-white-50">10/04/2026</span>
                            </div>
                            <h5 class="fw-bold mb-3 text-white">Bí kíp đạt điểm 9+ môn Văn cực đơn giản</h5>
                            <p class="small text-white-50 mb-4">Cách tối ưu thời gian làm bài thi và cách lập dàn ý thông minh...</p>
                            <a href="#" class="text-white fw-bold text-decoration-none small">Xem thêm <i class="fas fa-chevron-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="trending-widget mb-4">
                <h5 class="fw-bold mb-4 text-white"><i class="fas fa-bolt text-warning me-2"></i> Xu hướng</h5>
                <div class="d-flex flex-column gap-4">
                    <div class="d-flex gap-3">
                        <span class="trend-number">01</span>
                        <a href="#" class="text-white text-decoration-none fw-bold small">Top 10 tác phẩm trọng tâm 2026</a>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="trend-number">02</span>
                        <a href="#" class="text-white text-decoration-none fw-bold small">Bí kíp mở bài 'vạn người mê'</a>
                    </div>
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