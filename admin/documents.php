<?php
include 'includes/check_role.php';
require_once '../includes/db.php';

// 1. XỬ LÝ UPLOAD TÀI LIỆU
if(isset($_POST['upload_document'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = 0; // Set mặc định 0 
    
    $img_path = '';
    if(!empty($_FILES['course_image']['name'])) {
        $img_name = time() . "_" . basename($_FILES["course_image"]["name"]);
        if(move_uploaded_file($_FILES["course_image"]["tmp_name"], "../uploads/images/" . $img_name)) {
            $img_path = "uploads/images/" . $img_name;
        }
    }

    // Insert dạng tài liệu là PDF
    $sql = "INSERT INTO courses (title, description, category, image_url, format, price) 
            VALUES ('$title', '$desc', '$category', '$img_path', 'PDF', $price)";
    
    if(mysqli_query($conn, $sql)) {
        $course_id = mysqli_insert_id($conn);
        
        // Upload File PDF
        if(!empty($_FILES['course_pdf']['name'])) {
            $pdf_name = time() . "_" . basename($_FILES["course_pdf"]["name"]);
            if(move_uploaded_file($_FILES["course_pdf"]["tmp_name"], "../uploads/pdfs/" . $pdf_name)) {
                $p_path = "uploads/pdfs/" . $pdf_name;
                // Thêm vào bảng course_contents file_type = pdf
                mysqli_query($conn, "INSERT INTO course_contents (course_id, title, file_path, file_type) VALUES ($course_id, 'Tài liệu hướng dẫn', '$p_path', 'pdf')");
            }
        }
    }
    header("Location: documents.php");
    exit();
}

// 2. XỬ LÝ XÓA
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM course_contents WHERE course_id = $id");
    mysqli_query($conn, "DELETE FROM courses WHERE id = $id");
    header("Location: documents.php"); 
    exit();
}

// 3. LẤY DỮ LIỆU TÀI LIỆU PDF
$query = "SELECT c.*, cc.file_path as pdf_path 
          FROM courses c 
          INNER JOIN course_contents cc ON c.id = cc.course_id 
          WHERE c.format = 'PDF' 
          AND cc.file_type = 'pdf' 
          ORDER BY c.id DESC";
$res_docs = mysqli_query($conn, $query);

$documents = [];
while($row = mysqli_fetch_assoc($res_docs)) {
    $documents[] = $row;
}
$featured = !empty($documents) ? $documents[0] : null;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư viện Tài Liệu | Vanhoc247</title>
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
        .main-content { margin-left: 280px; min-height: 100vh; padding-bottom: 50px; position: relative; }
        
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
        .btn-upload { background: var(--netflix-red); border: none; font-weight: 700; color: white;}
        .btn-upload:hover { background: #b80710; color: white;}
        
        /* Grid */
        .doc-grid { padding: 0 4%; }
        .section-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #e5e5e5; }
        
        .doc-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            background: #222;
        }
        /* Thay vì 16:9, tài liệu nên có form dọc hơn chút hoặc giữ nguyên cho nhất quán */
        .doc-card img { width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .doc-card:hover {
            transform: scale(1.05);
            z-index: 10;
            box-shadow: 0 10px 25px rgba(0,0,0,0.9);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .doc-card .card-overlay {
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
        .doc-card:hover .card-overlay { opacity: 1; }
        .card-title { font-weight: 700; margin-bottom: 5px; font-size: 1rem; }
        .card-meta { font-size: 0.8rem; color: #aaa; }
        .btn-delete { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s; text-decoration: none;}
        .btn-delete:hover { background: var(--netflix-red); color: white; }
        .doc-card:hover .btn-delete { opacity: 1; text-decoration: none;}

        /* Modal Styles */
        .modal-content { background: var(--netflix-black); border: 1px solid var(--border); border-radius: 12px; }
        .modal-header { border-bottom: 1px solid var(--border); }
        .modal-footer { border-top: 1px solid var(--border); }
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff; }
        .form-control:focus, .form-select:focus { background: #2a2a2a; color: #fff; border-color: var(--netflix-red); box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25); }
        
        /* Modal Đọc PDF */
        #pdfPlayModal .modal-content { background: #2a2a2a; border: none; height: 90vh;}
        #pdfPlayModal .modal-body { padding: 0; border-radius: 12px; overflow: hidden; height: 100%;}
        #pdfViewer { width: 100%; height: 100%; border: none;}

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
            <h4 class="fw-bold m-0"><span style="color: var(--netflix-red);"><i class="fas fa-book-open"></i></span> Thư viện Tài liệu</h4>
        </div>
        <button class="btn btn-upload rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-file-upload me-2"></i> Upload tài liệu
        </button>
    </div>

    <?php if($featured): ?>
        <?php 
            $featured_img = !empty($featured['image_url']) ? "../" . $featured['image_url'] : "https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=1500";
        ?>
        <div class="hero-banner" style="background-image: url('<?= $featured_img ?>');">
            <div class="hero-vignette"></div>
            <div class="hero-content">
                <div class="badge bg-danger mb-2">Đ Mới Nhất</div>
                <h1 class="hero-title"><?= htmlspecialchars($featured['title']) ?></h1>
                <p class="hero-desc mb-4"><?= htmlspecialchars($featured['description']) ?></p>
                <div class="d-flex">
                    <button class="btn-play" onclick="readPdf('../<?= $featured['pdf_path'] ?>')">
                        <i class="fas fa-book-reader me-2"></i> Đọc Ngay
                    </button>
                    <!-- <button class="btn-more">
                        <i class="fas fa-info-circle me-2"></i> Thông tin
                    </button> -->
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="hero-banner" style="background: #222; display:flex; align-items:center; justify-content:center;">
            <div class="text-center text-muted">
                <i class="fas fa-file-pdf fa-3x mb-3"></i>
                <h2>Chưa có tài liệu nào</h2>
                <p>Hãy tải lên tài liệu PDF để chia sẻ kiến thức đầu tiên!</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="doc-grid">
        <h3 class="section-title">Kho Tài Liệu Tinh Hoa</h3>
        
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4 mt-2">
            <?php foreach($documents as $doc): ?>
                <?php 
                    $doc_img = !empty($doc['image_url']) ? "../" . $doc['image_url'] : "https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=600";
                ?>
                <div class="col">
                    <div class="doc-card" onclick="readPdf('../<?= $doc['pdf_path'] ?>')">
                        <img src="<?= $doc_img ?>" alt="<?= htmlspecialchars($doc['title']) ?>">
                        <div class="card-overlay">
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($doc['title']) ?></h5>
                            <div class="card-meta">
                                <span class="text-success fw-bold me-2"><i class="fas fa-fire"></i> Hot</span> 
                                <span><?= date('Y', strtotime($doc['created_at'])) ?></span>
                                <span class="badge border border-secondary text-secondary ms-2"><?= $doc['category'] ?></span>
                            </div>
                        </div>
                        <!-- Nâng z-index nút Edit/Delete để click k bị gọi hàm block -->
                        <a href="?delete_id=<?= $doc['id'] ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Chắc chắn muốn xóa tài liệu này chứ?');">
                            <i class="fas fa-times"></i>
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
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">📖 Đưa Sách Mới Trình Làng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-secondary small">Tên Tài Liệu</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Đề thi học sinh giỏi quốc gia...">
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary small">Tóm tắt nội dung</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Giới thiệu sơ bộ..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small">Phân loại</label>
                        <select name="category" class="form-select">
                            <option value="Lớp 12">Tài liệu Lớp 12</option>
                            <option value="Lớp 11">Tài liệu Lớp 11</option>
                            <option value="Đề Thi">Kho Đề Thi</option>
                            <option value="Sách quý">Sách hiếm sưu tầm</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small">Ảnh Bìa Nhận Diện (Cover Sách gốc)</label>
                        <input type="file" name="course_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="mb-3 p-3 rounded" style="background: rgba(229, 9, 20, 0.1); border: 1px dashed var(--netflix-red);">
                    <label class="form-label text-danger fw-bold"><i class="fas fa-file-pdf me-1"></i> Trái Tim Của Kiến Thức (Tệp PDF)</label>
                    <input type="file" name="course_pdf" class="form-control bg-transparent border-0 text-white" accept="application/pdf" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="upload_document" class="btn btn-upload w-100 py-2 fs-5">TẢI LÊN NGAY</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Đọc PDF -->
<div class="modal fade" id="pdfPlayModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" style="color: #aaa;"><i class="fas fa-glasses me-2"></i> Chế độ đọc tập trung</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body position-relative mt-2 p-3">
                <iframe id="pdfViewer" src="" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Logic gọi popup hiển thị PDF
    const pdfModal = new bootstrap.Modal(document.getElementById('pdfPlayModal'));
    const pdfViewer = document.getElementById('pdfViewer');

    function readPdf(pdfUrl) {
        // Có thể dính lỗi CORS trên 1 số brower nếu gọi file local, iframe cơ bản với src file pdf sẽ chạy trình reader của chrome.
        pdfViewer.src = pdfUrl;
        pdfModal.show();
    }

    document.getElementById('pdfPlayModal').addEventListener('hidden.bs.modal', function () {
        // Dọn resource lúc tắt
        pdfViewer.src = "";
    });
</script>
</body>
</html>
