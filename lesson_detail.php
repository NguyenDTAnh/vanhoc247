<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/db.php';

$lesson_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

if ($lesson_id === 0) {
    die("Bài học không tồn tại!");
}

// Lấy thông tin lesson
$sql = "SELECT * FROM courses WHERE id = $lesson_id AND format = 'Lesson'";
$res = mysqli_query($conn, $sql);
$lesson = mysqli_fetch_assoc($res);

if (!$lesson) {
    die("Không tìm thấy dữ liệu bài học này.");
}

// Lấy video và pdf
$sql_content = "SELECT * FROM course_contents WHERE course_id = $lesson_id";
$res_content = mysqli_query($conn, $sql_content);
$video_path = '';
$pdf_path = '';
while($row = mysqli_fetch_assoc($res_content)) {
    if ($row['file_type'] == 'video' && empty($video_path)) {
        $video_path = $row['file_path'];
    }
    if ($row['file_type'] == 'pdf' && empty($pdf_path)) {
        $pdf_path = $row['file_path'];
    }
}

// Lấy Ghi chú
$user_note_content = '';
if ($user_id > 0) {
    $sql_note = "SELECT note_content FROM lesson_notes WHERE user_id = $user_id AND course_id = $lesson_id";
    $res_note = mysqli_query($conn, $sql_note);
    if ($row_n = mysqli_fetch_assoc($res_note)) {
        $user_note_content = $row_n['note_content'];
    }
}

// Xử lý Gửi ghi chú (AJAX) - Trả JSON
if (isset($_POST['save_note']) && $user_id > 0) {
    $note = mysqli_real_escape_string($conn, $_POST['note_content']);
    if ($user_note_content !== '') {
        mysqli_query($conn, "UPDATE lesson_notes SET note_content = '$note' WHERE user_id = $user_id AND course_id = $lesson_id");
    } else {
        mysqli_query($conn, "INSERT INTO lesson_notes (user_id, course_id, note_content) VALUES ($user_id, $lesson_id, '$note')");
    }
    echo json_encode(['status' => 'success', 'message' => 'Lưu thành công']);
    exit();
}

// Xử lý comment
if (isset($_POST['submit_comment']) && $user_id > 0) {
    $content = mysqli_real_escape_string($conn, $_POST['comment_content']);
    $rating = intval($_POST['rating']);
    if ($rating < 1) $rating = 5;
    if ($rating > 5) $rating = 5;
    mysqli_query($conn, "INSERT INTO lesson_comments (user_id, course_id, rating, content) VALUES ($user_id, $lesson_id, $rating, '$content')");
    header("Location: lesson_detail.php?id=$lesson_id");
    exit();
}

// Tăng views
if (!isset($_SESSION["viewed_lesson_$lesson_id"])) {
    mysqli_query($conn, "UPDATE courses SET views = views + 1 WHERE id = $lesson_id");
    $_SESSION["viewed_lesson_$lesson_id"] = true;
    $lesson['views']++;
}

// Lấy danh sách Comments
$sql_cmts = "SELECT c.*, u.username, u.avatar FROM lesson_comments c 
             INNER JOIN users u ON c.user_id = u.id 
             WHERE c.course_id = $lesson_id ORDER BY c.id DESC";
$res_cmts = mysqli_query($conn, $sql_cmts);

?>
<?php include 'includes/header.php'; ?>
<style>
    body { background-color: #0f1015; color: #fff; }
    
    .video-container { background: #000; border-radius: 12px; overflow: hidden; height: 500px; display: flex; align-items: center; justify-content: center;}
    video { width: 100%; height: 100%; object-fit: contain;}
    
    .lesson-info { padding: 25px 0; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 25px; }
    .lesson-title { font-size: 2rem; font-weight: 800; margin-bottom: 10px; color: #fff; }
    
    .meta-badges { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;}
    .badge-muse-detail { padding: 8px 12px; font-weight: 600; border-radius: 6px; font-size: 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #ddd; }
    .badge-muse-detail i { color: #3d8bff; margin-right: 5px; }
    
    .desc-box { background: rgba(255,255,255,0.02); padding: 20px; border-radius: 12px; font-size: 0.95rem; line-height: 1.6; color: #bbb; border: 1px solid rgba(255,255,255,0.05);}
    
    .action-card { background: linear-gradient(145deg, #181920, #13141a); border-radius: 15px; padding: 20px; border: 1px solid rgba(255,255,255,0.05); height: 100%; }
    .action-title { font-size: 1.1rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 15px; color: #e5e5e5; }
    
    /* Notebook */
    #notebookArea { width: 100%; background: #fffcf2; color: #333; border: none; border-radius: 8px; padding: 15px; min-height: 250px; font-family: monospace; resize: vertical; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); background-image: repeating-linear-gradient(transparent, transparent 27px, #e1d8c1 28px); line-height: 28px; }
    #notebookArea:focus { outline: none; }
    .note-btn { background: #3d8bff; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; transition: 0.3s;}
    .note-btn:hover { background: #2b6ed9; }
    .btn-word { background: #2b579a; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: 600; font-size: 0.9rem;}
    
    /* Comments */
    .comment-item { display: flex; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .comment-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
    .stars { color: #facc15; font-size: 0.8rem; margin-bottom: 5px; }
    .comment-content { font-size: 0.9rem; color: #ccc; }
    
    /* Links */
    .resource-btn { display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 12px 15px; border-radius: 10px; text-decoration: none; color: #ddd; transition: 0.3s; margin-bottom: 10px; }
    .resource-btn:hover { background: rgba(255,255,255,0.08); color: #fff; transform: translateY(-2px); }
    .btn-quiz { background: rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.3); color: #a78bfa; }
    .btn-quiz:hover { background: rgba(139, 92, 246, 0.2); border-color: rgba(139, 92, 246, 0.5); color: #c4b5fd; }
</style>

<div class="container py-4">
    <div class="row">
        <!-- Main Column: Video & Info -->
        <div class="col-lg-8">
            <div class="video-container shadow-lg">
                <?php if ($video_path): ?>
                    <video controls controlsList="nodownload">
                        <source src="<?= htmlspecialchars($video_path) ?>" type="video/mp4">
                        Trình duyệt không hỗ trợ xem video.
                    </video>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-video-slash fa-3x mb-3"></i>
                        <p>Bài học này không có video bài giảng đính kèm.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="lesson-info">
                <h1 class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></h1>
                
                <div class="meta-badges">
                    <div class="badge-muse-detail"><i class="fas fa-user-edit"></i> <?= htmlspecialchars($lesson['author'] ?? 'Admin') ?></div>
                    <div class="badge-muse-detail"><i class="fas fa-eye"></i> <?= $lesson['views'] ?> views</div>
                    <div class="badge-muse-detail"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($lesson['created_at'])) ?></div>
                    <div class="badge-muse-detail"><i class="fas fa-tags"></i> <?= htmlspecialchars($lesson['category']) ?></div>
                </div>
                
                <div class="desc-box">
                    <strong>Nội dung tóm tắt:</strong><br>
                    <?= nl2br(htmlspecialchars($lesson['description'])) ?>
                </div>
            </div>
            
            <!-- Comment Section -->
            <div class="action-card mt-4 mb-4">
                <h3 class="action-title"><i class="fas fa-comments me-2"></i> Trải nghiệm & Thảo luận</h3>
                <?php if ($user_id): ?>
                    <form method="POST" class="mb-4">
                        <div class="mb-2">
                            <select name="rating" class="form-select form-select-sm w-auto bg-dark border-secondary text-white d-inline-block">
                                <option value="5">⭐⭐⭐⭐⭐ Mê tít (5 sao)</option>
                                <option value="4">⭐⭐⭐⭐ Quá Ổn (4 sao)</option>
                                <option value="3">⭐⭐⭐ Bình thường (3 sao)</option>
                            </select>
                        </div>
                        <textarea name="comment_content" class="form-control bg-dark border-secondary text-white mb-2" rows="3" placeholder="Chia sẻ góc nhìn hoặc thắc mắc của bạn về bài học này..." required></textarea>
                        <button type="submit" name="submit_comment" class="btn note-btn"><i class="fas fa-paper-plane me-1"></i> Gửi Bình Luận</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted small">Vui lòng <a href="login.php" class="text-primary">Đăng nhập</a> để tham gia bình luận.</p>
                <?php endif; ?>
                
                <div class="comments-list mt-4">
                    <?php if (mysqli_num_rows($res_cmts) > 0): ?>
                        <?php while($cmt = mysqli_fetch_assoc($res_cmts)): ?>
                            <div class="comment-item">
                                <img src="<?= !empty($cmt['avatar']) ? $cmt['avatar'] : 'https://ui-avatars.com/api/?name='.urlencode($cmt['username']) ?>" class="comment-avatar">
                                <div>
                                    <div class="fw-bold mb-1"><?= htmlspecialchars($cmt['username']) ?> <span class="text-muted small ms-2"><?= date('d/m/Y H:i', strtotime($cmt['created_at'])) ?></span></div>
                                    <div class="stars"><?= str_repeat('⭐', $cmt['rating']) ?></div>
                                    <div class="comment-content"><?= nl2br(htmlspecialchars($cmt['content'])) ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">Chưa có đánh giá nào.</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        
        <!-- Sidebar Column: Tài liệu + Trắc nghiệm + Notebook -->
        <div class="col-lg-4">
            
            <!-- Tài liệu & Trắc nghiệm -->
            <div class="action-card mb-4" style="height: auto;">
                <h3 class="action-title"><i class="fas fa-tools me-2"></i> Học Liệu Đính Kèm</h3>
                <?php if ($pdf_path): ?>
                    <a href="<?= htmlspecialchars($pdf_path) ?>" target="_blank" class="resource-btn">
                        <div><i class="fas fa-file-pdf text-danger me-2"></i> Tài liệu hướng dẫn bản đẹp (PDF)</div>
                        <i class="fas fa-download"></i>
                    </a>
                <?php else: ?>
                    <div class="text-muted small mb-2"><i class="fas fa-ban me-1"></i> Không có file đính kèm</div>
                <?php endif; ?>

                <?php if (!empty($lesson['quiz_link'])): ?>
                    <a href="<?= htmlspecialchars($lesson['quiz_link']) ?>" target="_blank" class="resource-btn btn-quiz">
                        <div><i class="fas fa-gamepad me-2"></i> Làm bài Trắc Nghiệm / Luyện tập</div>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Tập Take-notes -->
            <div class="action-card" style="height: auto;">
                <h3 class="action-title d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-book-open me-2"></i> Sổ tay điện tử</span>
                    <i class="fas fa-pen-alt text-secondary"></i>
                </h3>
                
                <?php if ($user_id): ?>
                    <textarea id="notebookArea" placeholder="Đang xem video, có ý hay? Ghi chép ngay vào đây. Dữ liệu sẽ được lưu vào tài khoản của bạn..."><?= htmlspecialchars($user_note_content) ?></textarea>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn-word text-white" onclick="exportToWord()"><i class="fas fa-file-word me-1"></i> Tải .DOCX</button>
                        <button class="note-btn" onclick="saveNote()" id="saveBtn"><i class="fas fa-save me-1"></i> Lưu Sổ Tay</button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 bg-dark rounded border border-secondary">
                        <i class="fas fa-lock text-muted fa-2x mb-2"></i>
                        <p class="mb-0 small text-muted">Đăng nhập để dùng tính năng Take-note.</p>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<script>
    // AJAX Lưu ghi chú
    function saveNote() {
        const note = document.getElementById('notebookArea').value;
        const btn = document.getElementById('saveBtn');
        const oldHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('save_note', '1');
        formData.append('note_content', note);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            btn.innerHTML = '<i class="fas fa-check"></i> Đã Lưu';
            // btn.classList.replace('note-btn', 'btn-success');
            setTimeout(() => {
                btn.innerHTML = oldHtml;
                btn.disabled = false;
            }, 2000);
        }).catch(err => {
            console.error(err);
            btn.innerHTML = '<i class="fas fa-times"></i> Lỗi';
            btn.disabled = false;
        });
    }

    // Export Sổ tay ra Word (Phương pháp base64 MIME)
    function exportToWord() {
        var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Ghi chép Bài Học</title></head><body>";
        var footer = "</body></html>";
        
        var content = document.getElementById('notebookArea').value;
        // Xử lý xuống dòng cho Word
        var htmlContent = content.replace(/\n/g, "<br>");
        
        var sourceHTML = header + "<h2>Ghi Chép: <?= addslashes($lesson['title']) ?></h2><div style='font-family: Arial; font-size: 14px;'>" + htmlContent + "</div>" + footer;
        var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
        
        var fileDownload = document.createElement("a");
        document.body.appendChild(fileDownload);
        fileDownload.href = source;
        fileDownload.download = 'Ban-ghi-chep-<?= $lesson_id ?>.doc';
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }
</script>
<?php include 'includes/footer.php'; ?>
