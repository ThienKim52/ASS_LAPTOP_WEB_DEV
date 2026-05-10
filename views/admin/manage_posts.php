<?php
// views/admin/manage_posts.php
include 'views/layouts/admin_header.php';
require_once 'config/db.php';
$pdo = Database::getConnection();

// Get stats for the page
$totalPosts = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$publishedPosts = $pdo->query("SELECT COUNT(*) FROM articles WHERE published_at IS NOT NULL AND published_at <= NOW()")->fetchColumn();
$draftPosts = $pdo->query("SELECT COUNT(*) FROM articles WHERE published_at IS NULL OR published_at > NOW()")->fetchColumn();
?>

<div class="main-content-inner">
    <div class="content-header mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold"><i class="ti-write me-2 text-primary"></i>Quản lý bài viết</h2>
            <p class="text-muted">Quản lý nội dung tin tức và bài viết trên hệ thống</p>
        </div>
        <button class="btn btn-primary shadow-sm px-4" onclick="openCreateModal()">
            <i class="ti-plus me-2"></i>Thêm bài viết mới
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-light-primary text-primary rounded-circle p-3 me-3">
                        <i class="ti-layers-alt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Tổng bài viết</div>
                        <h3 class="mb-0 fw-bold"><?= number_format($totalPosts) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-light-success text-success rounded-circle p-3 me-3">
                        <i class="ti-check-box fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Đã xuất bản</div>
                        <h3 class="mb-0 fw-bold"><?= number_format($publishedPosts) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-light-warning text-warning rounded-circle p-3 me-3">
                        <i class="ti-pencil-alt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Bản nháp</div>
                        <h3 class="mb-0 fw-bold"><?= number_format($draftPosts) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Articles List Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Danh sách bài viết</h5>
            <div class="search-box-admin" style="width: 300px;">
                <div class="input-group input-group-sm">
                    <input type="text" id="adminSearchInput" class="form-control" placeholder="Tìm kiếm tiêu đề...">
                    <button class="btn btn-primary" type="button" id="adminSearchBtn">
                        <i class="ti-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="postsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th>Hình ảnh</th>
                            <th>Tiêu đề & Slug</th>
                            <th>Tác giả</th>
                            <th>Phản hồi</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="postsTableBody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <nav id="adminPagination"></nav>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa bài viết -->
<div class="modal fade" id="articleModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="articleModalTitle">Thêm bài viết mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="articleForm">
                    <input type="hidden" name="id" id="articleId">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="articleTitle" class="form-control form-control-lg" required onkeyup="generateSlug(this.value)" placeholder="Nhập tiêu đề hấp dẫn...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Đường dẫn (Slug) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">/post/</span>
                                            <input type="text" name="slug" id="articleSlug" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Nội dung bài viết</label>
                                        <textarea name="content" id="articleContent" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white fw-bold">Thiết lập xuất bản</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ngày xuất bản</label>
                                        <input type="datetime-local" name="published_at" id="articlePublishedAt" class="form-control">
                                        <small class="text-muted mt-1 d-block"><i class="ti-info-alt me-1"></i>Để trống để lưu dưới dạng bản nháp.</small>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Ảnh đại diện</label>
                                        <div class="input-group mb-2">
                                            <input type="text" name="thumbnail_url" id="articleThumbnailUrl" class="form-control" placeholder="URL ảnh...">
                                            <input type="file" id="thumbnailUpload" class="d-none" accept="image/*">
                                            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('thumbnailUpload').click()" title="Tải ảnh lên">
                                                <i class="ti-upload"></i>
                                            </button>
                                        </div>
                                        <div id="thumbnailPreview" class="mt-2 border rounded p-2 text-center bg-white" style="min-height: 100px;">
                                            <span class="text-muted small">Xem trước hình ảnh</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white fw-bold">Tối ưu SEO (Metadata)</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Meta Title</label>
                                        <input type="text" name="meta_title" id="articleMetaTitle" class="form-control" placeholder="Tiêu đề hiển thị trên Google">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" id="articleMetaKeywords" class="form-control" placeholder="laptop, gaming, 2025...">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">Meta Description</label>
                                        <textarea name="meta_description" id="articleMetaDesc" class="form-control" rows="3" placeholder="Mô tả ngắn gọn nội dung bài viết..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" form="articleForm" class="btn btn-primary px-4 fw-bold">
                    <i class="ti-save me-2"></i>Lưu bài viết
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-primary { background-color: rgba(225, 27, 34, 0.1); }
    .bg-light-success { background-color: rgba(40, 167, 69, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; }
    .table th { font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
    .post-img-thumb { width: 60px; height: 45px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .tox-tinymce { border-radius: 8px !important; border: 1px solid #dee2e6 !important; }
</style>

<script src="https://cdn.tiny.cloud/1/oa2v1mkxyzsifaczvsfoxw78808dy4y7eitfnahkxfyc835c/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#articleContent',
            height: 550,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount emoticons',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image media table | emoticons charmap | removeformat | help',
            images_upload_url: 'ajax/admin/article_image_upload.php',
            automatic_uploads: true,
            relative_urls: false,
            remove_script_host: false,
            document_base_url: 'http://localhost/store/',
            content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:16px; line-height: 1.6; color: #333; } img { max-width: 100%; height: auto; border-radius: 8px; }',
            image_advtab: true,
            image_title: true,
            image_dimensions: true,
            image_class_list: [
                {title: 'None', value: ''},
                {title: 'Responsive', value: 'img-fluid rounded'},
                {title: 'Shadow', value: 'img-fluid rounded shadow-sm'}
            ]
        });

        loadPosts();

        document.getElementById('adminSearchBtn').onclick = () => loadPosts(1);
        document.getElementById('adminSearchInput').onkeypress = (e) => {
            if(e.key === 'Enter') loadPosts(1);
        };

        document.getElementById('articleThumbnailUrl').oninput = function() {
            const url = this.value;
            const preview = document.getElementById('thumbnailPreview');
            if (url) {
                preview.innerHTML = `<img src="${url}" class="img-fluid rounded shadow-sm" style="max-height: 150px;" onerror="this.parentElement.innerHTML='<span class=\"text-danger\">Link ảnh không hợp lệ</span>'">`;
            } else {
                preview.innerHTML = '<span class="text-muted small">Xem trước hình ảnh</span>';
            }
        };

        document.getElementById('thumbnailUpload').onchange = function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('file', this.files[0]);
                
                const btn = this.nextElementSibling;
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                btn.disabled = true;

                fetch('ajax/admin/article_image_upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.location) {
                        document.getElementById('articleThumbnailUrl').value = data.location;
                        document.getElementById('articleThumbnailUrl').oninput();
                    } else {
                        alert('Lỗi tải ảnh lên');
                    }
                })
                .catch(err => alert('Lỗi: ' + err))
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
            }
        };
    });

    let adminCurrentPage = 1;

    function loadPosts(page = 1) {
        adminCurrentPage = page;
        const search = document.getElementById('adminSearchInput').value;
        fetch(`ajax/manage_posts_handler.php?action=list&page=${page}&search=${search}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderTable(data.posts);
                    renderPagination(page, data.total);
                }
            });
    }

    function renderTable(posts) {
        const tbody = document.getElementById('postsTableBody');
        if (posts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">Không tìm thấy bài viết nào.</td></tr>';
            return;
        }
        tbody.innerHTML = posts.map(post => `
            <tr>
                <td class="ps-4 text-muted">#${post.id}</td>
                <td>
                    <img src="${post.thumbnail_url || 'assets/img/placeholder.png'}" class="post-img-thumb" onerror="this.onerror=null; this.src='assets/img/placeholder.png'">
                </td>
                <td>
                    <div class="fw-bold text-dark">${post.title}</div>
                    <small class="text-muted">slug: ${post.slug}</small>
                </td>
                <td><span class="badge bg-light text-dark fw-normal border"><i class="ti-user me-1"></i>${post.author_name || 'Admin'}</span></td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="ti-comment-alt text-muted me-1"></i>
                        <span class="fw-bold">${post.comment_count}</span>
                    </div>
                </td>
                <td>
                    ${post.published_at 
                        ? '<span class="status-badge bg-success-subtle text-success border border-success">ĐÃ ĐĂNG</span>' 
                        : '<span class="status-badge bg-warning-subtle text-warning border border-warning">BẢN NHÁP</span>'}
                </td>
                <td><small class="text-muted">${post.published_at ? new Date(post.published_at).toLocaleDateString('vi-VN') : '-'}</small></td>
                <td class="text-end pe-4">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="editPost(${post.id})" title="Chỉnh sửa"><i class="ti-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePost(${post.id})" title="Xóa"><i class="ti-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function openCreateModal() {
        document.getElementById('articleForm').reset();
        document.getElementById('articleId').value = '';
        if(tinymce.get('articleContent')) tinymce.get('articleContent').setContent('');
        document.getElementById('thumbnailPreview').innerHTML = '<span class="text-muted small">Xem trước hình ảnh</span>';
        document.getElementById('articleModalTitle').textContent = 'Thêm bài viết mới';
        new bootstrap.Modal(document.getElementById('articleModal')).show();
    }

    function editPost(id) {
        fetch(`ajax/manage_posts_handler.php?action=get&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const post = data.post;
                    document.getElementById('articleId').value = post.id;
                    document.getElementById('articleTitle').value = post.title;
                    document.getElementById('articleSlug').value = post.slug;
                    if(tinymce.get('articleContent')) tinymce.get('articleContent').setContent(post.content || '');
                    document.getElementById('articlePublishedAt').value = post.published_at ? post.published_at.replace(' ', 'T').substring(0, 16) : '';
                    document.getElementById('articleThumbnailUrl').value = post.thumbnail_url;
                    document.getElementById('articleMetaTitle').value = post.meta_title;
                    document.getElementById('articleMetaKeywords').value = post.meta_keywords;
                    document.getElementById('articleMetaDesc').value = post.meta_description;
                    
                    // Trigger preview
                    document.getElementById('articleThumbnailUrl').oninput();
                    
                    document.getElementById('articleModalTitle').textContent = 'Chỉnh sửa bài viết';
                    new bootstrap.Modal(document.getElementById('articleModal')).show();
                }
            });
    }

    document.getElementById('articleForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        if(tinymce.get('articleContent')) {
            formData.append('content', tinymce.get('articleContent').getContent());
        }

        fetch('ajax/manage_posts_handler.php?action=save', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('articleModal')).hide();
                loadPosts(adminCurrentPage);
                alert('Đã lưu bài viết thành công!');
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Lỗi server'));
            }
        });
    };

    function deletePost(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa bài viết này? Thao tác này không thể hoàn tác.')) return;
        const formData = new FormData();
        formData.append('id', id);
        fetch('ajax/manage_posts_handler.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) loadPosts(adminCurrentPage);
        });
    }

    function generateSlug(text) {
        const slug = text.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[đĐ]/g, 'd')
            .replace(/[^a-z0-9 ]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('articleSlug').value = slug;
    }

    function renderPagination(current, total) {
        const totalPages = Math.ceil(total / 10);
        const nav = document.getElementById('adminPagination');
        nav.innerHTML = '<ul class="pagination pagination-sm justify-content-end mb-0"></ul>';
        const ul = nav.querySelector('ul');
        if (totalPages <= 1) return;

        ul.innerHTML += `<li class="page-item ${current === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadPosts(${current - 1})">Trước</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            ul.innerHTML += `<li class="page-item ${i === current ? 'active' : ''}"><a class="page-link" href="#" onclick="loadPosts(${i})">${i}</a></li>`;
        }
        ul.innerHTML += `<li class="page-item ${current === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadPosts(${current + 1})">Sau</a></li>`;
    }
</script>

<?php include 'views/layouts/admin_footer.php'; ?>
