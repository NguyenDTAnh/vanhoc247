<?php 
include 'includes/check_role.php';
require_once '../includes/db.php';

if(isset($_POST['add_news'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $summary = mysqli_real_escape_string($conn, $_POST['summary']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    $img_name = '';
    if(!empty($_FILES['image']['name'])) {
        // Đường dẫn tương đối từ admin ra folder gốc
        $upload_dir = '../assets/img/news/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $img_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir . $img_name);
    }

    $sql = "INSERT INTO news (title, summary, content, image, author, category) 
            VALUES ('$title', '$summary', '$content', '$img_name', '$author', '$category')";
    
    mysqli_query($conn, $sql);
    header("Location: manage_news.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm bài viết mới | Vanhoc247</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    
    <style>
        :root {
            --netflix-black: #141414;
            --netflix-red: #E50914;
            --border: rgba(255, 255, 255, 0.1);
            --ck-color-base-background: #222 !important;
            --ck-color-base-text: #fff !important;
            --ck-color-base-border: #444 !important;
            --ck-color-toolbar-background: #1a1a1a !important;
        }
        
        body { background-color: var(--netflix-black); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; min-height: 100vh; padding: 40px; }
        
        .form-control, .form-select { background: #222; border: 1px solid #444; color: #fff; border-radius: 8px;}
        .form-control:focus { background: #2a2a2a; color: #fff; border-color: var(--netflix-red); box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25); }
        
        .btn-submit { background: var(--netflix-red); border: none; font-weight: 700; color: white; padding: 12px 30px; border-radius: 8px;}
        .btn-submit:hover { background: #b80710; color: white;}
        
        .btn-back { background: rgba(255,255,255,0.1); color: white; border: none; font-weight: 600; padding: 12px 30px; border-radius: 8px; text-decoration: none;}
        .btn-back:hover { background: rgba(255,255,255,0.2); color: white; }

        /* CKEditor Custom height */
        .ck-editor__editable_inline { min-height: 400px; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="fas fa-feather-alt text-danger me-2"></i> Chắp Bút Tác Phẩm</h3>
        <a href="manage_news.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i> Trở về Khung Soạn Thảo</a>
    </div>

    <div class="card" style="background: #111; border: 1px solid var(--border); border-radius: 16px;">
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small text-uppercase">Tiêu đề bài viết</label>
                        <input type="text" name="title" class="form-control form-control-lg" required placeholder="Nhập tiêu đề giật tít...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-secondary small text-uppercase">Phân loại</label>
                        <select name="category" class="form-select form-select-lg" required>
                            <option value="Kỳ thi THPT">Kỳ thi THPT</option>
                            <option value="Văn học 24/7">Văn học 24/7</option>
                            <option value="Thông báo">Thông báo</option>
                            <option value="Sự kiện">Sự kiện</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label text-secondary small text-uppercase">Tác giả</label>
                        <input type="text" name="author" class="form-control form-control-lg" required value="Ban biên tập">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small text-uppercase">Tóm tắt (Sapo)</label>
                    <textarea name="summary" class="form-control" rows="3" placeholder="Đoạn mở đầu làm nóng bài viết..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small text-uppercase">Ảnh Bìa (Cover)</label>
                    <input type="file" name="image" class="form-control form-control-lg" accept="image/*" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-secondary small text-uppercase fw-bold"><i class="fas fa-pen-nib"></i> Phóng Bút (Nội Dung)</label>
                    <textarea name="content" id="editor"></textarea>
                </div>

                <div class="text-end border-top pt-3" style="border-color: var(--border) !important;">
                    <button type="submit" name="add_news" class="btn btn-submit fs-5"><i class="fas fa-paper-plane me-2"></i> Xuất Bản Trực Tiếp</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Khởi tạo CKEditor 5
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'insertTable', 'undo', 'redo' ]
        })
        .catch(error => {
            console.error(error);
        });
</script>

</body>
</html>
