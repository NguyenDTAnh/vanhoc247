<?php
include 'includes/check_role.php';
require_once '../includes/db.php';

// 1. XỬ LÝ KHỞI TẠO LUỒNG LIVE MỚI
if(isset($_POST['create_live'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $youtube_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);
    $facebook_link = mysqli_real_escape_string($conn, $_POST['facebook_link']);
    $tiktok_link = mysqli_real_escape_string($conn, $_POST['tiktok_link']);
    
    $img_path = '';
    if(!empty($_FILES['live_thumbnail']['name'])) {
        $img_name = time() . "_" . basename($_FILES["live_thumbnail"]["name"]);
        // Đẩy ảnh bìa vào uploads/images/
        if(move_uploaded_file($_FILES["live_thumbnail"]["tmp_name"], "../uploads/images/" . $img_name)) {
            $img_path = "uploads/images/" . $img_name;
        }
    }

    $sql = "INSERT INTO livestreams (title, thumbnail, youtube_link, facebook_link, tiktok_link, status) 
            VALUES ('$title', '$img_path', '$youtube_link', '$facebook_link', '$tiktok_link', 'live')";
    
    mysqli_query($conn, $sql);
    header("Location: lives.php");
    exit();
}

// 2. XỬ LÝ DỪNG SÓNG (XÓA BỎ HOẶC ĐỔI TRẠNG THÁI)
// Ở đây dùng xóa cho nhanh và sạch DB theo luồng quản lý tĩnh
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM livestreams WHERE id = $id");
    header("Location: lives.php"); 
    exit();
}

// 3. LẤY RAW DATA
$query = "SELECT * FROM livestreams ORDER BY created_at DESC";
$res_lives = mysqli_query($conn, $query);

$lives = [];
if ($res_lives) {
    while($row = mysqli_fetch_assoc($res_lives)) {
        $lives[] = $row;
    }
} else {
    // Tự động đẻ bảng nếu bên Windows XAMPP chưa tạo
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS livestreams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        thumbnail VARCHAR(255) DEFAULT '',
        youtube_link VARCHAR(255) DEFAULT '',
        facebook_link VARCHAR(255) DEFAULT '',
        tiktok_link VARCHAR(255) DEFAULT '',
        status VARCHAR(50) DEFAULT 'live',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Livestream | Vanhoc247</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --netflix-black: #141414;
            --netflix-red: #E50914;
            --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            --glass: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
            
            /* Social Colors */
            --fb-color: #1877F2;
            --yt-color: #FF0000;
            --tt-color: #000000;
        }
        
        body { background-color: var(--netflix-black); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; min-height: 100vh; padding-top: 80px; padding-bottom: 50px; position: relative; }
        
        /* Header Bar */
        .admin-header {
            position: absolute; top: 0; left: 0; right: 0;
            padding: 20px 4%; z-index: 100;
            display: flex; justify-content: space-between; align-items: center;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
        }
        /* Buttons */
        .btn-upload { background: var(--netflix-red); border: none; font-weight: 700; color: white;}
        .btn-upload:hover { background: #b80710; color: white; transform: scale(1.05); transition: 0.2s;}
        
        /* Grid */
        .live-grid { padding: 0 4%; margin-top: 30px;}
        .section-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #e5e5e5; display: flex; align-items: center; }
        
        .live-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            background: #222;
        }
        .live-card img { width: 100%; aspect-ratio: 16/9; object-fit: cover; }
        
        /* Box-shadow đỏ / cam nhẹ để tạo hiệu ứng đang phát */
        .live-card.is-live { border-bottom: 3px solid var(--netflix-red); }
        .live-card.is-live:hover {
            transform: scale(1.05);
            z-index: 10;
            box-shadow: 0 10px 30px rgba(229, 9, 20, 0.4);
            border: 1px solid rgba(229, 9, 20, 0.8);
        }
        
        .live-card .card-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0; top: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, transparent 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 15px;
        }

        .btn-delete { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s; text-decoration: none; z-index: 20;}
        .btn-delete:hover { background: var(--netflix-red); color: white; }
        .live-card:hover .btn-delete { opacity: 1; }

        /* Nút nhấp nháy chữ Live */
        .live-badge {
            position: absolute; top: 10px; left: 10px; background: var(--netflix-red); color: white; border-radius: 4px; padding: 2px 10px; font-weight: 800; font-size: 0.75rem; letter-spacing: 1px; animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(229, 9, 20, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(229, 9, 20, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(229, 9, 20, 0); }
        }

        /* Nút Điều Hương Social trong Popup */
        .social-btn { display: flex; align-items: center; width: 100%; border-radius: 12px; padding: 15px 20px; margin-bottom: 15px; font-size: 1.2rem; font-weight: bold; color: white; text-decoration: none; transition: 0.3s; }
        .social-btn:hover { color: white; filter: brightness(1.1); transform: translateX(10px); }
        .social-yt { background: linear-gradient(45deg, #FF0000, #cc0000); }
        .social-fb { background: linear-gradient(45deg, #1877F2, #145dbf); }
        .social-tt { background: linear-gradient(45deg, #000000, #25f4ee, #fe2c55); } /* Màu gradient đú trend tiktok */

        /* Modal Custom css */
        .modal-content { background: #1c1c1c; border: 1px solid #333; border-radius: 16px; }
        .modal-header { border-bottom: 1px solid #333; }
        .form-control, .form-select { background: #111; border: 1px solid #333; color: #fff; }
        .form-control:focus { background: #111; color: #fff; border-color: var(--netflix-red); box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25); }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="fw-bold m-0"><span style="color: var(--netflix-red);"><i class="fas fa-broadcast-tower"></i></span> Trạm Phát Sóng Trực Tiếp</h4>
        </div>
        <button class="btn btn-upload rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createLiveModal">
            <i class="fas fa-satellite-dish me-2"></i> Tạo lớp học mới
        </button>
    </div>

    <!-- Live Grid -->
    <div class="live-grid">
        <h3 class="section-title"><i class="fas fa-satellite text-danger me-2"></i> Đang Phát Sóng</h3>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
            <?php if(empty($lives)): ?>
                <div class="col-12 text-center text-secondary py-5">
                    <i class="fas fa-tv fa-3x mb-3 opacity-50"></i>
                    <h5>Hiện tại trạm phát sóng đang ngủ yên...</h5>
                </div>
            <?php endif; ?>

            <?php foreach($lives as $live): ?>
                <?php 
                    $thumb = !empty($live['thumbnail']) ? "../" . $live['thumbnail'] : "https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=1500";
                    // Thu gom link bằng JSON để JS dễ xử lý
                    $platforms = json_encode([
                        'title' => htmlspecialchars($live['title']),
                        'yt' => $live['youtube_link'],
                        'fb' => $live['facebook_link'],
                        'tt' => $live['tiktok_link']
                    ]);
                ?>
                <div class="col">
                    <div class="live-card is-live" onclick='openWatchOptions(<?= $platforms ?>)'>
                        <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($live['title']) ?>">
                        <div class="live-badge">🔴 ĐANG TRỰC TIẾP</div>
                        
                        <div class="card-overlay">
                            <h4 class="fw-bold text-truncate mb-2"><?= htmlspecialchars($live['title']) ?></h4>
                            <div class="d-flex gap-2">
                                <?php if(!empty($live['facebook_link'])): ?><div style="width: 20px; height: 20px; border-radius: 50%; background: #1877F2; display:flex; align-items:center; justify-content:center; font-size:10px;"><i class="fab fa-facebook-f text-white"></i></div><?php endif; ?>
                                <?php if(!empty($live['youtube_link'])): ?><div style="width: 20px; height: 20px; border-radius: 50%; background: #FF0000; display:flex; align-items:center; justify-content:center; font-size:10px;"><i class="fab fa-youtube text-white"></i></div><?php endif; ?>
                                <?php if(!empty($live['tiktok_link'])): ?><div style="width: 20px; height: 20px; border-radius: 50%; background: #fff; display:flex; align-items:center; justify-content:center; font-size:10px;"><i class="fab fa-tiktok text-black"></i></div><?php endif; ?>
                            </div>
                        </div>

                        <!-- Chặn gọi thẻ div bên ngoài khi bấm tắt -->
                        <a href="?delete_id=<?= $live['id'] ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Dừng luồng live này và xoá lịch sử?');">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal 1: Create Live -->
<div class="modal fade" id="createLiveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tạo phiên học trực tuyến mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label text-secondary small text-uppercase fw-bold"># Tên Buổi Chinh Phạt</label>
                    <input type="text" name="title" class="form-control form-control-lg" required placeholder="Ví dụ: Lấy gốc Toán 12 trong 1 đêm">
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-secondary small text-uppercase fw-bold"># Ảnh Bìa Kêu Gọi (Thumbnail)</label>
                    <input type="file" name="live_thumbnail" class="form-control bg-dark" accept="image/*" required>
                </div>

                <div class="p-3 border rounded shadow-sm" style="border-color: #333 !important; background: #161616;">
                    <label class="form-label text-danger small text-uppercase fw-bold mb-3"><i class="fas fa-link"></i> Định Tuyến Đa Nền Tảng</label>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-transparent border-end-0 border-secondary" style="border-color: #333 !important; color:#FF0000;"><i class="fab fa-youtube fa-lg"></i></span>
                        <input type="url" name="youtube_link" class="form-control border-start-0 ps-0" placeholder="Paste link YouTube vào đây">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-transparent border-end-0 border-secondary" style="border-color: #333 !important; color:#1877F2;"><i class="fab fa-facebook fa-lg"></i></span>
                        <input type="url" name="facebook_link" class="form-control border-start-0 ps-0" placeholder="Paste link Facebook vào đây">
                    </div>

                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 border-secondary" style="border-color: #333 !important; color:#fff;"><i class="fab fa-tiktok fa-lg"></i></span>
                        <input type="url" name="tiktok_link" class="form-control border-start-0 ps-0" placeholder="Paste link TikTok vào đây">
                    </div>
                    <small class="text-secondary mt-2 d-block fst-italic">* Quăng URL nền tảng nào thì người xem sẽ thấy nút đó. Nền tảng nào không live cứ để trống nghen.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="submit" name="create_live" class="btn btn-upload w-100 py-3 fs-5">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Hiển thị Platform Chọn Lựa -->
<div class="modal fade" id="watchOptionsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background: rgba(10,10,10,0.9); backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-0 justify-content-center position-relative">
                <h5 class="modal-title fw-bold text-center w-100 mt-2" id="watchTitle">Tham Gia Lớp Học</h5>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-4" id="watchOptionsContainer">
                <!-- Nội dung các nút sẽ được Render bằng thẻ A từ JS xuống -->
                <p class="text-muted small">Vui lòng đợi...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JS Điều khiển Logic bóc JSON và mở popup chứa link
    const watchModal = new bootstrap.Modal(document.getElementById('watchOptionsModal'));
    const optionsContainer = document.getElementById('watchOptionsContainer');
    const titleNode = document.getElementById('watchTitle');

    function openWatchOptions(platforms) {
        titleNode.innerText = platforms.title;
        optionsContainer.innerHTML = ''; // reset

        let count = 0;
        
        // Check nếu có FB
        if(platforms.fb && platforms.fb.trim() !== '') {
            optionsContainer.innerHTML += `
                <a href="${platforms.fb}" target="_blank" class="social-btn social-fb shadow">
                    <i class="fab fa-facebook fa-2x me-3"></i> Xem trên Facebook
                </a>
            `; count++;
        }
        
        // Check nếu có Youtube
        if(platforms.yt && platforms.yt.trim() !== '') {
            optionsContainer.innerHTML += `
                <a href="${platforms.yt}" target="_blank" class="social-btn social-yt shadow">
                    <i class="fab fa-youtube fa-2x me-3"></i> Xem trên YouTube
                </a>
            `; count++;
        }
        
        // Check Tiktok
        if(platforms.tt && platforms.tt.trim() !== '') {
            optionsContainer.innerHTML += `
                <a href="${platforms.tt}" target="_blank" class="social-btn social-tt shadow">
                    <i class="fab fa-tiktok fa-2x me-3"></i> Tóp Tóp Live
                </a>
            `; count++;
        }

        // Bắt lỗi rỗng
        if(count === 0) {
            optionsContainer.innerHTML = `<p class="text-secondary my-3"><i class="fas fa-exclamation-triangle"></i> Lớp học chấn động nhưng Admin lại quên chưa dán link ở bất kỳ nền tảng nào!</p>`;
        }

        // Mở popup
        watchModal.show();
    }
</script>
</body>
</html>
