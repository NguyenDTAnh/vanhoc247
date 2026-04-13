<?php 
include 'includes/header.php';
require_once 'includes/db.php';

$query = "SELECT c.*, cc.file_path as video_path 
          FROM courses c 
          INNER JOIN course_contents cc ON c.id = cc.course_id 
          WHERE (c.format = 'Video' OR c.format IS NULL OR c.format = '') 
          AND cc.file_type = 'video' 
          ORDER BY c.id DESC";
$res_videos = mysqli_query($conn, $query);

$videos = [];
if ($res_videos) {
    while($row = mysqli_fetch_assoc($res_videos)) {
        $videos[] = $row;
    }
}
$featured = !empty($videos) ? $videos[0] : null;

// Fetches for PDFs
$query_docs = "SELECT c.*, cc.file_path as pdf_path 
          FROM courses c 
          INNER JOIN course_contents cc ON c.id = cc.course_id 
          WHERE c.format = 'PDF' 
          AND cc.file_type = 'pdf' 
          ORDER BY c.id DESC";
$res_docs = mysqli_query($conn, $query_docs);
$documents = [];
if ($res_docs) {
    while($row = mysqli_fetch_assoc($res_docs)) {
        $documents[] = $row;
    }
}
?>

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
        <?php if($featured): ?>
        <div class="spotlight-card">
            <img src="<?php echo !empty($featured['image_url']) ? $featured['image_url'] : 'https://images.unsplash.com/photo-1505664194779-8beaceb93744?q=80&w=1500'; ?>" class="spotlight-img" alt="Spotlight">
            <div class="spotlight-content">
                <span class="badge bg-danger px-3 py-2 mb-3">MỚI NHẤT</span>
                <h2 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($featured['title']); ?></h2>
                <p class="text-white-50 fs-5 mb-4" style="max-width: 600px;"><?php echo htmlspecialchars($featured['description']); ?></p>
                <div class="d-flex gap-3">
                    <a href="<?php echo htmlspecialchars($featured['video_path']); ?>" target="_blank" class="btn btn-light btn-lg px-5 fw-bold"><i class="fas fa-play me-2"></i>Xem ngay</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 2. VIDEO ĐANG XEM (CONTINUE WATCHING) -->
        <?php if(!empty($videos)): ?>
        <div class="nf-row">
            <div class="nf-row-header">
                <span>Tiếp tục xem cho Người dùng</span>
            </div>
            <div class="nf-scroll">
                <?php foreach(array_slice($videos, 0, 4) as $vid): ?>
                <div class="nf-item">
                    <a href="<?php echo htmlspecialchars($vid['video_path']); ?>" target="_blank" class="text-decoration-none text-white d-block">
                        <img src="<?php echo !empty($vid['image_url']) ? $vid['image_url'] : 'https://images.unsplash.com/photo-1518818494391-3841963e2e7b?w=600'; ?>" alt="Video">
                        <!-- fake progress bar random cho sinh động -->
                        <div class="watching-progress" style="width: <?php echo rand(20, 90); ?>%;"></div>
                        <div class="p-3 bg-dark">
                            <h6 class="mb-0 text-truncate"><?php echo htmlspecialchars($vid['title']); ?></h6>
                            <small class="text-white-50">Còn <?php echo rand(10, 45); ?> phút</small>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- DANH MỤC LỚP 12 -->
        <div class="nf-row">
            <div class="nf-row-header">
                <span>Kho bài giảng mới nhất</span>
                <a href="#" class="text-white-50 small text-decoration-none">Xem tất cả ></a>
            </div>
            <div class="nf-scroll">
                <?php if(empty($videos)): ?>
                    <div class="text-muted small">Đang cập nhật bài giảng...</div>
                <?php else: ?>
                    <?php foreach($videos as $vid): ?>
                    <a href="<?php echo htmlspecialchars($vid['video_path']); ?>" target="_blank" class="nf-item text-decoration-none text-white d-block">
                        <img src="<?php echo !empty($vid['image_url']) ? $vid['image_url'] : 'https://images.unsplash.com/photo-1518818494391-3841963e2e7b?w=600'; ?>">
                        <div class="p-3 bg-dark">
                            <h6 class="mb-1 text-truncate"><?php echo htmlspecialchars($vid['title']); ?></h6>
                            <small class="text-info"><?php echo htmlspecialchars($vid['category']); ?></small>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- NỘI DUNG PDF (ẨN MẶC ĐỊNH) -->
    <div id="pdf-area" style="display: none;">
        <?php if(empty($documents)): ?>
            <div class="text-center py-5 text-white-50">
                <i class="fas fa-file-pdf fa-4x mb-3" style="opacity: 0.3;"></i>
                <h4 class="fw-bold">Chưa có tài liệu nào</h4>
                <p>Kho tàng hiện đang trống, đang chờ được cập nhật.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($documents as $doc): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="glass-card p-4 text-center d-flex flex-column h-100" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; transition: 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <?php $pdf_cover = !empty($doc['image_url']) ? $doc['image_url'] : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=600'; ?>
                        <img src="<?php echo htmlspecialchars($pdf_cover); ?>" alt="Bìa sách" style="width: 100%; aspect-ratio: 3/4; object-fit: cover; border-radius: 12px;" class="mb-3 shadow-sm">
                        <h6 class="fw-bold text-white mb-2" style="font-size: 1.1rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($doc['title']); ?></h6>
                        <span class="badge border border-info text-info mx-auto mb-2"><?php echo htmlspecialchars($doc['category']); ?></span>
                        <p class="text-white-50 small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($doc['description']); ?></p>
                        <a href="<?php echo htmlspecialchars($doc['pdf_path']); ?>" target="_blank" class="btn btn-outline-light rounded-pill w-100 mt-3" style="font-weight: 700;"><i class="fas fa-external-link-alt me-2"></i>Đọc ngay</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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