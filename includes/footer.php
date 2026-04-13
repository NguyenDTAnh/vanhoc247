<style>
    :root {
        --footer-bg: rgba(10, 10, 12, 0.95);
        --footer-border: rgba(255, 255, 255, 0.05);
        --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
    }

    .muse-footer {
        background-color: var(--footer-bg);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--footer-border);
        padding: 70px 0 30px;
        margin-top: 60px;
        color: #999;
    }

    /* Đổi Logo thành chữ MUSE nghệ thuật */
    .footer-logo-text {
        font-size: 2.2rem;
        font-weight: 900;
        background: var(--muse-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-decoration: none;
        letter-spacing: -2px;
        display: inline-block;
        margin-bottom: 20px;
    }

    .footer-header {
        color: #fff;
        font-weight: 700;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 25px;
        position: relative;
    }
    
    .footer-header::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 30px;
        height: 2px;
        background: var(--muse-gradient);
    }

    .info-list { list-style: none; padding: 0; }
    .info-item {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    .info-item i {
        color: #f5576c;
        margin-top: 4px;
    }

    .footer-link {
        color: #888;
        text-decoration: none;
        display: block;
        margin-bottom: 12px;
        font-size: 0.9rem;
        transition: 0.3s ease;
    }

    .footer-link:hover {
        color: #fff;
        padding-left: 8px;
    }

    .social-group { display: flex; gap: 12px; margin-top: 25px; }
    .social-box {
        width: 38px; height: 38px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--footer-border);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; transition: 0.3s; text-decoration: none;
    }
    .social-box:hover {
        background: var(--muse-gradient);
        transform: translateY(-3px);
        border-color: transparent;
    }

    .footer-bottom-bar {
        border-top: 1px solid var(--footer-border);
        padding-top: 30px;
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .footer-bottom-bar { flex-direction: column; gap: 15px; text-align: center; }
    }
</style>

<footer class="muse-footer">
    <div class="container">
        <div class="row g-5">
            <!-- Cột 1: Logo & Giới thiệu -->
            <div class="col-lg-4 col-md-12">
                <a href="index.php" class="footer-logo-text">MUSE</a>
                <p class="pe-lg-5">
                    Nền tảng kết nối cộng đồng yêu văn học, nơi chia sẻ tri thức và lan tỏa những giá trị nhân văn sâu sắc. Đồng hành cùng sĩ tử 2k8 trên con đường chinh phục ước mơ.
                </p>
                <div class="social-group">
                    <a href="#" class="social-box"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-box"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-box"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-box"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Cột 2: Thông tin liên hệ (MỚI) -->
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-header">Thông tin liên hệ</h6>
                <div class="info-list">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Tòa nhà VanHoc247, Số 123 Đường Văn Chương, Quận Cầu Giấy, Hà Nội</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>Hotline: 0988.XXX.XXX (Hỗ trợ 24/7)</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email: contact@musecommunity.vn</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>Giờ làm việc: 08:00 - 22:00 hàng ngày</span>
                    </div>
                </div>
            </div>

            <!-- Cột 3: Liên kết nhanh -->
            <div class="col-lg-2 col-md-3">
                <h6 class="footer-header">Khám phá</h6>
                <a href="forum.php" class="footer-link">Cộng đồng</a>
                <a href="#" class="footer-link">Thư viện bài viết</a>
                <a href="#" class="footer-link">Kho tài liệu</a>
                <a href="#" class="footer-link">Gia nhập đội ngũ</a>
            </div>

            <!-- Cột 4: Pháp lý -->
            <div class="col-lg-2 col-md-3">
                <h6 class="footer-header">Pháp lý</h6>
                <a href="#" class="footer-link">Điều khoản sử dụng</a>
                <a href="#" class="footer-link">Chính sách bảo mật</a>
                <a href="#" class="footer-link">Quy tắc cộng đồng</a>
                <a href="#" class="footer-link">Khiếu nại</a>
            </div>
        </div>

        <div class="footer-bottom-bar">
            <div>
                © 2026 <strong>Muse Community</strong>. All rights reserved.
            </div>
            <div class="text-white-50">
                Design by <span style="color: #f5576c;">VanHoc247 Team</span> with Love <i class="fas fa-heart"></i>
            </div>
        </div>
    </div>
</footer>

<!-- Đảm bảo Font Awesome đã được tải -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">