<?php 
require_once 'config/db.php';
$pdo = Database::getConnection();

// Get stats
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$stats['products'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$stats['orders'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT SUM(final_amount) as revenue FROM orders WHERE status = 'completed'");
$stats['revenue'] = $stmt->fetch()['revenue'] ?? 0;
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM contacts WHERE status = 'unread'");
$stats['unread_contacts'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM articles WHERE published_at IS NOT NULL");
$stats['posts'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM reviews");
$stats['reviews'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();

include 'views/layouts/admin_header.php'; 
?>

<div class="main-content-inner">
<!-- Sales Report Area Start -->
<div class="sales-report-area mt-5 mb-5">
    <div class="row">
        <!-- Total Members -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-light rounded p-2 text-primary">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <span class="text-success fw-bold">+12.5%</span>
                    </div>
                    <div class="text-muted small">Tổng thành viên</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['users']) ?></h3>
                </div>
            </div>
        </div>
        <!-- New Orders -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-danger text-white rounded p-2">
                            <i class="bi bi-cart fs-4"></i>
                        </div>
                        <span class="text-danger fw-bold">Khẩn cấp</span>
                    </div>
                    <div class="text-muted small">Đơn hàng mới</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['orders']) ?></h3>
                </div>
            </div>
        </div>
        <!-- Pending Reviews -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-info text-white rounded p-2">
                            <i class="bi bi-chat-dots fs-4"></i>
                        </div>
                        <span class="text-dark fw-bold"><?= number_format($stats['unread_contacts']) ?> Liên hệ</span>
                    </div>
                    <div class="text-muted small">Đánh giá chờ duyệt</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['reviews']) ?></h3>
                </div>
            </div>
        </div>
        <!-- Revenue -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-success text-white rounded p-2">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <span class="text-primary fw-bold">Ổn định</span>
                    </div>
                    <div class="text-muted small">Doanh thu (hoàn tất)</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['revenue']) ?>₫</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Sales Report Area End -->

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Weekly Traffic Analysis -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Phân tích truy cập tuần</h4>
                    <div>
                        <span class="badge bg-light text-dark px-3 py-2 border">7 Ngày</span>
                        <span class="px-2 font-weight-bold">30 Ngày</span>
                    </div>
                </div>
                <!-- Placeholder for the traffic analysis image/chart as requested -->
                <div class="border rounded text-center bg-light" style="height: 300px; display: flex; flex-direction: column; justify-content: flex-end; padding: 20px; background-image: url('assets/img/hero-bg.png'); background-size: cover; background-position: center; position: relative;">
                     <div style="position: absolute; bottom: 10px; left: 10px; background: rgba(255,255,255,0.8); padding: 5px 10px; border-radius: 5px; font-weight: 500;">
                         <i class="bi bi-graph-up-arrow text-success"></i> Lưu lượng truy cập tăng 22% so với tuần trước.
                     </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Hoạt động gần đây</h4>
                    <a href="#" class="text-danger fw-bold small">Xem tất cả</a>
                </div>
                <div class="recent-activity">
                    <div class="timeline-task pb-3 border-bottom mb-3" style="border-left: 3px solid #e11b22; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1 fw-bold small">Cập nhật kho hàng</h5>
                            <span class="time text-muted small"><i class="bi bi-clock"></i> 14 phút trước</span>
                        </div>
                        <p class="text-muted small mb-0">MacBook Pro M3 đã nhập kho (+50 đơn vị)</p>
                    </div>
                    <div class="timeline-task pb-3 border-bottom mb-3" style="border-left: 3px solid #17a2b8; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1 fw-bold small">Thành viên mới</h5>
                            <span class="time text-muted small"><i class="bi bi-clock"></i> 1 giờ trước</span>
                        </div>
                        <p class="text-muted small mb-0">Nguyễn Văn A đã đăng ký tài khoản mới</p>
                    </div>
                    <div class="timeline-task pb-3 border-bottom mb-3" style="border-left: 3px solid #28a745; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1 fw-bold small">Đơn hàng hoàn tất</h5>
                            <span class="time text-muted small"><i class="bi bi-clock"></i> 3 giờ trước</span>
                        </div>
                        <p class="text-muted small mb-0">Đơn hàng #99214 đã được giao thành công</p>
                    </div>
                    <div class="timeline-task pb-3" style="border-left: 3px solid #e11b22; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1 fw-bold small">Cảnh báo hệ thống</h5>
                            <span class="time text-muted small"><i class="bi bi-clock"></i> 5 giờ trước</span>
                        </div>
                        <p class="text-muted small mb-0">Phát hiện độ trễ cao tại cổng thanh toán</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Low Stock Alert -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-sm bg-primary text-white" style="background-color: var(--primary-color) !important;">
            <div class="card-body">
                <h4 class="card-title text-white">Cảnh báo hết hàng</h4>
                <p class="mb-4 small">Có 8 sản phẩm đang dưới ngưỡng tồn kho an toàn. Cần nhập thêm hàng ngay.</p>
                <button class="btn btn-light text-danger w-100 fw-bold">Nhập hàng ngay</button>
            </div>
        </div>
    </div>

    <!-- Customer Sentiment -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-sm bg-info text-white" style="background-color: #007bff !important;">
            <div class="card-body">
                <h4 class="card-title text-white">Mức độ hài lòng</h4>
                <h1 class="display-4 text-white fw-bold mb-2">94% <i class="bi bi-emoji-smile"></i></h1>
                <p class="mb-4 small">Phản hồi tích cực tăng 4% so với tháng trước.</p>
                <button class="btn btn-light text-info w-100 fw-bold" style="color: #007bff !important;">Xem đánh giá</button>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-4">Sức khỏe hệ thống</h4>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Cơ sở dữ liệu chính</span>
                        <span class="text-danger fw-bold small">99.9%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 99.9%"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Cổng kết nối API</span>
                        <span class="text-info fw-bold small">94.2%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 94.2%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include 'views/layouts/admin_footer.php'; ?>