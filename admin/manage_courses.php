<?php 
include 'includes/check_role.php';
require_once '../includes/db.php';

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'Video';

// 1. XỬ LÝ THÊM MỚI
if(isset($_POST['add_course'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $cate = $_POST['category'];
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    
    $img_path = mysqli_real_escape_string($conn, $_POST['image_url']);
    if(!empty($_FILES['course_image']['name'])) {
        $img_name = time() . "_" . basename($_FILES["course_image"]["name"]);
        if(move_uploaded_file($_FILES["course_image"]["tmp_name"], "../uploads/images/" . $img_name)) {
            $img_path = "uploads/images/" . $img_name;
        }
    }

    // Fix lỗi Unknown column 'price' bằng cách thêm trực tiếp vào câu lệnh INSERT
    $sql = "INSERT INTO courses (title, description, category, image_url, price) 
            VALUES ('$title', '$desc', '$cate', '$img_path', '$price')";
    
    if(mysqli_query($conn, $sql)) {
        $course_id = mysqli_insert_id($conn);
        
        // Upload Video & Cập nhật format
        if(!empty($_FILES['course_video']['name'])) {
            $video_name = time() . "_" . basename($_FILES["course_video"]["name"]);
            if(move_uploaded_file($_FILES["course_video"]["tmp_name"], "../uploads/videos/" . $video_name)) {
                $v_path = "uploads/videos/" . $video_name;
                mysqli_query($conn, "INSERT INTO course_contents (course_id, title, file_path, file_type) VALUES ($course_id, 'Video bài giảng', '$v_path', 'video')");
                mysqli_query($conn, "UPDATE courses SET format = 'Video' WHERE id = $course_id");
            }
        }
        
        // Upload PDF
        if(!empty($_FILES['course_pdf']['name'])) {
            $pdf_name = time() . "_" . basename($_FILES["course_pdf"]["name"]);
            if(move_uploaded_file($_FILES["course_pdf"]["tmp_name"], "../uploads/pdfs/" . $pdf_name)) {
                $p_path = "uploads/pdfs/" . $pdf_name;
                mysqli_query($conn, "INSERT INTO course_contents (course_id, title, file_path, file_type) VALUES ($course_id, 'Tài liệu hướng dẫn', '$p_path', 'pdf')");
                if(empty($_FILES['course_video']['name'])) {
                    mysqli_query($conn, "UPDATE courses SET format = 'PDF' WHERE id = $course_id");
                }
            }
        }
    }
    header("Location: manage_courses.php?tab=" . $current_tab);
    exit();
}

// 2. XỬ LÝ XÓA
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM course_contents WHERE course_id = $id");
    mysqli_query($conn, "DELETE FROM courses WHERE id = $id");
    header("Location: manage_courses.php?tab=" . $current_tab); 
    exit();
}

// 3. TRUY VẤN DỮ LIỆU THẬT THEO TAB
if ($current_tab == 'Video') {
    $query = "SELECT * FROM courses WHERE format = 'Video' OR format IS NULL OR format = '' ORDER BY id DESC";
} else {
    $query = "SELECT * FROM courses WHERE format = 'PDF' ORDER BY id DESC";
}
$courses = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thư viện | MuseOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%); --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.08); }
        body { background: #050505; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; }
        .main-content { padding: 40px; margin-left: 280px; }
        .glass-card { background: var(--glass); backdrop-filter: blur(15px); border: 1px solid var(--border); border-radius: 24px; padding: 25px; }
        .course-img { width: 80px; height: 50px; object-fit: cover; border-radius: 10px; }
        .btn-muse { background: var(--muse-gradient); border: none; color: #fff; font-weight: 700; border-radius: 12px; padding: 10px 20px; }
        .nav-link { color: #aaa; font-weight: 600; border-radius: 10px !important; margin-right: 10px; }
        .nav-link.active { background: var(--muse-gradient) !important; color: #fff !important; border: none !important; }
        .modal-content { background: #111; border: 1px solid var(--border); border-radius: 20px; }
        .form-control { background: #1a1a1a; border: 1px solid var(--border); color: #fff; }
        .form-control:focus { background: #222; color: #fff; border-color: #f5576c; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold">Quản lý <span style="color: #f5576c;">Thư viện</span></h2>
            <p class="text-secondary">Dữ liệu được cập nhật thời gian thực từ Database</p>
        </div>
        <button class="btn btn-muse" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="fas fa-plus me-2"></i> THÊM NỘI DUNG
        </button>
    </div>

    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_tab == 'Video') ? 'active' : ''; ?>" href="?tab=Video">Video Bài Giảng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_tab == 'PDF') ? 'active' : ''; ?>" href="?tab=PDF">Tài Liệu PDF</a>
        </li>
    </ul>

    <div class="glass-card">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr class="text-secondary small">
                    <th>ẢNH</th>
                    <th>TÊN BÀI GIẢNG</th>
                    <th>PHÂN LOẠI</th>
                    <th>GIÁ TIỀN</th>
                    <th class="text-end">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($courses) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($courses)): ?>
                    <tr>
                        <td><img src="../<?php echo $row['image_url']; ?>" class="course-img" onerror="this.src='https://placehold.co/80x50/222/555?text=No+Img'"></td>
                        <td>
                            <div class="fw-bold"><?php echo $row['title']; ?></div>
                            <div class="small text-secondary"><?php echo substr($row['description'], 0, 50); ?>...</div>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo $row['category']; ?></span></td>
                        <td class="text-info fw-bold"><?php echo number_format($row['price']); ?>đ</td>
                        <td class="text-end">
                            <a href="?delete_id=<?php echo $row['id']; ?>&tab=<?php echo $current_tab; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Xóa nội dung này?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-secondary">Chưa có bài giảng nào trong mục này.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <div class="modal-header border-0">
                <h5 class="modal-title">Đăng tải tri thức mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="small text-secondary">Tiêu đề</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small text-secondary">Giá bán (VNĐ)</label>
                        <input type="number" name="price" class="form-control" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-secondary">Mô tả</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small text-secondary">Phân loại</label>
                        <select name="category" class="form-control">
                            <option value="Lớp 12">Lớp 12</option>
                            <option value="Lớp 11">Lớp 11</option>
                            <option value="Kỹ năng">Kỹ năng viết</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-secondary">Ảnh Thumbnail</label>
                        <input type="file" name="course_image" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small text-danger">File Video (.mp4)</label>
                        <input type="file" name="course_video" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-success">File PDF (.pdf)</label>
                        <input type="file" name="course_pdf" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="add_course" class="btn btn-muse w-100">XÁC NHẬN ĐĂNG TẢI</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>