<?php 
include 'includes/header.php'; 
include_once 'includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM news WHERE id = $id";
$res = mysqli_query($conn, $sql);
$news = mysqli_fetch_assoc($res);

if(!$news) {
    echo "<div class='container py-5 text-center text-white'><h2>Bài viết không tồn tại.</h2></div>";
    include 'includes/footer.php';
    exit;
}

$img_val = $news['image'];
if (!empty($img_val)) {
    $img = (strpos($img_val, 'http') === 0) ? $img_val : "assets/img/news/" . $img_val;
} else {
    $img = "https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=1200";
}
?>

<style>
    body { background-color: #0a0a0c; color: #f0f0f0; font-family: 'Inter', sans-serif;}
    .detail-header {
        position: relative;
        padding: 100px 0 60px;
        min-height: 50vh;
        background-image: url('<?= $img ?>');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: flex-end;
    }
    .detail-overlay {
        position: absolute; top:0;left:0;right:0;bottom:0;
        background: linear-gradient(to top, #0a0a0c 10%, rgba(10,10,12,0.8) 100%);
    }
    .detail-content { position: relative; z-index: 10;}
    .article-body {
        font-size: 1.15rem;
        line-height: 1.8;
        color: #d1d5db;
    }
    .article-body img { max-width: 100%; height: auto; border-radius: 12px; margin: 20px 0;}
    /* Fix format CKEditor Output */
    .article-body h2, .article-body h3 { color: #fff; font-weight: bold; margin-top: 30px;}
    .article-body blockquote { border-left: 4px solid #4facfe; padding-left: 20px; font-style: italic; color: #9ca3af; }
</style>

<div class="detail-header">
    <div class="detail-overlay"></div>
    <div class="container detail-content">
        <span class="badge bg-primary mb-3 px-3 py-2 fw-bold">TIN TỨC</span>
        <h1 class="display-4 fw-bold text-white mb-3" style="max-width: 900px;"><?= htmlspecialchars($news['title']) ?></h1>
        <div class="text-white-50 mt-4 d-flex align-items-center gap-4">
            <span><i class="fas fa-user-edit me-2"></i><?= htmlspecialchars($news['author'] ?? 'Ban biên tập') ?></span>
            <span><i class="fas fa-calendar-alt me-2"></i><?= date('d/m/Y', strtotime($news['created_at'])) ?></span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <p class="fs-5 text-white fw-bold mb-5" style="border-left: 3px solid #ff6b6b; padding-left: 20px;">
                <?= htmlspecialchars($news['summary']) ?>
            </p>
            <div class="article-body">
                <?= $news['content'] // Xuất raw nội dung từ CKEditor ?>
            </div>
            
            <div class="mt-5 pt-4 border-top border-secondary">
                <a href="news.php" class="btn btn-outline-light rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> Quay lại trang Tin tức</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
