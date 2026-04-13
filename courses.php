<?php include 'includes/header.php'; ?>

<style>
    :root {
        --netflix-red: #E50914;
        --accent-purple: #a855f7;
    }

    body { background-color: #050507; color: white; overflow-x: hidden; }

    /* 1. BIG TITLES */
    .huge-title {
        font-size: clamp(3rem, 8vw, 5rem);
        font-weight: 900;
        letter-spacing: -2px;
        line-height: 1;
        margin-top: 120px;
    }

    /* 2. MODE SWITCHER */
    .mode-toggle-container {
        margin: 40px 0;
        display: flex;
        justify-content: center;
    }
    .switcher-pill {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 5px;
        border-radius: 100px;
        display: flex;
        backdrop-filter: blur(10px);
    }
    .switch-btn {
        padding: 12px 35px;
        border-radius: 100px;
        border: none;
        background: transparent;
        color: #888;
        font-weight: 700;
        transition: 0.4s;
    }
    .switch-btn.active {
        background: white;
        color: black;
        box-shadow: 0 0 20px rgba(255,255,255,0.2);
    }

    /* 3. SPOTLIGHT (Featured Video) */
    .spotlight-card {
        position: relative;
        height: 500px;
        border-radius: 30px;
        overflow: hidden;
        margin-bottom: 60px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .spotlight-img {
        width: 100%; height: 100%; object-fit: cover;
        opacity: 0.6;
    }
    .spotlight-content {
        position: absolute;
        bottom: 0; left: 0;
        padding: 60px;
        width: 100%;
        background: linear-gradient(to top, #050507 20%, transparent 100%);
    }

    /* 4. NETFLIX ROWS */
    .nf-row { margin-bottom: 40px; position: relative; }
    .nf-row-header {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .nf-scroll {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding: 20px 0;
        scroll-behavior: smooth;
        scrollbar-width: none;
    }
    .nf-scroll::-webkit-scrollbar { display: none; }

    .nf-item {
        flex: 0 0 300px;
        border-radius: 12px;
        overflow: hidden;
        transition: 0.4s cubic-bezier(0.2, 1, 0.3, 1);
        position: relative;
    }
    .nf-item:hover {
        transform: scale(1.1) translateY(-10px);
        z-index: 50;
        box-shadow: 0 20px 40px rgba(0,0,0,0.6);
    }
    .nf-item img { width: 100%; height: 170px; object-fit: cover; }

    /* Progress bar cho video đang xem */
    .watching-progress {
        position: absolute; bottom: 0; left: 0; height: 4px;
        background: var(--netflix-red);
        box-shadow: 0 0 10px var(--netflix-red);
    }

    /* 5. SEARCH BOX */
    .modern-search-bar {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 15px;
        padding: 12px 25px;
        color: white;
        width: 100%;
        max-width: 500px;
        outline: none;
    }
</style>

<div class="container pb-5">
    <!-- TIÊU ĐỀ KHỔNG LỒ -->
    <div class="text-center">
        <h1 class="huge-title">XEM BẤT TẬN<br><span class="text-gradient">HỌC KHÔNG GIỚI HẠN</span></h1>
        
        <!-- THANH TÌM KIẾM -->
        <div class="mt-4">
            <input type="text" class="modern-search-bar" placeholder="Bạn muốn học tác phẩm nào hôm nay?">
        </div>

        <!-- BỘ CHUYỂN ĐỔI -->
        <div class="mode-toggle-container">
            <div class="switcher-pill">
                <button id="btn-video" class="switch-btn active" onclick="switchContent('video')">BÀI GIẢNG VIDEO</button>
                <button id="btn-pdf" class="switch-btn" onclick="switchContent('pdf')">KHO TÀI LIỆU PDF</button>
            </div>
        </div>
    </div>

    <!-- NỘI DUNG VIDEO -->
    <div id="video-area">
        <!-- 1. VIDEO NỔI BẬT (SPOTLIGHT) -->
        <div class="spotlight-card">
            <img src="https://images.unsplash.com/photo-1505664194779-8beaceb93744?q=80&w=1500" class="spotlight-img" alt="Spotlight">
            <div class="spotlight-content">
                <span class="badge bg-danger px-3 py-2 mb-3">SỐ 1 HÔM NAY</span>
                <h2 class="display-4 fw-bold mb-3">Vợ Chồng A Phủ: Bản 4K</h2>
                <p class="text-white-50 fs-5 mb-4" style="max-width: 600px;">Trải nghiệm hành trình tự giải thoát của nhân vật Mị qua công nghệ tái hiện bối cảnh 3D và phân tích chuyên sâu.</p>
                <div class="d-flex gap-3">
                    <button class="btn btn-light btn-lg px-5 fw-bold"><i class="fas fa-play me-2"></i>Xem ngay</button>
                    <button class="btn btn-secondary btn-lg px-4 bg-opacity-25 border-0"><i class="fas fa-plus me-2"></i>Danh sách</button>
                </div>
            </div>
        </div>

        <!-- 2. VIDEO ĐANG XEM (CONTINUE WATCHING) -->
        <div class="nf-row">
            <div class="nf-row-header">
                <span>Tiếp tục xem cho Admin Muse</span>
            </div>
            <div class="nf-scroll">
                <div class="nf-item">
                    <img src="https://images.unsplash.com/photo-1512149177596-f817c7ef5d4c?w=600" alt="Video">
                    <div class="watching-progress" style="width: 70%;"></div>
                    <div class="p-3 bg-dark">
                        <h6 class="mb-0">Tây Tiến - Quang Dũng</h6>
                        <small class="text-white-50">Còn 10 phút</small>
                    </div>
                </div>
                <div class="nf-item">
                    <img src="https://images.unsplash.com/photo-1457369804613-52c61a468e7d?w=600" alt="Video">
                    <div class="watching-progress" style="width: 30%;"></div>
                    <div class="p-3 bg-dark">
                        <h6 class="mb-0">Lý luận văn học: Tư duy sáng tạo</h6>
                        <small class="text-white-50">Còn 45 phút</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. DANH MỤC LỚP 12 -->
        <div class="nf-row">
            <div class="nf-row-header">
                <span>Ngữ Văn 12: Chương trình mới</span>
                <a href="#" class="text-white-50 small text-decoration-none">Xem tất cả ></a>
            </div>
            <div class="nf-scroll">
                <!-- Thẻ phim mẫu -->
                <div class="nf-item"><img src="https://images.unsplash.com/photo-1518818494391-3841963e2e7b?w=600"><div class="p-3 bg-dark"><h6>Đất Nước</h6><small class="text-info">Trending</small></div></div>
                <div class="nf-item"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600"><div class="p-3 bg-dark"><h6>Việt Bắc</h6><small class="text-info">9.8/10</small></div></div>
                <div class="nf-item"><img src="https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d?w=600"><div class="p-3 bg-dark"><h6>Sóng - Xuân Quỳnh</h6><small class="text-info">Mới cập nhật</small></div></div>
                <div class="nf-item"><img src="https://images.unsplash.com/photo-1534067783941-51c9c23ecefd?w=600"><div class="p-3 bg-dark"><h6>Chiếc thuyền ngoài xa</h6><small class="text-info">Xếp hạng #2</small></div></div>
            </div>
        </div>
    </div>

    <!-- NỘI DUNG PDF (ẨN MẶC ĐỊNH) -->
    <div id="pdf-area" style="display: none;">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card p-4 text-center">
                    <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                    <h5 class="fw-bold">Sơ đồ tư duy Sóng</h5>
                    <p class="text-white-50 small">Định dạng: PDF | 5.0 MB</p>
                    <button class="btn btn-outline-light rounded-pill w-100 mt-3">Tải ngay</button>
                </div>
            </div>
            <!-- Lặp lại các card PDF tương tự -->
        </div>
    </div>
</div>

<script>
    function switchContent(type) {
        const videoArea = document.getElementById('video-area');
        const pdfArea = document.getElementById('pdf-area');
        const btnVideo = document.getElementById('btn-video');
        const btnPdf = document.getElementById('btn-pdf');

        if (type === 'video') {
            videoArea.style.display = 'block';
            pdfArea.style.display = 'none';
            btnVideo.classList.add('active');
            btnPdf.classList.remove('active');
        } else {
            videoArea.style.display = 'none';
            pdfArea.style.display = 'block';
            btnPdf.classList.add('active');
            btnVideo.classList.remove('active');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>