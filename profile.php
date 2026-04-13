<?php 
include 'includes/db.php';
session_start();

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Lấy dữ liệu người dùng
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

// 2. XỬ LÝ CẬP NHẬT THÔNG TIN
if (isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    $avatar_sql = ""; 
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $new_filename = "avatar_" . $user_id . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], "uploads/" . $new_filename)) {
                $avatar_sql = ", avatar='$new_filename'";
                if (!empty($user['avatar']) && file_exists("uploads/" . $user['avatar'])) {
                    unlink("uploads/" . $user['avatar']);
                }
            }
        }
    }

    $sql = "UPDATE users SET username='$username', email='$email', bio='$bio', is_public='$is_public' $avatar_sql WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['username'] = $username;
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));
        $message = "<div class='alert alert-success border-0 shadow-lg mb-4 animate__animated animate__fadeIn' style='background: rgba(25, 135, 84, 0.2); color: #75b798; border-radius: 15px;'>Cập nhật hệ thống học tập thành công!</div>";
    }
}

// 3. DỮ LIỆU BÀI VIẾT
$posts_query = mysqli_query($conn, "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC");
$total_posts = mysqli_num_rows($posts_query);

$avatar_path = ($user['avatar'] && file_exists("uploads/" . $user['avatar'])) 
               ? "uploads/" . $user['avatar'] 
               : "https://ui-avatars.com/api/?name=" . urlencode($user['username']) . "&size=150&background=f5576c&color=fff";
?>

<?php include 'includes/header.php'; ?>

<style>
    /* BASE STYLE */
    body { background-color: #050505 !important; color: #fff; overflow-x: hidden; font-family: 'Inter', sans-serif; }
    
    .bg-glow {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at 10% 20%, rgba(245, 87, 108, 0.05) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(240, 147, 251, 0.05) 0%, transparent 40%);
        z-index: -1;
    }

    .profile-cover-wrapper { position: relative; height: 350px; background: url('https://images.unsplash.com/photo-1614850523296-d8c1af93d400?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover; }
    .profile-cover-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(5,5,5,0) 0%, rgba(5,5,5,1) 100%); }
    .profile-content-offset { margin-top: -100px; position: relative; z-index: 10; }

    /* COMPONENTS */
    .glass-card {
        background: rgba(20, 20, 22, 0.75) !important;
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 28px !important;
    }

    /* TABS NAVIGATION */
    .nav-tabs-muse {
        display: flex !important; gap: 12px; background: rgba(255, 255, 255, 0.03);
        padding: 8px; border-radius: 20px; width: fit-content; margin-bottom: 30px; list-style: none;
    }
    .nav-tab-item {
        padding: 10px 22px; border-radius: 15px; color: rgba(255, 255, 255, 0.5);
        cursor: pointer; font-weight: 600; transition: 0.3s; font-size: 0.85rem;
        text-decoration: none !important; display: block;
    }
    .nav-tab-item.active {
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        color: #fff !important; box-shadow: 0 8px 20px rgba(245, 87, 108, 0.3);
    }

    /* LEARNING SPECIFIC STYLE */
    .learning-item {
        background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 18px; padding: 15px; margin-bottom: 12px;
        display: flex; align-items: center; justify-content: space-between; transition: 0.3s;
    }
    .learning-item:hover { background: rgba(255, 255, 255, 0.05); border-color: #f5576c; }
    
    .status-watched { color: #00ff88; text-shadow: 0 0 10px rgba(0, 255, 136, 0.3); }

    .notify-card {
        border-left: 4px solid #f5576c; background: rgba(245, 87, 108, 0.05);
        padding: 15px; border-radius: 0 18px 18px 0; margin-bottom: 12px;
    }

    .progress-muse { height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; }
    .progress-bar-muse { background: linear-gradient(90deg, #f5576c, #f093fb); }
</style>

<div class="bg-glow"></div>
<div class="profile-cover-wrapper"><div class="profile-cover-overlay"></div></div>

<div class="container profile-content-offset">
    <div class="row g-4 px-md-4">
        
        <div class="col-lg-4 col-xl-3">
            <div class="sticky-sidebar">
                <div class="glass-card p-4 text-center shadow-lg">
                    <div class="avatar-ring mb-4" style="display:inline-block; padding:4px; background:linear-gradient(135deg, #f5576c, #f093fb); border-radius:50%;">
                        <img src="<?php echo $avatar_path; ?>" class="rounded-circle border border-4 border-dark" width="120" height="120" style="object-fit:cover;">
                    </div>
                    <h3 class="fw-900 mb-1 text-white"><?php echo $user['username']; ?></h3>
                    <p class="text-danger fw-bold small mb-3">Học sinh chuyên cần</p>

                    <div class="row g-2 mb-4 text-center">
                        <div class="col-6">
                            <div class="p-2" style="background:rgba(255,255,255,0.03); border-radius:15px;">
                                <div class="h5 fw-900 mb-0">12</div>
                                <div style="font-size: 9px; color: #666;">TÀI LIỆU</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2" style="background:rgba(255,255,255,0.03); border-radius:15px;">
                                <div class="h5 fw-900 mb-0">8.8</div>
                                <div style="font-size: 9px; color: #666;">GPA</div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-join-muse w-100 py-3 rounded-pill mb-3" data-bs-toggle="modal" data-bs-target="#editModal">
                        <i class="fas fa-user-edit me-2"></i> CHỈNH SỬA
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9 ps-lg-5">
            <?php echo $message; ?>

            <ul class="nav nav-tabs-muse border-0" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-tab-item active" id="timeline-tab" data-bs-toggle="tab" href="#timeline">Dòng thời gian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-tab-item" id="learning-tab" data-bs-toggle="tab" href="#learning">Lộ trình học</a>
                </li>
                <li class="nav-item">
                    <a class="nav-tab-item" id="notify-tab" data-bs-toggle="tab" href="#notify">Thông báo</a>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                
                <div class="tab-pane fade show active" id="timeline">
                    <h4 class="fw-bold text-white mb-4">Chia sẻ gần đây</h4>
                    <?php if ($total_posts > 0): ?>
                        <?php while($post = mysqli_fetch_assoc($posts_query)): ?>
                            <div class="card glass-card mb-4 border-0 p-2">
                                <div class="card-body">
                                    <h6 class="fw-bold text-white"><?php echo htmlspecialchars($post['title']); ?></h6>
                                    <p class="text-white-50 small mb-0"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                    <hr class="opacity-10">
                                    <span style="font-size: 10px;" class="text-white-50">Ngày đăng: <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5 glass-card opacity-50">Chưa có bài viết nào.</div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="learning">
                    
                    <div class="glass-card p-4 mb-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fas fa-laptop-code me-2 text-danger"></i>Lớp học đã đăng ký</h5>
                        <div class="learning-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 rounded-circle bg-danger bg-opacity-10"><i class="fas fa-code text-danger"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-white">Lập trình Web nâng cao với PHP</h6>
                                    <small class="text-white-50">Giảng viên: Mr. Kathv</small>
                                </div>
                            </div>
                            <div class="text-end" style="min-width: 100px;">
                                <div class="progress-muse mb-1"><div class="progress-bar-muse" style="width: 85%; height:100%;"></div></div>
                                <span style="font-size: 10px;" class="text-white-50">Hoàn thành 85%</span>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fas fa-folder-open me-2 text-info"></i>Tài liệu & Bài học</h5>
                        
                        <h6 class="text-white-50 small fw-bold mb-3">VIDEO ĐÃ XEM</h6>
                        <div class="learning-item">
                            <div class="d-flex align-items-center">
                                <i class="fab fa-youtube me-3 text-danger fs-5"></i>
                                <div>
                                    <p class="mb-0 fw-bold small text-white">Bài 05: Xử lý Session và Cookie trong PHP</p>
                                    <span style="font-size: 10px;" class="text-white-50">Thời lượng: 45:00 | Đã xem xong</span>
                                </div>
                            </div>
                            <i class="fas fa-check-circle status-watched"></i>
                        </div>

                        <h6 class="text-white-50 small fw-bold mt-4 mb-3">FILE PDF ĐÃ LƯU</h6>
                        <div class="learning-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf me-3 text-info fs-5"></i>
                                <div>
                                    <p class="mb-0 fw-bold small text-white">Tai_lieu_on_tap_IELTS.pdf</p>
                                    <span style="font-size: 10px;" class="text-white-50">Dung lượng: 1.8 MB | Ngày tải: 10/04/2026</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-sm btn-link text-white-50 p-0"><i class="fas fa-download"></i></a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="notify">
                    <div class="glass-card p-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fas fa-bell me-2 text-warning"></i>Trung tâm thông báo</h5>
                        
                        <div class="notify-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="fw-bold text-white mb-1 small">Lịch thi cuối kỳ PHP</h6>
                                <span class="badge rounded-pill bg-danger" style="font-size: 8px;">QUAN TRỌNG</span>
                            </div>
                            <p class="mb-0 text-white-50" style="font-size: 12px;">Mày lưu ý lịch thi đã được chốt vào 08:00 sáng Chủ Nhật tới. Đừng có ngủ quên đấy!</p>
                        </div>

                        <div class="notify-card border-info" style="background:rgba(0,255,255,0.05);">
                            <h6 class="fw-bold text-white mb-1 small">Tài liệu mới được đăng tải</h6>
                            <p class="mb-0 text-white-50" style="font-size: 12px;">"Slide bài giảng 08: Security in Web Design" đã có sẵn trong tab Lộ trình.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#0f0f11; border-radius:25px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="fw-bold text-white mb-0">Cập nhật tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-white-50">Tên hiển thị</label>
                        <input type="text" name="username" class="form-control" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff;" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-white-50">Email</label>
                        <input type="email" name="email" class="form-control" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff;" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-white-50">Tiểu sử</label>
                        <textarea name="bio" class="form-control" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff;"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-white-50">Ảnh đại diện mới</label>
                        <input type="file" name="avatar" class="form-control" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="update_profile" class="btn btn-join-muse w-100 py-3 rounded-4 fw-bold shadow-lg">LƯU CÀI ĐẶT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>