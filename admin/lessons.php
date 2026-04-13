<?php
include 'includes/check_role.php';
require_once '../includes/db.php';

// 1. XỬ LÝ UPLOAD BÀI HỌC (VIDEO + PDF + QUIZ)
if(isset($_POST['upload_lesson'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $quiz_link = mysqli_real_escape_string($conn, $_POST['quiz_link']);
    $price = 0; 
    
    $img_path = '';
    if(!empty($_FILES['course_image']['name'])) {
        $img_name = time() . "_" . basename($_FILES["course_image"]["name"]);
        if(move_uploaded_file($_FILES["course_image"]["tmp_name"], "../uploads/images/" . $img_name)) {
            $img_path = "uploads/images/" . $img_name;
        }
    }

    $sql = "INSERT INTO courses (title, description, category, author, quiz_link, image_url, format, price) 
            VALUES ('$title', '$desc', '$category', '$author', '$quiz_link', '$img_path', 'Lesson', $price)";
    
    if(mysqli_query($conn, $sql)) {
        $course_id = mysqli_insert_id($conn);
        
        // Lấy đường dẫn Video đã chọn
        $v_path = mysqli_real_escape_string($conn, $_POST['course_video']);
        if(!empty($v_path)) {
            mysqli_query($conn, "INSERT INTO course_contents (course_id, title, file_path, file_type) VALUES ($course_id, 'Video bài giảng', '$v_path', 'video')");
        }

        // Lấy đường dẫn PDF đã chọn
        $p_path = mysqli_real_escape_string($conn, $_POST['course_pdf']);
        if(!empty($p_path)) {
            mysqli_query($conn, "INSERT INTO course_contents (course_id, title, file_path, file_type) VALUES ($course_id, 'Tài liệu đính kèm', '$p_path', 'pdf')");
        }
    }
    header("Location: lessons.php");
    exit();
}

// 2. XỬ LÝ XÓA
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM course_contents WHERE course_id = $id");
    mysqli_query($conn, "DELETE FROM courses WHERE id = $id");
    header("Location: lessons.php"); 
    exit();
}

// 3. LẤY DỮ LIỆU
$query = "SELECT * FROM courses WHERE format = 'Lesson' ORDER BY id DESC";
$res_lessons = mysqli_query($conn, $query);

$lessons = [];
while($row = mysqli_fetch_assoc($res_lessons)) {
    $lessons[] = $row;
}
$featured = !empty($lessons) ? $lessons[0] : null;

// Tải danh sách Video & PDF có sẵn
$avail_videos = [];
$res_vids = mysqli_query($conn, "SELECT c.title, cc.file_path FROM courses c INNER JOIN course_contents cc ON c.id = cc.course_id WHERE (c.format = 'Video' OR c.format IS NULL OR c.format = '') AND cc.file_type = 'video'");
if($res_vids) { while($r = mysqli_fetch_assoc($res_vids)) $avail_videos[] = $r; }

$avail_pdfs = [];
$res_pdfs = mysqli_query($conn, "SELECT c.title, cc.file_path FROM courses c INNER JOIN course_contents cc ON c.id = cc.course_id WHERE c.format = 'PDF' AND cc.file_type = 'pdf'");
if($res_pdfs) { while($r = mysqli_fetch_assoc($res_pdfs)) $avail_pdfs[] = $r; }

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bài học toàn diện | Vanhoc247</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --netflix-black: #141414;
            --netflix-red: #E50914;
            --border: rgba(255, 255, 255, 0.1);
        }
        
        body { background-color: var(--netflix-black); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; min-height: 100vh; padding-bottom: 50px; position: relative; }
        
        /* Hero Section */
        .hero-banner {
            position: relative; height: 60vh; background-size: cover; background-position: center; border-bottom: 1px solid var(--border); margin-bottom: 40px;
        }
        .hero-vignette {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, var(--netflix-black) 0%, transparent 60%), linear-gradient(to right, var(--netflix-black) 0%, transparent 70%);
        }
        .hero-content {
            position: absolute; bottom: 20%; left: 5%; width: 60%; z-index: 10;
        }
        .hero-title { font-size: 3.5rem; font-weight: 800; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero-desc { font-size: 1.1rem; color: #ccc; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        
        .btn-upload { background: var(--netflix-red); border: none; font-weight: 700; color: white;}
        .btn-upload:hover { background: #b80710; color: white;}
        
        /* Grid */
        .lesson-grid { padding: 0 4%; }
        .section-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #e5e5e5; }
        
        .lesson-card {
            position: relative; border-radius: 8px; overflow: hidden; transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); box-shadow: 0 4px 10px rgba(0,0,0,0.5); background: #222; margin-bottom: 20px;
        }
        .lesson-card img { width: 100%; aspect-ratio: 16/9; object-fit: cover; }
        .lesson-card:hover { transform: scale(1.03); z-index: 10; box-shadow: 0 10px 25px rgba(0,0,0,0.9); border: 1px solid rgba(255,255,255,0.2); }
        .lesson-card .card-body { padding: 15px; }
        .card-title { font-weight: 700; margin-bottom: 5px; font-size: 1.1rem; }
        .card-meta { font-size: 0.85rem; color: #aaa; margin-bottom: 10px; }
        .btn-delete { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: white; transition: 0.3s; text-decoration: none;}
        .btn-delete:hover { background: var(--netflix-red); color: white; }

        /* Modal Styles */
        .modal-content { background: var(--netflix-black); border: 1px solid var(--border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--border); }
        .modal-footer { border-top: 1px solid var(--border); }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff; }
        .form-control:focus, .form-select:focus { background: #2a2a2a; color: #fff; border-color: var(--netflix-red); box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25); }

        .admin-header {
            position: absolute; top: 0; left: 0; right: 0; padding: 20px 4%; z-index: 100; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="fw-bold m-0"><span style="color: var(--netflix-red);"><i class="fas fa-layer-group"></i></span> Quản lý Bài Học (Lesson)</h4>
        </div>
        <button class="btn btn-upload rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-plus-circle me-2"></i> Tạo Bài Học Mới
        </button>
    </div>

    <?php if($featured): ?>
        <?php $featured_img = !empty($featured['image_url']) ? "../" . $featured['image_url'] : "https://images.unsplash.com/photo-1510531704581-5b2870972060?q=80&w=1500"; ?>
        <div class="hero-banner" style="background-image: url('<?= $featured_img ?>');">
            <div class="hero-vignette"></div>
            <div class="hero-content">
                <div class="badge bg-danger mb-2">Đ Mới Nhất</div>
                <h1 class="hero-title"><?= htmlspecialchars($featured['title']) ?></h1>
                <p class="hero-desc mb-4"><?= htmlspecialchars($featured['description']) ?></p>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary"><i class="fas fa-user-edit"></i> <?= htmlspecialchars($featured['author'] ?? 'Admin') ?></span>
                    <span class="text-secondary"><i class="fas fa-eye"></i> <?= $featured['views'] ?? 0 ?> Views</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-banner" style="background: #222; display:flex; align-items:center; justify-content:center;">
            <div class="text-center text-muted">
                <i class="fas fa-cubes fa-3x mb-3"></i>
                <h2>Chưa có bài học nào</h2>
                <p>Khởi tạo bài học bao gồm Video + PDF + Quiz ngay thôi!</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="lesson-grid">
        <h3 class="section-title">Danh sách bài học tổng hợp</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
            <?php foreach($lessons as $lesson): ?>
                <?php $card_img = !empty($lesson['image_url']) ? "../" . $lesson['image_url'] : "https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=600"; ?>
                <div class="col">
                    <div class="lesson-card">
                        <img src="<?= $card_img ?>" alt="<?= htmlspecialchars($lesson['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($lesson['title']) ?></h5>
                            <div class="card-meta d-flex justify-content-between">
                                <span><i class="fas fa-user"></i> <?= htmlspecialchars($lesson['author'] ?? 'Trống') ?></span>
                                <span><?= date('d/m/Y', strtotime($lesson['created_at'])) ?></span>
                            </div>
                            <div class="badge border border-secondary text-secondary"><?= $lesson['category'] ?></div>
                            <a href="../lesson_detail.php?id=<?= $lesson['id'] ?>" target="_blank" class="btn btn-outline-light btn-sm mt-3 w-100">Xem Review</a>
                        </div>
                        <a href="?delete_id=<?= $lesson['id'] ?>" class="btn-delete" onclick="return confirm('Xóa bài học này?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">🚀 Đóng gói Bài Học Trọn Vẹn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label text-secondary small">Tiêu đề bài học</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Phân tích trọn vẹn Vợ Nhặt...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-secondary small">Tác giả biên soạn</label>
                        <input type="text" name="author" class="form-control" placeholder="Tên thầy cô/Tác giả">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-secondary small">Mô tả chi tiết</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Overview về lesson..."></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small">Thuộc chuyên đề</label>
                        <select name="category" class="form-select">
                            <option value="Lớp 12">Ngữ Văn 12</option>
                            <option value="Lớp 11">Ngữ Văn 11</option>
                            <option value="Kỹ năng viết">Kỹ năng viết lách</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small">Link Quiz / Bài trắc nghiệm (Tùy chọn)</label>
                        <input type="url" name="quiz_link" class="form-control" placeholder="Link Quizizz, Google Form...">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Thumbnail (Ảnh bìa nhận diện)</label>
                    <input type="file" name="course_image" class="form-control" accept="image/*" required>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded h-100" style="background: rgba(46, 204, 113, 0.1); border: 1px dashed #2ecc71;">
                            <label class="form-label text-success fw-bold"><i class="fas fa-video me-1"></i> Video Bài Giảng (Từ kho Videos)</label>
                            <select name="course_video" class="form-select bg-transparent border-0 text-white" required>
                                <option value="" class="text-dark">-- Chọn Video --</option>
                                <?php foreach($avail_videos as $vid): ?>
                                    <option value="<?= htmlspecialchars($vid['file_path']) ?>" class="text-dark"><?= htmlspecialchars($vid['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded h-100" style="background: rgba(229, 9, 20, 0.1); border: 1px dashed var(--netflix-red);">
                            <label class="form-label text-danger fw-bold"><i class="fas fa-file-pdf me-1"></i> File Học Liệu (Từ kho Tài liệu)</label>
                            <select name="course_pdf" class="form-select bg-transparent border-0 text-white">
                                <option value="" class="text-dark">-- Mảng không đính kèm --</option>
                                <?php foreach($avail_pdfs as $pdf): ?>
                                    <option value="<?= htmlspecialchars($pdf['file_path']) ?>" class="text-dark"><?= htmlspecialchars($pdf['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="upload_lesson" class="btn btn-upload w-100 py-3 fs-5">LƯU BÀI HỌC VÀ LÊN SÓNG</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
