<?php 
include 'includes/db.php';
include 'includes/header.php';

// 1. Lấy ID khóa học từ URL
if(isset($_GET['id'])) {
    $course_id = intval($_GET['id']);
    
    // 2. Truy vấn thông tin khóa học
    $sql_course = "SELECT * FROM courses WHERE id = $course_id";
    $result_course = mysqli_query($conn, $sql_course);
    $course = mysqli_fetch_assoc($result_course);

    if(!$course) {
        die("Khóa học không tồn tại!");
    }

    // 3. Truy vấn các file nội dung (Video, PDF)
    $sql_contents = "SELECT * FROM course_contents WHERE course_id = $course_id";
    $contents = mysqli_query($conn, $sql_contents);
} else {
    header("Location: index.php");
}
?>

<div class="container my-5 pt-5">
    <div class="row">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active"><?php echo $course['category']; ?></li>
                </ol>
            </nav>
            
            <h2 class="fw-bold mb-4"><?php echo $course['title']; ?></h2>

            <?php 
            // Tìm xem có video nào không để hiển thị
            $has_video = false;
            mysqli_data_seek($contents, 0); // Reset con trỏ dữ liệu
            while($item = mysqli_fetch_assoc($contents)) {
                if($item['file_type'] == 'video') {
                    $has_video = $item['file_path'];
                    break;
                }
            }
            ?>

            <div class="card border-0 shadow-lg overflow-hidden rounded-4 mb-4">
                <?php if($has_video): ?>
                    <div class="ratio ratio-16x9 bg-black">
                        <video controls controlsList="nodownload">
                            <source src="<?php echo $has_video; ?>" type="video/mp4">
                            Trình duyệt của bạn không hỗ trợ xem video.
                        </video>
                    </div>
                <?php else: ?>
                    <img src="<?php echo $course['image_url']; ?>" class="img-fluid" style="height: 400px; object-fit: cover;">
                    <div class="p-4 text-center">
                        <p class="text-muted italic">Khóa học này hiện chưa có video bài giảng.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h5 class="fw-bold border-bottom pb-3 mb-3">Giới thiệu khóa học</h5>
                <p class="text-muted"><?php echo nl2br($course['description']); ?></p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 100px;">
                <div class="card-header bg-primary text-white py-3 border-0">
                    <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i>Tài liệu học tập</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php 
                        mysqli_data_seek($contents, 0);
                        $found_pdf = false;
                        while($item = mysqli_fetch_assoc($contents)): 
                            if($item['file_type'] == 'pdf'):
                                $found_pdf = true;
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-2 fa-lg"></i>
                                    <span class="small fw-bold"><?php echo $item['title']; ?></span>
                                </div>
                                <a href="<?php echo $item['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill">
                                    <i class="fas fa-download"></i> Tải về
                                </a>
                            </li>
                        <?php 
                            endif;
                        endwhile; 
                        
                        if(!$found_pdf) echo "<li class='list-group-item text-muted small px-0'>Chưa có tài liệu đính kèm.</li>";
                        ?>
                    </ul>
                    <hr>
                    <div class="text-center">
                        <p class="small text-muted mb-3">Mọi thắc mắc về bài giảng, hãy nhắn tin cho Trợ lý AI nhé!</p>
                        <a href="ai_chat.php" class="btn btn-warning w-100 rounded-pill fw-bold">Chat với AI ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>