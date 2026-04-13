<?php include 'includes/header.php'; ?>

<style>
    :root {
        --neon-blue: #00d2ff;
        --neon-purple: #9d50bb;
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
    }

    body { background: #050508; color: #e0e0e0; font-family: 'Plus Jakarta Sans', sans-serif; }

    /* 1. HERO GLASS BANNER */
    .glass-hero {
        background: linear-gradient(135deg, rgba(0, 210, 255, 0.1), rgba(157, 80, 187, 0.1));
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        padding: 60px;
        margin-top: 100px;
        backdrop-filter: blur(20px);
        position: relative;
        overflow: hidden;
    }
    .glass-hero::before {
        content: ''; position: absolute; top: -50%; left: -20%; width: 400px; height: 400px;
        background: radial-gradient(circle, var(--neon-blue), transparent 70%);
        opacity: 0.1; filter: blur(50px);
    }

    /* 2. LIVE SECTION - ĐỘT PHÁ THỊ GIÁC */
    .live-status-pill {
        background: rgba(255, 75, 92, 0.1);
        color: #ff4b5c;
        padding: 6px 15px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.8rem;
        border: 1px solid rgba(255, 75, 92, 0.2);
    }

    .main-live-card {
        background: rgba(10, 10, 15, 0.6);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        overflow: hidden;
        transition: 0.4s;
    }
    .live-preview {
        height: 250px;
        background: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800') center/cover;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .play-btn-ripple {
        width: 60px; height: 60px;
        background: #fff; color: #000;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 20px rgba(255,255,255,0.4);
        animation: ripple 2s infinite;
    }

    @keyframes ripple {
        0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
        70% { box-shadow: 0 0 0 20px rgba(255,255,255,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
    }

    /* 3. SOCIAL GRID */
    .platform-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 30px;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .platform-card:hover {
        background: rgba(255,255,255,0.06);
        transform: translateY(-10px);
        border-color: var(--neon-blue);
    }

    /* 4. UPCOMING LIST */
    .upcoming-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: rgba(255,255,255,0.02);
        border-radius: 18px;
        border: 1px solid transparent;
        transition: 0.3s;
    }
    .upcoming-item:hover {
        background: rgba(255,255,255,0.05);
        border-color: var(--glass-border);
    }
    .date-box {
        width: 70px; height: 70px;
        background: linear-gradient(135deg, var(--neon-blue), var(--neon-purple));
        border-radius: 15px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        font-weight: 800; line-height: 1.2;
    }

    .btn-gradient {
        background: linear-gradient(135deg, var(--neon-blue), var(--neon-purple));
        border: none; color: white; font-weight: 700;
        border-radius: 12px; padding: 12px 30px;
    }
</style>

<div class="container pb-5">
    <!-- 1. HEADER HERO -->
    <div class="glass-hero text-center">
        <div class="live-status-pill d-inline-block mb-3">
            <i class="fas fa-circle me-1 small"></i> 24/7 LIVE SUPPORT
        </div>
        <h1 class="display-3 fw-black mb-3 text-white">Muse <span class="text-info">Live</span> Academy</h1>
        <p class="text-white-50 fs-5 mb-0 mx-auto" style="max-width: 700px;">
            Không gian học tập tương tác thế hệ mới. Trực quan - Đột phá - Hiệu quả.
        </p>
    </div>

    <div class="row mt-5 g-4">
        <!-- CỘT TRÁI: LIVE & SCHEDULE -->
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4 d-flex align-items-center">
                <i class="fas fa-video text-danger me-2"></i> Lớp Học Đang Diễn Ra
            </h4>
            
            <div class="main-live-card mb-5">
                <div class="live-preview">
                    <div class="play-btn-ripple">
                        <i class="fas fa-play"></i>
                    </div>
                    <div class="position-absolute bottom-0 start-0 p-4">
                        <span class="badge bg-danger mb-2">LIVE</span>
                    </div>
                </div>
                <div class="p-4 bg-dark bg-opacity-50 border-top border-secondary">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h3 class="fw-bold text-white mb-1">Chữa đề thi THPTQG 2026 - Kỹ năng NLVH</h3>
                            <p class="text-white-50 mb-0">Thầy Nguyễn Văn A • 1,240 học viên đang online</p>
                        </div>
                        <a href="#" class="btn btn-gradient btn-lg">Vào lớp ngay</a>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-4 mt-5">Lộ trình học trực tiếp tuần này</h4>
            <div class="d-flex flex-column gap-3">
                <div class="upcoming-item">
                    <div class="date-box text-white">
                        <span class="small opacity-75">T7</span>
                        <span>20</span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-white mb-1">Phân tích chuyên sâu "Vợ Nhặt" - Kim Lân</h6>
                        <p class="text-white-50 small mb-0">Focus: Tâm lý nhân vật & Tư duy phê bình</p>
                    </div>
                    <button class="btn btn-outline-info btn-sm rounded-pill px-4">Đăng ký lịch</button>
                </div>

                <div class="upcoming-item">
                    <div class="date-box text-white" style="filter: hue-rotate(45deg);">
                        <span class="small opacity-75">CN</span>
                        <span>21</span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-white mb-1">Chiến thuật 9+ Nghị luận xã hội</h6>
                        <p class="text-white-50 small mb-0">Dành cho học sinh khối 12 mục tiêu trường top</p>
                    </div>
                    <button class="btn btn-outline-info btn-sm rounded-pill px-4">Đăng ký lịch</button>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: SOCIAL & UTILITIES -->
        <div class="col-lg-4">
            <h4 class="fw-bold mb-4">Kết nối cộng đồng</h4>
            <div class="platform-card mb-4" style="border-left: 5px solid #1877f2;">
                <div class="d-flex align-items-center mb-3">
                    <i class="fab fa-facebook fa-2x text-primary me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">Facebook Group</h6>
                        <small class="text-white-50">50,000 Thành viên</small>
                    </div>
                </div>
                <p class="small text-white-50">Cập nhật đề thi thử từ các sở giáo dục nhanh nhất Việt Nam.</p>
                <a href="#" class="btn btn-dark w-100 rounded-pill">Tham gia Group</a>
            </div>

            <div class="platform-card mb-4" style="border-left: 5px solid #fff;">
                <div class="d-flex align-items-center mb-3">
                    <i class="fab fa-tiktok fa-2x me-3 text-white"></i>
                    <div>
                        <h6 class="fw-bold mb-0 text-white">TikTok Education</h6>
                        <small class="text-white-50">Triệu View Văn Học</small>
                    </div>
                </div>
                <p class="small text-white-50">Học văn qua nhạc và các clip ngắn dễ nhớ, dễ thuộc.</p>
                <a href="#" class="btn btn-dark w-100 rounded-pill">Follow Kênh</a>
            </div>

            <!-- TIỆN ÍCH PHỤ -->
            <div class="p-4 rounded-4 bg-info bg-opacity-5 border border-info border-opacity-10">
                <h6 class="fw-bold text-info mb-3"><i class="fas fa-bullhorn me-2"></i>Thông báo lớp học</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                        <span class="d-block fw-bold text-white">Dời lịch học tối nay</span>
                        <span class="text-white-50">Lớp Thơ sẽ dời sang 20:30 do bảo trì hệ thống.</span>
                    </li>
                    <li>
                        <span class="d-block fw-bold text-white">Tài liệu mới đã sẵn sàng</span>
                        <span class="text-white-50">Check mục PDF để tải đề minh họa mới nhất.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>