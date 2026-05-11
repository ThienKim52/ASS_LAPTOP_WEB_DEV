<?php
// File: views/admin/manage_orders.php

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php?page=home");
    exit();
}

require_once __DIR__ . '/../../models/OrderModel.php';

$orderModel = new OrderModel();

// Handle POST update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = $_POST['new_status'];
    
    if ($orderModel->updateOrderStatus($orderId, $newStatus)) {
        $successMessage = "Cập nhật trạng thái đơn hàng thành công!";
    } else {
        $errorMessage = "Lỗi khi cập nhật trạng thái đơn hàng.";
    }
}

// Get filter and pagination
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$page = isset($_GET['order_page']) ? (int)$_GET['order_page'] : 1;
$limit = 10;

// Get orders
$orderData = $orderModel->getPaginatedOrders($page, $limit, $status);
$orders = $orderData['orders'];
$totalPages = $orderData['totalPages'];
$totalOrders = $orderData['total'];

function getStatusColor($status) {
    switch($status) {
        case 'pending': return 'warning';
        case 'processing': return 'info';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function getStatusLabel($status) {
    switch($status) {
        case 'pending': return 'Chờ duyệt';
        case 'processing': return 'Đang xử lý';
        case 'completed': return 'Hoàn tất';
        case 'cancelled': return 'Đã huỷ';
        default: return $status;
    }
}
?>

<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <h2><i class="bi bi-receipt"></i> Quản lý Đơn hàng</h2>
                <p class="text-muted">Theo dõi, cập nhật trạng thái và quản lý danh sách đơn hàng của khách hàng.</p>
            </div>

            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-2"></i><?= $successMessage ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $errorMessage ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filter Tabs -->
            <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm">
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" 
                        href="index.php?page=manage_orders&status=all">
                        Tất cả
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" 
                        href="index.php?page=manage_orders&status=pending">
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'processing' ? 'active' : '' ?>" 
                        href="index.php?page=manage_orders&status=processing">
                        <span class="badge bg-info">Đang xử lý</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'completed' ? 'active' : '' ?>" 
                        href="index.php?page=manage_orders&status=completed">
                        <span class="badge bg-success">Hoàn tất</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'cancelled' ? 'active' : '' ?>" 
                        href="index.php?page=manage_orders&status=cancelled">
                        <span class="badge bg-danger">Đã huỷ</span>
                    </a>
                </li>
            </ul>

            <!-- Orders Table -->
            <?php if (empty($orders)): ?>
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="bi bi-info-circle me-2"></i> Không tìm thấy đơn hàng nào với bộ lọc này.
                </div>
            <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <a href="index.php?page=order_details&id=<?= $order['id'] ?>" 
                                           class="text-decoration-none fw-bold text-primary" 
                                           title="Xem chi tiết">
                                           #<?= $order['id'] ?>
                                        </a>
                                    </td>

                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        <small class="text-muted">
                                            <i class="bi bi-envelope small"></i> <?= htmlspecialchars($order['customer_email']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                    </td>
                                    <td><strong class="text-danger"><?= number_format($order['total']) ?>₫</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($order['payment_method']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                                            <?= getStatusLabel($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <div class="input-group input-group-sm" style="max-width:220px;">
                                                <select name="new_status" class="form-select form-select-sm">
                                                    <option value="">-- Cập nhật --</option>
                                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                                                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã huỷ</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-success" title="Cập nhật">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Order pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=manage_orders&status=<?= $status ?>&order_page=1">&laquo;&laquo;</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=manage_orders&status=<?= $status ?>&order_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=manage_orders&status=<?= $status ?>&order_page=<?= $totalPages ?>">&raquo;&raquo;</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
</div>

<?php include 'views/layouts/admin_footer.php'; ?>
