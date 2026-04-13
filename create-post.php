<?php 
session_start();
include 'includes/db.php';

// Bảo vệ: Chỉ người dùng đã đăng nhập mới được đăng bài
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
if (isset($_POST['submit_post'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO posts (user_id, title, content, category) VALUES ('$user_id', '$title', '$content', '$category')";
        if (mysqli_query($conn, $sql)) {
            header("Location: forum.php"); // Đăng xong thì đẩy sang diễn đàn
            exit();
        } else {
            $error = "Lỗi hệ thống, không thể đăng bài!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ tiêu đề và nội dung!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h2 class="font-serif fw-bold mb-4"><i class="fas fa-pen-nib me-2"></i>Viết bài mới</h2>
                
                <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="fw-bold small mb-2">Tiêu đề bài viết</label>
                        <input type="text" name="title" class="form-control py-2" placeholder="Ví dụ: Cảm nhận về bài thơ Sóng..." required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small mb-2">Chuyên mục</label>
                        <select name="category" class="form-select">
                            <option value="Thơ">Thơ</option>
                            <option value="Văn xuôi">Văn xuôi</option>
                            <option value="Phê bình">Phê bình văn học</option>
                            <option value="Tâm sự">Góc tâm sự</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small mb-2">Nội dung bài viết</label>
                        <textarea name="content" class="form-control" rows="10" placeholder="Hãy để những dòng cảm xúc tuôn trào..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="forum.php" class="btn btn-light rounded-pill px-4">Hủy bỏ</a>
                        <button type="submit" name="submit_post" class="btn btn-dark rounded-pill px-5 shadow">ĐĂNG BÀI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>