<?php
session_start();
require_once '../includes/db.php';

// 1. THỐNG KÊ NHANH
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users"))['t'];
$count_admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role = 'admin'"))['t'];
$count_teachers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role = 'teacher'"))['t'];
$count_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role = 'student'"))['t'];

// 2. LẤY DỮ LIỆU TỪNG NHÓM
$admins = mysqli_query($conn, "SELECT * FROM users WHERE role = 'admin' ORDER BY id DESC");
$teachers = mysqli_query($conn, "SELECT * FROM users WHERE role = 'teacher' ORDER BY id DESC");
$students = mysqli_query($conn, "SELECT * FROM users WHERE role = 'student' ORDER BY id DESC");

// 3. DỮ LIỆU BIỂU ĐỒ
$chart_labels = "['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN']";
$chart_data = "[12, 19, 3, 5, 2, 3, 15]"; 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống nhân sự | MuseOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --bg: #050508; --accent: #3d8bff; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.08); }
        body { background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; padding: 40px; }

        .btn-muse-add { 
            background: linear-gradient(135deg, #3d8bff 0%, #a855f7 100%);
            border: none; border-radius: 14px; padding: 10px 25px;
            font-weight: 700; color: white; transition: 0.3s;
            box-shadow: 0 4px 15px rgba(61, 139, 255, 0.3);
        }
        .btn-muse-add:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(168, 85, 247, 0.4); color: #fff; }

        .stat-card { background: var(--glass); border: 1px solid var(--border); border-radius: 24px; padding: 25px; transition: 0.3s; position: relative; overflow: hidden; height: 100%; }
        .stat-card:hover { border-color: var(--accent); transform: translateY(-5px); }
        .stat-val { font-size: 32px; font-weight: 800; margin: 10px 0 5px; }
        .stat-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }

        .muse-card { background: var(--glass); backdrop-filter: blur(30px); border: 1px solid var(--border); border-radius: 28px; padding: 30px; margin-top: 30px; }
        .table { color: #cbd5e1 !important; vertical-align: middle; background: transparent !important; }
        .table thead th { background: transparent !important; border: none; color: #475569; font-size: 11px; text-transform: uppercase; padding: 20px; }
        .table tr { border-bottom: 1px solid var(--border) !important; background: transparent !important; }
        .table td { background: transparent !important; border: none; padding: 20px; }
        
        .user-avatar { width: 45px; height: 45px; border-radius: 14px; background: linear-gradient(135deg, #3d8bff, #a855f7); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; }
        .role-tag { font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 8px; border: 1px solid transparent; }
        .tag-admin { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
        .tag-teacher { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
        .tag-student { background: rgba(61, 139, 255, 0.1); color: #3d8bff; border-color: rgba(61, 139, 255, 0.2); }

        .btn-action { width: 38px; height: 38px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); color: #94a3b8; transition: 0.3s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; }
        .btn-action:hover { background: var(--accent); color: #fff; }
        .btn-action.text-danger:hover { background: #dc3545; color: #fff; }

        .nav-pills .nav-link { color: #64748b; font-weight: 700; border-radius: 12px; padding: 10px 25px; margin-right: 10px; border: 1px solid transparent; }
        .nav-pills .nav-link.active { background: var(--accent) !important; color: #fff !important; box-shadow: 0 8px 20px rgba(61, 139, 255, 0.3); }
        .nav-pills { background: var(--glass); padding: 8px; border-radius: 18px; border: 1px solid var(--border); display: inline-flex; }

        .muse-input { background: #16161d !important; border: 1px solid var(--border) !important; color: #fff !important; border-radius: 12px !important; padding: 12px !important; }
        .muse-input:focus { border-color: var(--accent) !important; box-shadow: none !important; }
        .input-label { font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.2); color: #10b981; border-radius: 18px;">
                    <i class="fas fa-check-circle me-2"></i> <strong>Thành công!</strong> Đã thêm thành viên mới.
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 mb-4" role="alert" style="background: rgba(220, 38, 38, 0.2); color: #ff4d4d; border-radius: 18px;">
                    <i class="fas fa-trash-alt me-2"></i> <strong>Đã xóa!</strong> Thành viên đã bị gỡ.
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['msg'] == 'edited'): ?>
                <div class="alert alert-info alert-dismissible fade show border-0 mb-4" role="alert" style="background: rgba(61, 139, 255, 0.2); color: #3d8bff; border-radius: 18px;">
                    <i class="fas fa-user-edit me-2"></i> <strong>Cập nhật!</strong> Thông tin đã được lưu lại.
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-800 m-0" style="font-size: 32px;">Nhân sự <span style="color:var(--accent)">MuseOS</span></h1>
                <p class="text-secondary mt-1">Quản lý và theo dõi hiệu suất nhân sự.</p>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-outline-secondary px-4" style="border-radius:14px; border: 1px solid var(--border); color: #fff;"><i class="fas fa-download me-2"></i>Báo cáo</button>
                <button class="btn btn-muse-add" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i> Thêm thành viên
                </button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-8">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="stat-label">Tăng trưởng người dùng mới</div>
                        <i class="fas fa-chart-line text-accent"></i>
                    </div>
                    <canvas id="userGrowthChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row g-3">
                    <div class="col-6"><div class="stat-card text-center"><div class="stat-label">Tổng</div><div class="stat-val"><?php echo $total_users; ?></div></div></div>
                    <div class="col-6"><div class="stat-card text-center"><div class="stat-label">Admin</div><div class="stat-val text-warning"><?php echo $count_admins; ?></div></div></div>
                    <div class="col-6"><div class="stat-card text-center"><div class="stat-label">GV</div><div class="stat-val text-success"><?php echo $count_teachers; ?></div></div></div>
                    <div class="col-6"><div class="stat-card text-center"><div class="stat-label">HS</div><div class="stat-val text-primary"><?php echo $count_students; ?></div></div></div>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-admin">ADMINS</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-teacher">GIÁO VIÊN</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-student">HỌC SINH</button></li>
        </ul>

        <div class="tab-content mt-4">
            <div class="tab-pane fade show active" id="tab-admin"><?php renderMuseTable($admins, 'admin'); ?></div>
            <div class="tab-pane fade" id="tab-teacher"><?php renderMuseTable($teachers, 'teacher'); ?></div>
            <div class="tab-pane fade" id="tab-student"><?php renderMuseTable($students, 'student'); ?></div>
        </div>
    </main>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #0f0f15; border: 1px solid var(--border); border-radius: 24px; color: #fff;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-800">Thêm thành viên mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_add_user.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="input-label">TÊN ĐĂNG NHẬP</label>
                            <input type="text" name="username" class="form-control muse-input" required placeholder="Ví dụ: muse_admin">
                        </div>
                        <div class="mb-3">
                            <label class="input-label">EMAIL</label>
                            <input type="email" name="email" class="form-control muse-input" required placeholder="admin@museos.vn">
                        </div>
                        <div class="mb-3">
                            <label class="input-label">MẬT KHẨU</label>
                            <input type="password" name="password" class="form-control muse-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="input-label">VAI TRÒ HỆ THỐNG</label>
                            <select name="role" class="form-select muse-input">
                                <option value="student">Học sinh</option>
                                <option value="teacher">Giáo viên</option>
                                <option value="admin">Quản trị viên</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none fw-700" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" name="btn_add" class="btn btn-muse-add">Xác nhận tạo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #0f0f15; border: 1px solid var(--border); border-radius: 24px; color: #fff;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-800 text-accent">Chỉnh sửa nhân sự</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_edit_user.php" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="mb-3">
                            <label class="input-label">TÊN ĐĂNG NHẬP</label>
                            <input type="text" name="username" id="edit_username" class="form-control muse-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="input-label">EMAIL</label>
                            <input type="email" name="email" id="edit_email" class="form-control muse-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="input-label">MẬT KHẨU MỚI (để trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control muse-input" placeholder="••••••••">
                        </div>
                        <div class="mb-3">
                            <label class="input-label">VAI TRÒ HỆ THỐNG</label>
                            <select name="role" id="edit_role" class="form-select muse-input">
                                <option value="student">Học sinh</option>
                                <option value="teacher">Giáo viên</option>
                                <option value="admin">Quản trị viên</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none fw-700" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="btn_edit" class="btn btn-muse-add shadow-sm">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    function renderMuseTable($result, $role) {
        $tagClass = ($role == 'admin') ? 'tag-admin' : (($role == 'teacher') ? 'tag-teacher' : 'tag-student');
        echo '<div class="muse-card p-0 overflow-hidden"><div class="table-responsive"><table class="table mb-0">';
        echo '<thead><tr><th>Thành viên</th><th>Vai trò</th><th>Ngày đăng ký</th><th class="text-end">Thao tác</th></tr></thead>';
        echo '<tbody>';
        if(mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td><div class="d-flex align-items-center gap-3"><div class="user-avatar">'.strtoupper(substr($row['username'], 0, 1)).'</div>';
                echo '<div><div class="fw-700 text-white">'.$row['username'].'</div><div class="small text-secondary">'.$row['email'].'</div></div></div></td>';
                echo '<td><span class="role-tag '.$tagClass.'">'.$row['role'].'</span></td>';
                echo '<td class="text-secondary small">'.date('d M, Y', strtotime($row['created_at'])).'</td>';
                echo '<td class="text-end">';
                // NÚT SỬA ĐÃ CẬP NHẬT AJAX
                echo '<button class="btn-action me-2 btn-edit-user" data-id="'.$row['id'].'" data-bs-toggle="modal" data-bs-target="#editUserModal"><i class="fas fa-edit"></i></button>';
                echo '<button class="btn-action me-2"><i class="fas fa-lock"></i></button>';
                echo '<a href="delete_user.php?id='.$row['id'].'" class="btn-action text-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa thành viên này không?\');"><i class="fas fa-trash-alt"></i></a>';
                echo '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="text-center py-5 text-secondary">Không có dữ liệu trong mục này.</td></tr>';
        }
        echo '</tbody></table></div></div>';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS BIỂU ĐỒ (Giữ nguyên)
        const ctx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $chart_labels; ?>,
                datasets: [{
                    label: 'Thành viên mới',
                    data: <?php echo $chart_data; ?>,
                    borderColor: '#3d8bff',
                    backgroundColor: 'rgba(61, 139, 255, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3d8bff'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                }
            }
        });

        // JS AJAX LẤY DỮ LIỆU SỬA (MỚI THÊM)
        document.querySelectorAll('.btn-edit-user').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                fetch('get_user.php?id=' + userId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('edit_user_id').value = data.data.id;
                            document.getElementById('edit_username').value = data.data.username;
                            document.getElementById('edit_email').value = data.data.email;
                            document.getElementById('edit_role').value = data.data.role;
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(err => console.error('Lỗi Ajax:', err));
            });
        });
    </script>
</body>
</html>