<?php 
include 'includes/check_role.php';
require_once '../includes/db.php';

// XỬ LÝ XÓA
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM news WHERE id = $id");
    header("Location: manage_news.php"); 
    exit();
}

$query = "SELECT * FROM news ORDER BY created_at DESC";
$res_news = mysqli_query($conn, $query);

$news_list = [];
while($row = mysqli_fetch_assoc($res_news)) {
    $news_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Báo & Sự Kiện | Vanhoc247</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --netflix-black: #141414;
            --netflix-red: #E50914;
            --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            --border: rgba(255, 255, 255, 0.1);
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

        .btn-upload { background: var(--netflix-red); border: none; font-weight: 700; color: white;}
        .btn-upload:hover { background: #b80710; color: white; transform: scale(1.05); transition: 0.2s;}

        .news-grid { padding: 0 4%; margin-top: 30px;}
        .section-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #e5e5e5; display: flex; align-items: center; }

        .news-card {
            background: #222;
            border-radius: 12px;
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid var(--border);
            height: 100%;
            display: flex; flex-direction: column;
            position: relative;
        }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.6); border-color: rgba(255,255,255,0.3); }
        .news-img { width: 100%; height: 200px; object-fit: cover; }
        
        .news-body { padding: 20px; flex-grow: 1; display:flex; flex-direction: column;}
        .news-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;}
        .news-summary { font-size: 0.9rem; color: #aaa; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .news-meta { font-size: 0.8rem; color: #777; margin-top: auto; display: flex; justify-content: space-between; align-items: center;}

        .btn-delete { background: rgba(229, 9, 20, 0.2); color: #E50914; border-radius: 5px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s;}
        .btn-delete:hover { background: var(--netflix-red); color: white; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="fw-bold m-0"><span style="color: var(--netflix-red);"><i class="fas fa-newspaper"></i></span> Tòa Soạn Vanhoc247</h4>
        </div>
        <a href="add_news.php" class="btn btn-upload rounded-pill px-4 text-decoration-none">
            <i class="fas fa-pen-nib me-2"></i> Thêm bài viết mới
        </a>
    </div>

    <div class="news-grid">
        <h3 class="section-title"><i class="fas fa-book-reader text-danger me-2"></i> Bài Viết Đã Đăng</h3>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 mt-2">
            <?php if(empty($news_list)): ?>
                <div class="col-12 text-center text-secondary py-5">
                    <i class="fas fa-feather fa-3x mb-3 opacity-50"></i>
                    <h5>Tòa soạn đang vắng bóng tác phẩm...</h5>
                </div>
            <?php endif; ?>

            <?php foreach($news_list as $n): ?>
                <?php 
                    $img_val = $n['image'];
                    if (!empty($img_val)) {
                        $thumb = (strpos($img_val, 'http') === 0) ? $img_val : "../assets/img/news/" . $img_val;
                    } else {
                        $thumb = "https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=600";
                    }
                ?>
                <div class="col">
                    <div class="news-card">
                        <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($n['title']) ?>" class="news-img">
                        <div class="news-body">
                            <h5 class="news-title"><?= htmlspecialchars($n['title']) ?></h5>
                            <p class="news-summary"><?= htmlspecialchars($n['summary']) ?></p>
                            <div class="news-meta">
                                <span><i class="fas fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($n['created_at'])) ?></span>
                                <div class="d-flex gap-2">
                                    <a href="edit_news.php?id=<?= $n['id'] ?>" class="btn-delete" style="color: #4facfe; background: rgba(79, 172, 254, 0.2);"><i class="fas fa-pen"></i></a>
                                    <a href="?delete_id=<?= $n['id'] ?>" class="btn-delete" onclick="return confirm('Chắc chắn muốn xóa bài viết này chứ?');"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>