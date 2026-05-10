<?php
// views/admin/manage_article_comments.php
include 'views/layouts/admin_header.php';
require_once 'config/db.php';
$pdo = Database::getConnection();

// Get reported comments count
$reportedCount = (int)$pdo->query("SELECT COUNT(*) FROM article_comments WHERE report_count > 0")->fetchColumn();
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM article_comments WHERE status = 'pending'")->fetchColumn();
?>

<div class="main-content-inner">
    <div class="content-header mb-4">
        <h2 class="fw-bold"><i class="ti-comments me-2 text-primary"></i>Duyệt bình luận bài viết</h2>
        <p class="text-muted">Quản lý, phê duyệt và xử lý các báo cáo vi phạm bình luận từ người dùng</p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 border-start border-4 border-danger">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-light-danger text-danger rounded-circle p-3 me-3">
                        <i class="ti-alert fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Bình luận bị báo cáo</div>
                        <h3 class="mb-0 fw-bold text-danger" id="reportedCount"><?= number_format($reportedCount) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 border-start border-4 border-warning">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-light-warning text-warning rounded-circle p-3 me-3">
                        <i class="ti-timer fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Đang chờ duyệt</div>
                        <h3 class="mb-0 fw-bold text-warning" id="pendingCount"><?= number_format($pendingCount) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & List Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 fw-bold text-dark">Danh sách bình luận</h5>
            <div class="d-flex gap-2 flex-wrap">
                <select id="statusFilter" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Đang chờ duyệt</option>
                    <option value="approved">Đã phê duyệt</option>
                    <option value="rejected">Đã từ chối</option>
                </select>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" id="commentSearch" class="form-control" placeholder="Tìm nội dung / người dùng...">
                    <button class="btn btn-primary" type="button"><i class="ti-search"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Người dùng</th>
                            <th>Bài viết</th>
                            <th>Nội dung bình luận</th>
                            <th>Báo cáo</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="commentsTableBody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <nav id="commentPagination"></nav>
        </div>
    </div>
</div>

<!-- Modal Báo cáo -->
<div class="modal fade" id="reportsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="ti-alert me-2"></i>Chi tiết báo cáo vi phạm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div id="reportsList"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-danger { background-color: rgba(225, 27, 34, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; }
    .table th { font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
    .status-select { min-width: 120px; font-weight: 600; font-size: 12px; padding: 4px 8px; border-radius: 6px; }
    .comment-content-cell { max-width: 300px; font-size: 14px; color: #444; }
    .report-badge { cursor: pointer; transition: transform 0.2s; }
    .report-badge:hover { transform: scale(1.05); }
    .report-card { border-left: 4px solid #e11b22; transition: all 0.3s; }
    .report-card:hover { background-color: #fff !important; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadComments();
        document.getElementById('statusFilter').onchange = () => loadComments(1);
        document.getElementById('commentSearch').onkeyup = (e) => {
            if(e.key === 'Enter' || e.target.value === '') loadComments(1);
        };
    });

    let currentCommentPage = 1;

    function loadComments(page = 1) {
        currentCommentPage = page;
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('commentSearch').value;
        
        fetch(`ajax/admin/article_comment_handler.php?action=list&page=${page}&status=${status}&search=${search}`)
            .then(res => res.json())
            .then(data => {
                renderTable(data.items);
                renderPagination(page, data.total_pages);
                document.getElementById('reportedCount').textContent = data.reported_count;
                // Add pending count if returned or update separately
            });
    }

    function renderTable(comments) {
        const tbody = document.getElementById('commentsTableBody');
        if (comments.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy bình luận nào.</td></tr>';
            return;
        }
        tbody.innerHTML = comments.map(c => `
            <tr class="${c.report_count > 0 ? 'bg-light-danger' : ''} ${c.status === 'pending' ? 'bg-light-warning' : ''}">
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-secondary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                            ${c.commenter_name.charAt(0).toUpperCase()}
                        </div>
                        <div class="fw-bold text-dark">${c.commenter_name}</div>
                    </div>
                </td>
                <td><small class="text-truncate d-block text-muted" style="max-width: 150px;" title="${c.article_title}">${c.article_title}</small></td>
                <td><div class="comment-content-cell">${c.content}</div></td>
                <td>
                    ${c.report_count > 0 
                        ? `<span class="badge bg-danger report-badge" onclick="viewReports(${c.id})"><i class="ti-alert me-1"></i>${c.report_count} báo cáo</span>` 
                        : '<span class="text-muted small">0 báo cáo</span>'}
                </td>
                <td>
                    <select class="form-select form-select-sm status-select ${getStatusClass(c.status)}" onchange="updateStatus(${c.id}, this.value)">
                        <option value="pending" ${c.status === 'pending' ? 'selected' : ''}>Chờ duyệt</option>
                        <option value="approved" ${c.status === 'approved' ? 'selected' : ''}>Đã duyệt</option>
                        <option value="rejected" ${c.status === 'rejected' ? 'selected' : ''}>Từ chối</option>
                    </select>
                </td>
                <td><small class="text-muted">${new Date(c.created_at).toLocaleDateString('vi-VN')}</small></td>
                <td class="text-end pe-4">
                    <div class="btn-group">
                        <button class="btn btn-sm ${c.is_hidden ? 'btn-secondary' : 'btn-outline-secondary'}" onclick="toggleHidden(${c.id}, ${!c.is_hidden})" title="${c.is_hidden ? 'Hiện' : 'Ẩn'} bình luận">
                            <i class="ti-${c.is_hidden ? 'eye' : 'na'}"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteComment(${c.id})" title="Xóa vĩnh viễn"><i class="ti-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function getStatusClass(status) {
        if (status === 'approved') return 'text-success border-success bg-success-subtle';
        if (status === 'rejected') return 'text-danger border-danger bg-danger-subtle';
        return 'text-warning border-warning bg-warning-subtle';
    }

    function updateStatus(id, status) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);
        fetch('ajax/admin/article_comment_handler.php?action=update_status', {
            method: 'POST',
            body: formData
        }).then(() => loadComments(currentCommentPage));
    }

    function toggleHidden(id, isHidden) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('is_hidden', isHidden ? 1 : 0);
        fetch('ajax/admin/article_comment_handler.php?action=toggle_hidden', {
            method: 'POST',
            body: formData
        }).then(() => loadComments(currentCommentPage));
    }

    function deleteComment(id) {
        if(!confirm('Bạn có chắc muốn xóa vĩnh viễn bình luận này? Thao tác này không thể hoàn tác.')) return;
        const formData = new FormData();
        formData.append('id', id);
        fetch('ajax/admin/article_comment_handler.php?action=delete', {
            method: 'POST',
            body: formData
        }).then(() => loadComments(currentCommentPage));
    }

    function viewReports(commentId) {
        fetch(`ajax/admin/article_comment_handler.php?action=get_reports&comment_id=${commentId}`)
            .then(res => res.json())
            .then(reports => {
                const list = document.getElementById('reportsList');
                if (reports.length === 0) {
                    list.innerHTML = '<p class="text-center py-4 text-muted">Không có dữ liệu báo cáo.</p>';
                } else {
                    list.innerHTML = reports.map(r => `
                        <div class="card mb-3 border-0 shadow-sm report-card bg-white">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-bold text-dark"><i class="ti-user me-2 text-muted"></i>${r.reporter_name}</div>
                                    <span class="badge ${r.status === 'pending' ? 'bg-warning text-dark' : 'bg-success'} text-uppercase" style="font-size: 10px;">${r.status}</span>
                                </div>
                                <div class="badge bg-danger-subtle text-danger border border-danger mb-2" style="font-size: 11px;">Lý do: ${r.reason}</div>
                                <p class="text-muted small mb-3">${r.description || 'Không có mô tả chi tiết'}</p>
                                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                    <small class="text-muted"><i class="ti-calendar me-1"></i>${new Date(r.created_at).toLocaleString('vi-VN')}</small>
                                    ${r.status === 'pending' ? `<button class="btn btn-sm btn-primary" onclick="resolveReport(${r.id}, 'resolved')"><i class="ti-check me-1"></i>Đã xử lý</button>` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
                new bootstrap.Modal(document.getElementById('reportsModal')).show();
            });
    }

    function resolveReport(reportId, status) {
        const formData = new FormData();
        formData.append('report_id', reportId);
        formData.append('status', status);
        fetch('ajax/admin/article_comment_handler.php?action=resolve_report', {
            method: 'POST',
            body: formData
        }).then(() => {
            bootstrap.Modal.getInstance(document.getElementById('reportsModal')).hide();
            loadComments(currentCommentPage);
        });
    }

    function renderPagination(current, total) {
        const nav = document.getElementById('commentPagination');
        nav.innerHTML = '<ul class="pagination pagination-sm justify-content-end mb-0"></ul>';
        const ul = nav.querySelector('ul');
        if (total <= 1) return;
        
        ul.innerHTML += `<li class="page-item ${current === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadComments(${current - 1})">Trước</a></li>`;
        for (let i = 1; i <= total; i++) {
            ul.innerHTML += `<li class="page-item ${i === current ? 'active' : ''}"><a class="page-link" href="#" onclick="loadComments(${i})">${i}</a></li>`;
        }
        ul.innerHTML += `<li class="page-item ${current === total ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadComments(${current + 1})">Sau</a></li>`;
    }
</script>

<?php include 'views/layouts/admin_footer.php'; ?>
