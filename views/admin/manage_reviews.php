<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

        <div class="content-header mb-4">
            <h2><i class="bi bi-star-fill"></i> Quản lý đánh giá sản phẩm</h2>
            <p class="text-muted">Xem, duyệt hoặc xoá các đánh giá từ khách hàng về sản phẩm.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <div class="text-muted small">Tổng đánh giá</div>
                        <div class="h3 mb-0 fw-bold"><?= number_format($stats['total']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body">
                        <div class="text-muted small">Đang chờ duyệt</div>
                        <div class="h3 mb-0 fw-bold"><?= number_format($stats['pending']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="text-muted small">Đã duyệt</div>
                        <div class="h3 mb-0 fw-bold"><?= number_format($stats['approved']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body">
                        <div class="text-muted small">Đã từ chối</div>
                        <div class="h3 mb-0 fw-bold"><?= number_format($stats['rejected']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <ul class="nav nav-pills card-header-pills">
                    <li class="nav-item">
                        <a href="?page=manage_reviews&status=all" class="nav-link <?= $status === 'all' ? 'active' : '' ?>">
                            Tất cả
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=manage_reviews&status=pending" class="nav-link <?= $status === 'pending' ? 'active' : '' ?>">
                            Chờ duyệt <?php if($stats['pending'] > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?= $stats['pending'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=manage_reviews&status=approved" class="nav-link <?= $status === 'approved' ? 'active' : '' ?>">
                            Đã duyệt
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=manage_reviews&status=rejected" class="nav-link <?= $status === 'rejected' ? 'active' : '' ?>">
                            Đã từ chối
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Reviews Table -->
            <div class="card-body p-0">
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Không có đánh giá nào được tìm thấy.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Sản phẩm</th>
                                    <th>Khách hàng</th>
                                    <th>Đánh giá</th>
                                    <th style="width: 35%;">Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th class="pe-4 text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($review['product_name']) ?>">
                                                <?= htmlspecialchars($review['product_name']) ?>
                                            </div>
                                            <small class="text-muted">ID: <?= $review['product_id'] ?></small>
                                        </td>
                                        
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($review['user_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($review['user_email']) ?></small>
                                        </td>
                                        
                                        <td>
                                            <div class="text-warning">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <small class="text-muted"><?= $review['rating'] ?>/5 sao</small>
                                        </td>
                                        
                                        <td>
                                            <div class="small text-muted text-wrap">
                                                <?= htmlspecialchars(substr($review['comment'] ?? '', 0, 200)) ?><?= strlen($review['comment'] ?? '') > 200 ? '...' : '' ?>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <?php 
                                            // Map 'reject' from DB to 'rejected' for our badges
                                            $currentStatus = ($review['status'] === 'reject' ? 'rejected' : $review['status']);
                                            
                                            $statusBadges = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Chờ duyệt',
                                                'approved' => 'Đã duyệt',
                                                'rejected' => 'Từ chối'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusBadges[$currentStatus] ?? 'secondary' ?>">
                                                <?= $statusLabels[$currentStatus] ?? $review['status'] ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="small"><?= date('d/m/Y', strtotime($review['created_at'])) ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($review['created_at'])) ?></small>
                                        </td>
                                        
                                        <td class="pe-4 text-end">
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($review['status'] !== 'approved'): ?>
                                                    <button class="btn btn-outline-success" 
                                                            onclick="updateReviewStatus(<?= $review['id'] ?>, 'approved')"
                                                            title="Duyệt">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($review['status'] !== 'reject'): ?>
                                                    <button class="btn btn-outline-warning" 
                                                            onclick="updateReviewStatus(<?= $review['id'] ?>, 'rejected')"
                                                            title="Từ chối">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button class="btn btn-outline-danger" 
                                                        onclick="deleteReview(<?= $review['id'] ?>)"
                                                        title="Xoá">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="card-footer bg-white border-0 py-3">
                    <nav aria-label="Review pagination">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=manage_reviews&status=<?= $status ?>&current_page=<?= $currentPage - 1 ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=manage_reviews&status=<?= $status ?>&current_page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=manage_reviews&status=<?= $status ?>&current_page=<?= $currentPage + 1 ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
</div>

<script>
function updateReviewStatus(reviewId, status) {
    const statusText = status === 'approved' ? 'duyệt' : 'từ chối';
    if (!confirm(`Bạn có chắc chắn muốn ${statusText} đánh giá này?`)) return;
    
    fetch('ajax/admin/update_review_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({review_id: reviewId, status: status})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi kết nối máy chủ');
    });
}

function deleteReview(reviewId) {
    if (!confirm('⚠️ Bạn có chắc chắn muốn XOÁ VĨNH VIỄN đánh giá này? Thao tác này không thể hoàn tác.')) return;
    
    fetch('ajax/admin/delete_review.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({review_id: reviewId})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi kết nối máy chủ');
    });
}
</script>

<?php include 'views/layouts/admin_footer.php'; ?>
