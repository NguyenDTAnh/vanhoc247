<?php include 'includes/header.php'; ?>

<!-- CSS bổ sung để trang About lung linh hơn -->
<style>
    :root {
        --gold-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        --emerald-glow: rgba(16, 185, 129, 0.1);
    }

    .about-hero {
        background: radial-gradient(circle at top right, var(--emerald-glow), transparent);
        padding: 100px 0 60px;
    }

    .text-gold-gradient {
        background: var(--gold-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .story-img-container {
        position: relative;
        border-radius: 30px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .story-img-container img {
        transition: transform 0.5s ease;
        width: 100%;
        height: 450px;
        object-fit: cover;
    }

    .story-img-container:hover img {
        transform: scale(1.05);
    }

    .feature-step {
        border-left: 2px solid rgba(250, 204, 21, 0.2);
        padding-left: 30px;
        position: relative;
        margin-bottom: 40px;
    }

    .feature-step::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 0;
        width: 12px;
        height: 12px;
        background: var(--primary-gradient);
        border-radius: 50%;
        box-shadow: 0 0 15px var(--primary-gradient);
    }

    blockquote {
        font-style: italic;
        font-size: 1.2rem;
        border-left: 4px solid #a855f7;
        padding-left: 20px;
        color: #e2e8f0;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 25px;
        transition: 0.3s;
    }

    .stat-card:hover {
        background: rgba(255, 255, 255, 0.06);
        transform: translateY(-5px);
    }
</style>

<!-- 1. HERO SECTION -->
<section class="about-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 animate__animated animate__fadeInLeft">
                <h6 class="text-gold-gradient text-uppercase ls-3 mb-3">Kỷ nguyên Văn học số 2026</h6>
                <h1 class="display-3 fw-bold text-white mb-4">Hồi sinh <br><span class="text-gradient">Di sản Tâm hồn</span></h1>
                <p class="fs-5 text-white-50 lh-base pe-lg-5">
                    Chúng tôi không dạy bạn cách thuộc lòng bài văn mẫu. Chúng tôi kiến tạo một 
                    <span class="text-white">vũ trụ thị giác</span>, nơi mỗi điển tích, mỗi nhân vật đều bước ra khỏi trang sách để sống một cuộc đời rực rỡ.
                </p>
                <div class="mt-5 d-flex gap-4">
                    <div class="stat-card">
                        <h3 class="text-gold-gradient mb-0">1.5K+</h3>
                        <small class="text-white-50">Ngày sáng tạo</small>
                    </div>
                    <div class="stat-card">
                        <h3 class="text-gold-gradient mb-0">50K+</h3>
                        <small class="text-white-50">Hội viên Muse</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block animate__animated animate__fadeInRight">
                <div class="story-img-container active-frame">
                    <!-- Ảnh đại diện cho Bảo tàng số -->
                    <img src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=1000" alt="Vanhoc247 Digital Art">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. CÂU CHUYỆN THƯƠNG HIỆU -->
<section class="py-5 bg-art-dark">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 order-2 order-lg-1">
                <h2 class="text-white fw-bold mb-4">Tại sao là Vanhoc247?</h2>
                <div class="text-white-50 fs-5">
                    <p class="mb-4">
                        Tất cả bắt đầu từ một nỗi trăn trở: <span class="text-white">Tại sao vẻ đẹp của văn chương lại bị che lấp bởi những áp lực điểm số và cách học cũ kỹ?</span>
                    </p>
                    <blockquote>
                        "Văn chương không phải là những con chữ nằm yên trên giấy, nó là dòng chảy của tư duy và cảm xúc."
                    </blockquote>
                    <p class="mt-4">
                        Vanhoc247 ra đời để trở thành **"Giám tuyển tri thức"**. Chúng tôi ứng dụng công nghệ AI để cá nhân hóa lộ trình và đồ họa 4K để trực quan hóa bối cảnh, giúp bạn chạm vào linh hồn của tác phẩm một cách chân thực nhất.
                    </p>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <div class="story-img-container shadow-vibrant">
                    <!-- Ảnh minh họa câu chuyện -->
                    <img src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?q=80&w=1000" alt="Writing Story">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. ĐIỂM NHẤN ĐỘC BẢN -->
<section class="py-5 border-bottom-art">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-gradient fw-bold text-uppercase">Khác biệt tạo nên đẳng cấp</h6>
            <h2 class="fw-bold text-white display-5">Sức mạnh cốt lõi</h2>
        </div>
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="feature-step">
                    <h4 class="text-white fw-bold">Thị giác hóa 4K</h4>
                    <p class="text-white-50">Tái hiện bối cảnh các tác phẩm kinh điển bằng đồ họa đỉnh cao, biến bài học thành trải nghiệm điện ảnh.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-step">
                    <h4 class="text-white fw-bold">Trợ lý Muse AI</h4>
                    <p class="text-white-50">AI thông minh hỗ trợ bóc tách luận điểm chuyên sâu và sửa bài viết dựa trên dữ liệu hàng nghìn bài văn giỏi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-step">
                    <h4 class="text-white fw-bold">Tư duy Đa chiều</h4>
                    <p class="text-white-50">Không học vẹt. Chúng tôi dạy cách liên tưởng văn chương với lịch sử, tâm lý học và đời sống hiện đại.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- 5. GÓC ADMIN: CHUYỆN BẾP NÚC -->
<section class="py-5 bg-art-dark overflow-hidden">
    <div class="container py-5">
        <div class="glass-card p-5 border-0 position-relative" style="background: linear-gradient(160deg, rgba(168, 85, 247, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
            
            <div class="row align-items-center">
                <div class="col-lg-4 text-center mb-4 mb-lg-0">
                    <div class="admin-avatar-group position-relative d-inline-block">
                        <!-- Icon đại diện nhí nhảnh -->
                        <div class="float-animation">
                            <i class="fas fa-ghost fa-5x text-gradient mb-3"></i>
                        </div>
                        <h4 class="text-white fw-bold">Team "Cày Đêm"</h4>
                        <span class="badge rounded-pill bg-purple-light text-white px-3 py-2">Hội những kẻ mộng mơ</span>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <h3 class="text-white fw-bold mb-3">Lời tự thú từ "Hội đồng Quản trị... Chữ" 🖋️</h3>
                    <div class="text-white-50 lh-lg">
                        <p>
                            Mọi người hỏi: <span class="text-white italic">"Ủa, sao lại làm Vanhoc247?"</span>. 
                            Thật ra, nguồn gốc của trang web này không "hoành tráng" như mấy bài diễn văn đâu. Nó bắt đầu từ một đêm mấy anh em Admin ngồi gặm mì tôm, nhìn đống đề cương Văn dài như... sớ Táo Quân và tự hỏi: 
                            <strong>"Tại sao chúng ta phải khổ thế này? Tại sao không có cái gì học Văn mà 'cuốn' như xem Netflix nhỉ?"</strong>.
                        </p>
                        <p>
                            Và thế là, bùm! Vanhoc247 ra đời. Tụi mình đã thức trắng hàng trăm đêm (cùng rất nhiều ly cafe vỉa hè) để biến những nhân vật "trong truyền thuyết" trở nên gần gũi nhất có thể.
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-heart text-danger me-2"></i> 
                            <strong>Lời nhắn từ Admin:</strong> "Bọn mình không phải là những giáo sư uyên bác, bọn mình chỉ là những người yêu Tiếng Việt đến phát điên và muốn lây lan cái 'bệnh' yêu Văn này cho bạn. Học đi, vì Văn học thực sự rất ngầu!"
                        </p>
                    </div>
                </div>
            </div>

            <!-- Decor nhí nhảnh ở góc -->
            <div class="position-absolute top-0 end-0 p-3 opacity-25">
                <i class="fas fa-rocket fa-3x text-white rotate-45"></i>
            </div>
        </div>
    </div>
</section>
<!-- 4. CALL TO ACTION - ĐÃ FIX LỖI ĐÈ CHỮ -->
<section class="py-5 mb-5">
    <div class="container">
        <!-- Thêm style inline trực tiếp để xử lý triệt để -->
        <div class="glass-card p-5 text-center position-relative overflow-hidden" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
            
            <!-- Nội dung chính: Phải có z-index cao hơn -->
            <div class="position-relative" style="z-index: 10;">
                <h2 class="text-white fw-bold mb-3">Bạn đã sẵn sàng để "Cảm" văn chương theo cách khác?</h2>
                <p class="text-white-50 mb-4 mx-auto" style="max-width: 600px;">Gia nhập cộng đồng hơn 50.000 học viên đang thay đổi cách tư duy mỗi ngày.</p>
                <a href="index.php#registration" class="btn btn-primary-vibrant btn-lg px-5 py-3 shadow-vibrant fw-bold">
                    Gia nhập ngay bây giờ
                </a>
            </div>

            <!-- Icon nền: Cho mờ hẳn (opacity-5) và đẩy xuống dưới cùng (z-index: 1) -->
            <div class="position-absolute" style="z-index: 1; bottom: -20px; right: 20px; opacity: 0.05;">
                <i class="fas fa-quote-right" style="font-size: 15rem; color: #fff;"></i>
            </div>
            
            <!-- Thêm một cái icon bên trái cho cân bằng nếu muốn -->
            <div class="position-absolute" style="z-index: 1; top: -20px; left: 20px; opacity: 0.05;">
                <i class="fas fa-quote-left" style="font-size: 10rem; color: #fff;"></i>
            </div>
        </div>
    </div>
</section>

<style>
    /* Xoay cái tên lửa cho nó vui mắt */
    .rotate-45 {
        transform: rotate(45deg);
    }
    
    /* Hiệu ứng gợn sóng nhẹ cho khung này */
    .bg-purple-light {
        background: rgba(168, 85, 247, 0.2);
        border: 1px solid rgba(168, 85, 247, 0.3);
    }
</style>

<?php include 'includes/footer.php'; ?>