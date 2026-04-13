<?php 
include 'includes/check_role.php';
require_once '../includes/db.php';

// TẠO BẢNG TỰ ĐỘNG NẾU CHƯA CÓ (Tiện cho Tình yêu test luôn không cần chạy tay)
$table_check = "CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(100) NOT NULL,
    avatar_color VARCHAR(10) DEFAULT 'random',
    content TEXT NOT NULL,
    likes INT DEFAULT 0,
    comments INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $table_check);

// XỬ LÝ XÓA BÀI VIẾT
if(isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM forum_posts WHERE id = $id");
    header("Location: manage_forum.php"); 
    exit();
}

// LẤY DANH SÁCH BÀI VIẾT
$query = "SELECT * FROM forum_posts ORDER BY created_at DESC";
$res_forum = mysqli_query($conn, $query);

$posts = [];
if($res_forum) {
    while($row = mysqli_fetch_assoc($res_forum)) {
        $posts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Forum | Vanhoc247 Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --netflix-black: #141414;
            --netflix-red: #E50914;
            --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            --card-bg: #222;
            --border: rgba(255, 255, 255, 0.1);
        }
        
        body { background-color: var(--netflix-black); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; min-height: 100vh; padding-top: 80px; padding-bottom: 50px; }
        
        /* Header Bar */
        .admin-header {
            position: absolute; top: 0; left: 0; right: 0;
            padding: 20px 4%; z-index: 100;
            display: flex; justify-content: space-between; align-items: center;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
        }

        .btn-upload { background: var(--muse-gradient); border: none; font-weight: 700; color: white;}
        .btn-upload:hover { transform: scale(1.05); transition: 0.2s;}

        .forum-grid { padding: 0 4%; margin-top: 30px;}
        .section-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; color: #e5e5e5; display: flex; align-items: center; }

        .post-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border);
            transition: 0.3s;
            height: 100%;
            display: flex; flex-direction: column;
        }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.6); border-color: rgba(255,255,255,0.3); }
        
        .author-box { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 15px;}
        .author-name { font-weight: 700; font-size: 1rem; margin: 0; }
        .post-date { font-size: 0.75rem; color: #aaa; }
        
        .post-content { font-size: 0.95rem; color: #eee; margin-bottom: 20px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
        
        .post-meta { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 15px;}
        .stats i { color: #888; margin-right: 5px; }
        
        .btn-delete { background: rgba(229, 9, 20, 0.2); color: #E50914; border-radius: 5px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s;}
        .btn-delete:hover { background: var(--netflix-red); color: white; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="fw-bold m-0"><span style="color: #f093fb;"><i class="fas fa-users"></i></span> Muse Community Admin</h4>
        </div>
        <!-- Giả sử có trang thêm post do Admin seeding -->
        <a href="#" class="btn btn-upload rounded-pill px-4 text-decoration-none" onclick="alert('Chức năng Seeding bài viết sẽ làm sau nhé!')">
            <i class="fas fa-plus me-2"></i> Tạo bài đăng
        </a>
    </div>

    <div class="forum-grid">
        <h3 class="section-title"><i class="fas fa-comments text-info me-2"></i> Quản lý Bài Viết (Threads)</h3>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
            <?php if(empty($posts)): ?>
                <div class="col-12 text-center text-secondary py-5">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                    <h5>Forum chưa có bài viết nào... Tịch mịch quá!</h5>
                    <p>Hãy chờ user vào đăng bài, hoặc tự seed vài bài =))</p>
                </div>
            <?php endif; ?>

            <?php foreach($posts as $p): ?>
                <div class="col">
                    <div class="post-card">
                        <div class="author-box">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['author_name']) ?>&background=<?= $p['avatar_color'] ?>&color=fff" class="rounded-circle" width="40" height="40">
                            <div>
                                <h6 class="author-name"><?= htmlspecialchars($p['author_name']) ?> <i class="fas fa-check-circle text-primary" style="font-size: 0.7rem;"></i></h6>
                                <span class="post-date"><?= date('H:i - d/m/Y', strtotime($p['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($p['content'])) ?>
                        </div>
                        
                        <div class="post-meta">
                            <div class="stats d-flex gap-3">
                                <span class="small"><i class="fas fa-heart"></i> <?= $p['likes'] ?></span>
                                <span class="small"><i class="fas fa-comment"></i> <?= $p['comments'] ?></span>
                            </div>
                            <div class="actions">
                                <a href="?delete_id=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Chắc chắn muốn xóa THREAD này của User chứ? Hành động không thể hoàn tác!');" title="Xóa bài viết">
                                    <i class="fas fa-trash"></i>
                                </a>
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
