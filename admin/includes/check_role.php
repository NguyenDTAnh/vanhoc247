<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra: Nếu chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'] ?? 'student';
$current_page = basename($_SERVER['PHP_SELF']);

// Block access to students entirely from admin if desired, 
// OR allow them if they have permissions mapped.
// Note: If you want any role to access admin dashboard, skip strict admin check here.

if ($user_role !== 'admin') {
    include_once __DIR__ . '/../../includes/db.php';
    
    // Ánh xạ trang hiện tại với tên module
    $page_module_map = [
        'users_list.php' => 'users',
        'roles.php' => 'users',
        'user_logs.php' => 'users',
        'courses_list.php' => 'courses',
        'course_assign.php' => 'courses',
        'manage_courses.php' => 'courses',
        'videos.php' => 'content',
        'documents.php' => 'content',
        'lives.php' => 'lives',
        'ai_config.php' => 'ai_chat',
        'manage_news.php' => 'news',
        'forum.php' => 'forum',
        'quizzes.php' => 'quizzes',
        'grading.php' => 'quizzes',
        'analytics.php' => 'system',
        'notifications.php' => 'system',
        'billing.php' => 'system',
        'settings.php' => 'system',
        'security.php' => 'system'
    ];

    // Các trang mặc định cho phép truy cập nếu đã vào được admin
    $public_admin_pages = ['index.php'];

    if (!in_array($current_page, $public_admin_pages) && isset($page_module_map[$current_page])) {
        $module = $page_module_map[$current_page];
        
        $sql_check = "SELECT is_allowed FROM role_permissions WHERE role_name = '$user_role' AND module_name = '$module'";
        $result_check = mysqli_query($conn, $sql_check);
        $is_allowed = false;
        
        if ($result_check && mysqli_num_rows($result_check) > 0) {
            $row = mysqli_fetch_assoc($result_check);
            if ($row['is_allowed'] == 1) {
                $is_allowed = true;
            }
        }
        
        if (!$is_allowed) {
            // Không có quyền truy cập, đá về dashboard
            header("Location: index.php?error=access_denied");
            exit();
        }
    } elseif (!in_array($current_page, $public_admin_pages) && !isset($page_module_map[$current_page])) {
        // Những trang không nằm trong danh sách không cần check cũng cấm nếu không phải admin
        header("Location: index.php?error=access_denied");
        exit();
    }
}
?>