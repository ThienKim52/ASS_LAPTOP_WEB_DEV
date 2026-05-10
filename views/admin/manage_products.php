<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-box-seam"></i> Quản lý Sản phẩm</h2>
                    <p class="text-muted">Xem, thêm, sửa và xoá các sản phẩm trong hệ thống.</p>
                </div>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm mới
                </button>
            </div>

            <div class="content-card">
                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="text-muted small">Tổng sản phẩm</div>
                                <div class="h3 mb-0 fw-bold"><?= number_format($totalProducts) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="productTable">
                        <thead class="table-light">
                            <tr>
                                <th>Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Thương hiệu</th>
                                <th>Giá</th>
                                <th>Bộ nhớ</th>
                                <th>RAM</th>
                                <th>Kho</th>
                                <th>Danh mục</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $imageSrc = htmlspecialchars($product['image']);
                                            if (!empty($imageSrc) && (strpos($imageSrc, 'http') === 0 || file_exists($imageSrc))) {
                                                echo '<img src="' . $imageSrc . '" alt="' . htmlspecialchars($product['name']) . '" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">';
                                            } else {
                                                echo '<img src="assets/img/placeholder.png" alt="No image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">';
                                            }
                                            ?>
                                        </td>
                                        <td class="fw-bold"><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['brand']) ?></td>
                                        <td class="text-primary fw-bold"><?= number_format($product['price']) ?>₫</td>
                                        <td><?= htmlspecialchars($product['storage']) ?></td>
                                        <td><?= htmlspecialchars($product['ram'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $product['stock'] ?>
                                            </span>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($product['category']) ?></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-warning" onclick="editProduct(<?= $product['id'] ?>)" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(<?= $product['id'] ?>)" title="Xoá">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=manage_products&product_page=1">&laquo;&laquo;</a>
                        </li>

                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=manage_products&product_page=1">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=manage_products&product_page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=manage_products&product_page=<?= $totalPages ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif; ?>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=manage_products&product_page=<?= $totalPages ?>">&raquo;&raquo;</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
</div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="productForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="productId" name="id">
                        <input type="hidden" id="existingImage" name="existing_image">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tên sản phẩm *</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thương hiệu *</label>
                                <input type="text" class="form-control" id="productBrand" name="brand" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá (₫) *</label>
                                <input type="number" class="form-control" id="productPrice" name="price" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Bộ nhớ</label>
                                <select class="form-select" id="productStorage" name="storage">
                                    <option value="64GB">64GB</option>
                                    <option value="128GB">128GB</option>
                                    <option value="256GB">256GB</option>
                                    <option value="512GB">512GB</option>
                                    <option value="1TB">1TB</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Số lượng trong kho *</label>
                                <input type="number" class="form-control" id="productStock" name="stock" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">RAM</label>
                                <select class="form-select" id="productRam" name="ram">
                                    <option value="">Chọn RAM</option>
                                    <option value="4GB">4GB</option>
                                    <option value="6GB">6GB</option>
                                    <option value="8GB">8GB</option>
                                    <option value="12GB">12GB</option>
                                    <option value="16GB">16GB</option>
                                    <option value="18GB">18GB</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select class="form-select" id="productCategory" name="category">
                                    <option value="Smartphone">Smartphone</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Accessory">Phụ kiện</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            <div id="dropZone" class="border rounded p-4 text-center bg-light" style="cursor: pointer; border-style: dashed !important;">
                                <i class="bi bi-cloud-upload" style="font-size: 2.5rem; color: #6c757d;"></i>
                                <p class="mb-1">Kéo thả ảnh vào đây hoặc click để chọn</p>
                                <small class="text-muted">Định dạng hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)</small>
                                <input type="file" class="d-none" id="productImage" name="image" accept="image/*">
                            </div>
                            <div id="currentImagePreview" class="mt-2"></div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/javascript/admin_manage_products.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
