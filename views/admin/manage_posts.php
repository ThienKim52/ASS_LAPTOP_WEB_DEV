<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-file-text"></i> Quản lý bài viết</h2>
                    <p class="text-muted">Tạo, chỉnh sửa và quản lý các bài tin tức, hướng dẫn.</p>
                </div>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    <i class="bi bi-plus-circle me-1"></i> Viết bài mới
                </button>
            </div>

            <div class="content-card">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-sm-4">
                            <div class="card border-0 shadow-sm bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Đã xuất bản</div>
                                            <div class="h2 mb-0 fw-bold" id="stat-published">0</div>
                                        </div>
                                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card border-0 shadow-sm bg-warning text-dark">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Bản nháp</div>
                                            <div class="h2 mb-0 fw-bold" id="stat-draft">0</div>
                                        </div>
                                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card border-0 shadow-sm bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Đã lưu trữ</div>
                                            <div class="h2 mb-0 fw-bold" id="stat-archived">0</div>
                                        </div>
                                        <i class="bi bi-archive fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Posts Table Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Danh sách bài viết</h3>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary active" onclick="filterPosts(null)">Tất cả</button>
                                    <button type="button" class="btn btn-outline-success" onclick="filterPosts('published')">Đã đăng</button>
                                    <button type="button" class="btn btn-outline-warning" onclick="filterPosts('draft')">Nháp</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filterPosts('archived')">Lưu trữ</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle card-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Bài viết</th>
                                        <th>Danh mục</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Lượt xem</th>
                                        <th class="text-center">Tương tác</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="posts-table-body">
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-white border-0" id="posts-pagination"></div>
                    </div>
                </div>
</div>

    <!-- Create/Edit Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="postModalTitle">Viết bài mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="postForm">
                        <input type="hidden" id="post-id" name="id">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold required">Tiêu đề bài viết *</label>
                            <input type="text" class="form-control" id="post-title" name="title" placeholder="Nhập tiêu đề bài viết" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select class="form-select" id="post-category" name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <select class="form-select" id="post-status" name="status">
                                    <option value="draft">Bản nháp</option>
                                    <option value="published">Xuất bản</option>
                                    <option value="archived">Lưu trữ</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh bài viết</label>
                            <div id="drop-zone" class="border rounded p-4 text-center bg-light" style="cursor: pointer; border-style: dashed !important;">
                                <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                <p class="mb-1">Kéo thả ảnh vào đây hoặc click để chọn</p>
                                <small class="text-muted">JPG, PNG, GIF, tối đa 5MB</small>
                                <input type="file" class="d-none" id="post-image" name="image" accept="image/*">
                            </div>
                            <div id="image-preview" class="mt-2" style="display: none;">
                                <div class="position-relative d-inline-block">
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeImagePreview()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold required">Nội dung bài viết *</label>
                            <!-- Formatting toolbar -->
                            <div class="content-toolbar border rounded-top p-2 bg-light d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')" title="Chữ đậm"><i class="bi bi-type-bold"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')" title="Chữ nghiêng"><i class="bi bi-type-italic"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')" title="Gạch chân"><i class="bi bi-type-underline"></i></button>
                                <span class="border-end mx-1"></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('h1')" title="Tiêu đề 1">H1</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('h2')" title="Tiêu đề 2">H2</button>
                                <span class="border-end mx-1"></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('unorderedList')" title="Danh sách dấu chấm"><i class="bi bi-list-ul"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('orderedList')" title="Danh sách số"><i class="bi bi-list-ol"></i></button>
                                <span class="border-end mx-1"></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('undo')" title="Hoàn tác"><i class="bi bi-arrow-counterclockwise"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('redo')" title="Làm lại"><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                            <div id="post-content" class="form-control content-editor border-top-0 rounded-0 rounded-bottom" contenteditable="true" style="min-height: 250px; max-height: 400px; overflow-y: auto;" placeholder="Viết nội dung bài viết ở đây..."></div>
                            <input type="hidden" id="post-content-hidden" name="content">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="button" class="btn btn-primary" onclick="savePost()">
                        <i class="bi bi-floppy me-1"></i> Lưu bài viết
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/javascript/admin_manage_posts.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
