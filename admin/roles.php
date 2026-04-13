<?php
session_start();
include '../includes/db.php';
include 'includes/check_role.php'; 

$modules = [
    'users' => 'Quản lý người dùng',
    'courses' => 'Quản lý khóa học',
    'content' => 'Nội dung bài giảng',
    'lives' => 'Livestream',
    'ai_chat' => 'Trợ lý học tập AI',
    'news' => 'Quản lý tin tức',
    'forum' => 'Diễn đàn',
    'quizzes' => 'Bài tập & Kiểm tra',
    'system' => 'Hệ thống (Analytics, Cài đặt)'
];

$roles = ['student', 'teacher'];

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_permissions'])) {
    // Reset all
    mysqli_query($conn, "DELETE FROM role_permissions");
    
    if (isset($_POST['perms']) && is_array($_POST['perms'])) {
        foreach ($_POST['perms'] as $role => $mods) {
            foreach ($mods as $mod => $val) {
                if ($val == '1') {
                    $role_esc = mysqli_real_escape_string($conn, $role);
                    $mod_esc = mysqli_real_escape_string($conn, $mod);
                    mysqli_query($conn, "INSERT INTO role_permissions (role_name, module_name, is_allowed) VALUES ('$role_esc', '$mod_esc', 1)");
                }
            }
        }
    }
    $msg = 'Cập nhật phân quyền thành công!';
}

// Fetch current
$current_perms = [];
$res = mysqli_query($conn, "SELECT * FROM role_permissions");

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $current_perms[$row['role_name']][$row['module_name']] = $row['is_allowed'];
    }
} else {
    // Cứu cánh: Tự tạo bảng nếu thằng XAMPP bên Windows chưa có
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS role_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(50) NOT NULL,
        module_name VARCHAR(50) NOT NULL,
        is_allowed TINYINT(1) DEFAULT 0,
        UNIQUE KEY role_module (role_name, module_name)
    )");
}

function hasPerm($role, $module, $current_perms) {
    return isset($current_perms[$role][$module]) && $current_perms[$role][$module] == 1;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân quyền Hệ thống | MuseOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #050508; --accent: #3d8bff; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.08); }
        body { background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; padding: 40px; }

        .btn-muse-primary { 
            background: linear-gradient(135deg, #3d8bff 0%, #a855f7 100%);
            border: none; border-radius: 14px; padding: 12px 30px;
            font-weight: 700; color: white; transition: 0.3s;
            box-shadow: 0 4px 15px rgba(61, 139, 255, 0.3);
        }
        .btn-muse-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(168, 85, 247, 0.4); color: #fff; }

        .muse-card { background: var(--glass); backdrop-filter: blur(30px); border: 1px solid var(--border); border-radius: 28px; padding: 30px; margin-top: 30px; }
        .table { color: #cbd5e1 !important; vertical-align: middle; background: transparent !important; }
        .table thead th { background: transparent !important; border: none; color: #475569; font-size: 12px; text-transform: uppercase; padding: 20px; }
        .table tr { border-bottom: 1px solid var(--border) !important; background: transparent !important; }
        .table td { background: transparent !important; border: none; padding: 20px; font-weight: 600; }
        
        .form-switch .form-check-input {
            width: 3em; height: 1.5em; background-color: rgba(255,255,255,0.1); border: none; cursor: pointer;
        }
        .form-switch .form-check-input:checked {
            background-color: var(--accent); box-shadow: 0 0 15px rgba(61, 139, 255, 0.5);
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php if($msg): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.2); color: #10b981; border-radius: 18px;">
                <i class="fas fa-check-circle me-2"></i> <strong>Thành công!</strong> <?php echo $msg; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-800 m-0" style="font-size: 32px;">Phân quyền & <span style="color:var(--accent)">Roles</span></h1>
                <p class="text-secondary mt-1">Thiết lập quyền truy cập cho giáo viên và học sinh.</p>
            </div>
        </div>

        <div class="muse-card p-0 overflow-hidden">
            <form action="" method="POST">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Module Hệ thống</th>
                                <th class="text-center">Giáo viên (Teacher)</th>
                                <th class="text-center">Học trò (Student)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $mod_key => $mod_name): ?>
                            <tr>
                                <td style="color: #fff;">
                                    <i class="fas fa-cube text-secondary me-2"></i> 
                                    <?php echo $mod_name; ?> <br>
                                    <small class="text-secondary"><?php echo $mod_key; ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type="hidden" name="perms[teacher][<?php echo $mod_key; ?>]" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="perms[teacher][<?php echo $mod_key; ?>]" value="1" <?php echo hasPerm('teacher', $mod_key, $current_perms) ? 'checked' : ''; ?>>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type="hidden" name="perms[student][<?php echo $mod_key; ?>]" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="perms[student][<?php echo $mod_key; ?>]" value="1" <?php echo hasPerm('student', $mod_key, $current_perms) ? 'checked' : ''; ?>>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top" style="border-color: var(--border) !important; text-align: right;">
                    <button type="submit" name="update_permissions" class="btn btn-muse-primary">
                        <i class="fas fa-save me-2"></i> Lưu cấu hình Phân Quyền
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
