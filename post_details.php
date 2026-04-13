<?php 
include 'includes/db.php'; 
include 'includes/header.php'; 

$sql_recent = "SELECT posts.*, users.username 
               FROM posts 
               JOIN users ON posts.user_id = users.id 
               ORDER BY posts.created_at DESC 
               LIMIT 3";

$recent_posts = false;
if (isset($conn)) {
    $recent_posts = mysqli_query($conn, $sql_recent);
}
?>

<div class="container-fluid p-0">
    <div class="position-relative bg-dark text-white text-center py-5" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?auto=format&fit=crop&w=1800&q=80') center/cover; min-height: 550px; display: flex; align-items: center; justify-content: center;">
        <div class="position-relative z-index-1">
            <h1 class="display-2 fw-bold mb-4 animate__animated animate__fadeInDown" style="font-family: 'Playfair Display', serif;">Vanhoc247: Học Văn Là Để Cảm Nhận</h1>
            <p class="lead mx-auto mb-5 animate__animated animate__fadeInUp" style="max-width: 800px; font-size: 1.35rem;">Nền tảng tương tác số hỗ trợ học tập và lan tỏa tình yêu văn chương dành cho thế hệ học sinh mới.</p>
            <div class="mt-4 animate__animated animate__fadeInUp animate__delay-1s">
                <a href="#featured" class="btn btn-primary btn-lg px-5 rounded-pill me-3 shadow-lg">Khám phá ngay</a>
                <a href="forum.php" class="btn btn-outline-light btn-lg px-5 rounded-pill shadow-lg">Diễn đàn thảo luận</a>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4">Lộ trình bứt phá môn Văn</h3>
            <p class="text-muted">Đang cập nhật các bài giảng mới nhất...</p>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 bg-light">
                    <h5 class="fw-bold mb-3"><i class="fas fa-comments text-primary me-2"></i>Thảo luận mới trên Vanhoc247</h5>
                    <div class="list-group list-group-flush bg-transparent">
                        <?php if($recent_posts && mysqli_num_rows($recent_posts) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($recent_posts)): ?>
                                <a href="post_details.php?id=<?php echo $row['id']; ?>" class="list-group-item list-group-item-action border-0 px-0 py-3 bg-transparent">
                                    <div class="fw-bold text-dark mb-1 text-truncate"><?php echo htmlspecialchars($row['content']); ?></div>
                                    <small class="text-muted">Bởi <?php echo $row['username']; ?> • <?php echo date('H:i', strtotime($row['created_at'])); ?></small>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="small text-muted">Chưa có bài viết nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>