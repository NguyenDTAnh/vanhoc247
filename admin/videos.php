<?php
include 'includes/check_role.php';
require_once '../includes/db.php';

// 1. XỬ LÝ UPLOAD VIDEO (RÚT GỌN)
if(isset($_POST['upload_video'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = 0; // Set mặc định 0 cho các video khóa học này (hoặc có thể bỏ trống nếu DB ko yêu cầu)
    
    $img_path = '';
    if(!empty($_FILES['course_image']['name'])) {
        $img_name = time() . "_" . basename($_FILES["course_image"]["name"]);
        if(move_uploaded_file($_FILES["course_image"]["tmp_name"], "../uploads/images/" . $img_name)) {
            $img_path = "uploads/images/" . $img_name;
        }
    }

    $sql = "INSERT INTO courses (title, description, category, image_url, format, price) 
            VALUES ('$title', '$desc', '$category', '$img_path', 'Video', $price)";
    
    if(mysqli_query($conn, $sql)) {
        $course_id = mysqli_insert_id($conn);
        
        // Upload Video
        if(!empty($_FILES['course_video']['name'])) {
            $video_name = time() . "_" . basename($_FILES["course_video"]["name"]);
            if(move_uploaded_file($_FILES["course_video"]["tmp_name"], "../uploads/videos/" . $video_name)) {
                $v_path = "uploads/videos/" . $video_name;
                // Thêm vào bảng course_contents
                mysqli_query($conn, "INSERT INTO course_contents (course_id, title, file_path, file_type) VALUES ($course_id, 'Video bài giảng', '$v_path', 'video')");
            }
        }
    }
    header("Location: videos.php");
    exit();
}

// 2. XỬ LÝ XÓA
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM course_contents WHERE course_id = $id");
    mysqli_query($conn, "DELETE FROM courses WHERE id = $id");
    header("Location: videos.php"); 
    exit();
}

// 3. LẤY DỮ LIỆU VIDEO
$query = "SELECT c.*, cc.file_path as video_path 
          FROM courses c 
          INNER JOIN course_contents cc ON c.id = cc.course_id 
          WHERE (c.format = 'Video' OR c.format IS NULL OR c.format = '') 
          AND cc.file_type = 'video' 
          ORDER BY c.id DESC";
$res_videos = mysqli_query($conn, $query);

$videos = [];
while($row = mysqli_fetch_assoc($res_videos)) {
    $videos[] = $row;
}
$featured = !empty($videos) ? $videos[0] : null;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos Cinematic | Vanhoc247</title>
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
        }
        
        body { background-color: var(--netflix-black); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; min-height: 100vh; padding-bottom: 50px; }
        
        /* Hero Section */
        .hero-banner {
            position: relative;
            height: 60vh;
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid var(--border);
            margin-bottom: 40px;
        }
        .hero-vignette {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, var(--netflix-black) 0%, transparent 60%),
                        linear-gradient(to right, var(--netflix-black) 0%, transparent 70%);
        }
        .hero-content {
            position: absolute;
            bottom: 20%;
            left: 5%;
            width: 50%;
            z-index: 10;
        }
        .hero-title { font-size: 3.5rem; font-weight: 800; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero-desc { font-size: 1.1rem; color: #ccc; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Buttons */
        .btn-play {
            background-color: white; color: black; font-weight: bold; border-radius: 4px; padding: 10px 24px; font-size: 1.1rem; margin-right: 15px; border: none; transition: 0.2s;
        }
        .btn-play:hover { background-color: rgba(255,255,255,0.7); color: black; }
        .btn-more {
            background-color: rgba(109, 109, 110, 0.7); color: white; font-weight: bold; border-radius: 4px; padding: 10px 24px; font-size: 1.1rem; border: none; transition: 0.2s;
        }
        .btn-more:hover { background-color: rgba(109, 109, 110, 0.4); color: white; }
        .btn-upload { background: var(--netflix-red); border: none; font-weight: 700; }
        .btn-upload:hover { background: #b80710; }
        
        /* Video Grid */
        .video-grid { padding: 0 4%; }
        .section-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #e5e5e5; }
        
        .video-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            background: #222;
        }
        .video-card img { width: 100%; aspect-ratio: 16/9; object-fit: cover; }
        .video-card:hover {
            transform: scale(1.08);
            z-index: 10;
            box-shadow: 0 10px 25px rgba(0,0,0,0.9);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .video-card .card-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0; top: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 15px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .video-card:hover .card-overlay { opacity: 1; }
        .card-title { font-weight: 700; margin-bottom: 5px; font-size: 1rem; }
        .card-meta { font-size: 0.8rem; color: #aaa; }
        .btn-delete { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s; }
        .btn-delete:hover { background: var(--netflix-red); color: white; }
        .video-card:hover .btn-delete { opacity: 1; }

        /* Modal Styles */
        .modal-content { background: var(--netflix-black); border: 1px solid var(--border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--border); }
        .modal-footer { border-top: 1px solid var(--border); }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff; }
        .form-control:focus, .form-select:focus { background: #2a2a2a; color: #fff; border-color: var(--netflix-red); box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25); }
        
        /* Modal Phát Video */
        #videoPlayModal .modal-content { background: transparent; border: none; }
        #videoPlayModal .modal-body { padding: 0; background: black; border-radius: 12px; overflow: hidden;}
        #playingVideo { width: 100%; outline: none; border-radius: 12px;}

        /* Header Bar */
        .admin-header {
            position: absolute; top: 0; left: 0; right: 0;
            padding: 20px 4%; z-index: 100;
            display: flex; justify-content: space-between; align-items: center;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="fw-bold m-0"><span style="color: var(--netflix-red);">V</span> Videos Streaming</h4>
        </div>
        <button class="btn btn-upload rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload me-2"></i> Upload bài giảng
        </button>
    </div>

    <?php if($featured): ?>
        <?php 
            $featured_img = !empty($featured['image_url']) ? "../" . $featured['image_url'] : "https://images.unsplash.com/photo-1574267432553-4b462808152f?q=80&w=1500";
        ?>
        <div class="hero-banner" style="background-image: url('<?= $featured_img ?>');">
            <div class="hero-vignette"></div>
            <div class="hero-content">
                <div class="badge bg-danger mb-2">Đ Mới Nhất</div>
                <h1 class="hero-title"><?= htmlspecialchars($featured['title']) ?></h1>
                <p class="hero-desc mb-4"><?= htmlspecialchars($featured['description']) ?></p>
                <div class="d-flex">
                    <button class="btn-play" onclick="playVideo('../<?= $featured['video_path'] ?>')">
                        <i class="fas fa-play me-2"></i> Phát Ngay
                    </button>
                    <!-- <button class="btn-more">
                        <i class="fas fa-info-circle me-2"></i> Chi tiết
                    </button> -->
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-banner" style="background: #222; display:flex; align-items:center; justify-content:center;">
            <div class="text-center text-muted">
                <i class="fas fa-film fa-3x mb-3"></i>
                <h2>Chưa có bộ phim/video nào</h2>
                <p>Hãy upload để trải nghiệm ngay!</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="video-grid">
        <h3 class="section-title">Kho Phim Độc Bản</h3>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach($videos as $vid): ?>
                <?php 
                    $v_img = !empty($vid['image_url']) ? "../" . $vid['image_url'] : "https://images.unsplash.com/photo-1594897030264-ab7d87efc473?q=80&w=600";
                ?>
                <div class="col">
                    <div class="video-card" onclick="playVideo('../<?= $vid['video_path'] ?>')">
                        <img src="<?= $v_img ?>" alt="<?= htmlspecialchars($vid['title']) ?>">
                        <div class="card-overlay">
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($vid['title']) ?></h5>
                            <div class="card-meta">
                                <span class="text-success fw-bold me-2">99% Match</span> 
                                <span><?= date('Y', strtotime($vid['created_at'])) ?></span>
                                <span class="badge border border-secondary text-secondary ms-2"><?= $vid['category'] ?></span>
                            </div>
                        </div>
                        <!-- Nâng z-index nút Edit/Delete để click k bị gọi hàm playVideo -->
                        <a href="?delete_id=<?= $vid['id'] ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Chắc chắn muốn xóa tác phẩm này chứ?');">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Upload (Rút Gọn) -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">🎬 Xuất xưởng Video Mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-secondary small">Tên Series/Bài Giảng</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Phân tích kiệt tác Vợ Nhặt...">
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary small">Tóm tắt nội dung</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Ghi chú nhẹ những điểm hay chấn động..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small">Gắn nhãn (Thể loại)</label>
                        <select name="category" class="form-select">
                            <option value="Lớp 12">Văn 12 - Ôn thi Quốc Gia</option>
                            <option value="Lớp 11">Văn 11 - Phá đảo kiến thức</option>
                            <option value="Kỹ năng">Muse Tips - Kỹ năng viết</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small">Ảnh Thumbnail (Poster phim)</label>
                        <input type="file" name="course_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="mb-3 p-3 rounded" style="background: rgba(229, 9, 20, 0.1); border: 1px dashed var(--netflix-red);">
                    <label class="form-label text-danger fw-bold"><i class="fas fa-file-video me-1"></i> Trái Tim Của Bài Giảng (Tệp Ngồn MP4)</label>
                    <input type="file" name="course_video" class="form-control bg-transparent border-0 text-white" accept="video/mp4" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="upload_video" class="btn btn-upload w-100 py-2 fs-5">LÊN SÓNG</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Phát Video Trực Tiếp -->
<div class="modal fade" id="videoPlayModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal"></button>
                <video id="playingVideo" controls controlsList="nodownload">
                    <source src="" type="video/mp4">
                    Trình duyệt của bạn quá cũ để chiếu Cinematic này!
                </video>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Logic gọi popup phát video
    const videoModal = new bootstrap.Modal(document.getElementById('videoPlayModal'));
    const videoPlayer = document.getElementById('playingVideo');

    function playVideo(videoUrl) {
        videoPlayer.src = videoUrl;
        videoModal.show();
        videoPlayer.play();
    }

    // Tự động tắt âm và dừng video khi đóng popup
    document.getElementById('videoPlayModal').addEventListener('hidden.bs.modal', function () {
        videoPlayer.pause();
        videoPlayer.currentTime = 0;
    });
</script>
</body>
</html>
