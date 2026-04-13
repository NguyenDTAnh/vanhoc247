<?php
session_start();
require_once '../includes/db.php';

// --- PHẦN 1: LOGIC QUẢN TRỊ (ADMIN METRICS) ---
$global_stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(id) as total_courses,
        SUM(current_students) as total_active_students,
        SUM(price * current_students) as estimated_revenue,
        COUNT(CASE WHEN current_students >= max_students THEN 1 END) as full_classes
    FROM courses
"));

// Truy vấn dữ liệu cho các Tab
$q_upcoming = mysqli_query($conn, "SELECT * FROM courses WHERE status = 'upcoming' OR status IS NULL ORDER BY schedule_date ASC");
$q_ongoing  = mysqli_query($conn, "SELECT * FROM courses WHERE status = 'ongoing' ORDER BY id DESC");
$q_finished = mysqli_query($conn, "SELECT * FROM courses WHERE status = 'finished' ORDER BY id DESC");

function renderAdminTable($result, $type) {
    echo '<div class="table-responsive"><table class="table table-dark-muse mb-0 align-middle">';
    echo '<thead><tr>
            <th class="ps-4">THÔNG TIN LỚP HỌC</th>
            <th>LỊCH TRÌNH</th>
            <th>HIỆU SUẤT / SĨ SỐ</th>
            <th>TRẠNG THÁI</th>
            <th class="text-end pe-4">THAO TÁC</th>
          </tr></thead>';
    echo '<tbody>';
    
    if($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $max = $row['max_students'] ?: 40;
            $current = $row['current_students'] ?: 0;
            $percent = ($current / $max) * 100;
            $revenue = number_format($row['price'] * $current, 0, ',', '.');
            
            echo '<tr>';
            echo '<td class="ps-4 py-3">
                    <div class="d-flex align-items-center">
                        <div class="van-square">'.mb_substr($row['title'], 0, 1).'</div>
                        <div class="ms-3">
                            <div class="fw-bold text-white" style="font-size:14px;">'.$row['title'].'</div>
                            <div class="text-muted" style="font-size: 11px;">ID: #VAN-'.$row['id'].'</div>
                        </div>
                    </div>
                  </td>';
            echo '<td class="text-info fw-600 small">'.($row['schedule_date'] ? date('d/m | H:i', strtotime($row['schedule_date'])) : 'Chưa đặt lịch').'</td>';
            echo '<td>
                    <div class="d-flex justify-content-between mb-1" style="width: 120px;">
                        <span class="text-success fw-bold" style="font-size:11px;">'.$revenue.'đ</span>
                        <span class="text-muted" style="font-size:10px;">'.$current.'/'.$max.'</span>
                    </div>
                    <div class="progress" style="height: 3px; width: 120px; background: #1a1a24;">
                        <div class="progress-bar" style="width: '.$percent.'%; background: #a855f7 !important;"></div>
                    </div>
                  </td>';
            echo '<td><span class="badge-muse '.($type.'-tag').'">'.strtoupper($type).'</span></td>';
            echo '<td class="text-end pe-4">
                    <button class="btn-tool"><i class="fas fa-users-viewfinder"></i></button>
                    <button class="btn-tool mx-2"><i class="fas fa-edit"></i></button>
                    <button class="btn-tool text-danger"><i class="fas fa-trash-alt"></i></button>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5" class="text-center py-5 text-secondary">Hệ thống chưa ghi nhận dữ liệu.</td></tr>';
    }
    echo '</tbody></table></div>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản trị MuseOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --bg: #050508; --card: #0d0d12; --accent: #a855f7; --border: rgba(255, 255, 255, 0.05); }
        body { background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; }
        .main-content { margin-left: 280px; padding: 40px; }
        
        /* Dashboard Cards */
        .admin-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; padding: 25px; }
        .card-label { font-size: 11px; font-weight: 800; color: #475569; letter-spacing: 1px; margin-bottom: 5px; }

        /* Chart Area */
        .chart-container { background: var(--card); border: 1px solid var(--border); border-radius: 24px; padding: 25px; margin-bottom: 30px; }

        /* Fix lỗi TRẮNG Table */
        .muse-main-table { background: var(--card); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; }
        .table-dark-muse { background: transparent !important; margin-bottom: 0; }
        .table-dark-muse thead th { background: #11111a !important; border: none; color: #64748b; font-size: 11px; padding: 18px; text-transform: uppercase; }
        .table-dark-muse td { background: transparent !important; border-bottom: 1px solid var(--border); color: #cbd5e1 !important; }
        .table-dark-muse tr:last-child td { border-bottom: none; }

        /* Tabs & Buttons */
        .nav-admin-tabs { background: #11111a; padding: 6px; border-radius: 14px; display: inline-flex; gap: 5px; margin-bottom: 20px; }
        .nav-admin-tabs .nav-link { color: #64748b; font-weight: 700; border: none; padding: 8px 18px; border-radius: 10px; font-size: 13px; }
        .nav-admin-tabs .nav-link.active { background: var(--accent) !important; color: white; }
        .btn-tool { background: #16161d; border: 1px solid var(--border); color: #64748b; width: 34px; height: 34px; border-radius: 10px; }

        /* Modal Dark Mode */
        .modal-content { background: #0f0f15; border: 1px solid var(--border); border-radius: 24px; }
        .v-input { background: #16161d !important; border: 1px solid var(--border) !important; color: #fff !important; border-radius: 12px !important; }

        /* Tags */
        .van-square { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #a855f7, #6366f1); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; }
        .badge-muse { font-size: 9px; font-weight: 800; padding: 5px 12px; border-radius: 8px; }
        .upcoming-tag { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
        .ongoing-tag { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .finished-tag { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    </style>
</head>
<body>
    <?php if (file_exists('includes/sidebar.php')) include 'includes/sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-800 m-0" style="font-size: 28px;">Quản trị <span style="color:var(--accent)">Ngữ Văn</span></h1>
                <p class="text-secondary small">Theo dõi hiệu suất kinh doanh và đào tạo chuyên sâu.</p>
            </div>
            <button class="btn" style="background:var(--accent); color:white; border-radius:14px; font-weight:700; padding:12px 25px;" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="fas fa-plus-circle me-2"></i> Lên lịch học mới
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="admin-card"><div class="card-label">TỔNG CHUYÊN ĐỀ</div><div class="h2 fw-800 m-0"><?php echo $global_stats['total_courses']; ?></div></div></div>
            <div class="col-md-3"><div class="admin-card"><div class="card-label">HỌC VIÊN ACTIVE</div><div class="h2 fw-800 m-0 text-accent"><?php echo number_format($global_stats['total_active_students']); ?></div></div></div>
            <div class="col-md-3"><div class="admin-card"><div class="card-label">DOANH THU ƯỚC TÍNH</div><div class="h2 fw-800 m-0 text-success"><?php echo number_format($global_stats['estimated_revenue'], 0, ',', '.'); ?>đ</div></div></div>
            <div class="col-md-3"><div class="admin-card"><div class="card-label">LỚP ĐÃ FULL</div><div class="h2 fw-800 m-0 text-danger"><?php echo $global_stats['full_classes']; ?></div></div></div>
        </div>

        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 m-0">TĂNG TRƯỞNG NGƯỜI DÙNG MỚI</h6>
                <span class="text-secondary small">Dữ liệu 7 ngày qua</span>
            </div>
            <canvas id="growthChart" height="80"></canvas>
        </div>

        <div class="muse-main-table">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom border-secondary" style="--bs-border-opacity: 0.05">
                <nav class="nav nav-admin-tabs">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-up">Sắp khai giảng</button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-on">Đang vận hành</button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-fin">Kho lưu trữ</button>
                </nav>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-up"><?php renderAdminTable($q_upcoming, 'upcoming'); ?></div>
                <div class="tab-pane fade" id="tab-on"><?php renderAdminTable($q_ongoing, 'ongoing'); ?></div>
                <div class="tab-pane fade" id="tab-fin"><?php renderAdminTable($q_finished, 'finished'); ?></div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="process_add_course.php" method="POST">
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-800 text-white">Thiết lập chuyên đề mới</h5>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="small fw-700 text-secondary mb-2 d-block">CHỦ ĐỀ BÀI DẠY</label>
                            <input type="text" name="title" class="form-control v-input" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="small fw-700 text-secondary mb-2 d-block">NGÀY GIỜ HỌC</label>
                                <input type="datetime-local" name="schedule_date" class="form-control v-input">
                            </div>
                            <div class="col-6">
                                <label class="small fw-700 text-secondary mb-2 d-block">GIÁ KHÓA HỌC</label>
                                <input type="number" name="price" class="form-control v-input" placeholder="VNĐ">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-700 text-secondary mb-2 d-block">MÔ TẢ NGẮN</label>
                            <textarea name="description" class="form-control v-input" rows="2"></textarea>
                        </div>
                        <input type="hidden" name="category" value="Ngữ Văn">
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" name="btn_add_course" class="btn w-100 py-3" style="background:var(--accent); color:white; font-weight:800; border-radius:14px;">XÁC NHẬN CÔNG BỐ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
                datasets: [{
                    label: 'Học viên mới',
                    data: [12, 19, 3, 5, 2, 3, 15],
                    borderColor: '#a855f7',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(168, 85, 247, 0.05)',
                    pointRadius: 4,
                    pointBackgroundColor: '#fff'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { display: false } },
                    x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } }
                }
            }
        });
    </script>
</body>
</html>