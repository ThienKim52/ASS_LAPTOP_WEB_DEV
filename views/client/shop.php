<?php 
require_once 'helpers/settings_helper.php';
include 'views/layouts/header.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<section class="shop-banner position-relative overflow-hidden">
    <div class="container-fluid px-5 h-100">
        <div class="row align-items-center h-100 g-4">

            <div class="col-lg-6 text-white">
                <h1 class="banner-title">
                    <?php echo htmlspecialchars(getSetting('shop.page_title', 'Laptop')); ?>
                </h1>
                <p class="banner-desc mb-0">
                    Khám phá thế hệ laptop tiếp theo với hiệu suất tuyệt vời. Tìm kiếm bộ sưu tập premium của chúng tôi gồm ultrabook cao cấp, laptop gaming mạnh mẽ và workstation chuyên nghiệp.
                </p>

            </div>

            <div class="col-lg-6 position-relative text-center d-flex justify-content-lg-end justify-content-center">
                <div class="banner-image-wrap position-relative">
                <div class="tech-badge">
                    CÔNG NGHỆ HÀNG ĐẦU
                </div>

                <img src="/LaptopWeb-articles/FE/assets/img/products/product_1.webp"
     class="banner-image"
     alt="Laptop Banner">
                </div>
            </div>
        </div>
    </div>
</section>

<div class="filter-bar py-3 bg-light">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="filter-left d-flex align-items-center gap-3">
            <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel"></i> <?php echo htmlspecialchars(getSetting('shop.filter_title', 'Lọc')); ?>
            </button>
        </div>
        <div class="filter-right d-flex align-items-center gap-3">
            <span class="small"><?php echo htmlspecialchars(getSetting('shop.sort_label', 'Sắp xếp theo')); ?></span>
            <form id="sortForm" action="index.php" method="GET">
                <input type="hidden" name="page" value="shop">

                <?php
                 if (isset($_GET['brand']) && is_array($_GET['brand'])) {
                    foreach ($_GET['brand'] as $brand) {
                    echo '<input type="hidden" name="brand[]" value="' . htmlspecialchars($brand) . '">';
                    }
                }
        
                // Giữ lại category filter
                if (isset($_GET['category'])) {
                    echo '<input type="hidden" name="category" value="' . htmlspecialchars($_GET['category']) . '">';
                }
        
                // Giữ lại storage filters
                if (isset($_GET['storage']) && is_array($_GET['storage'])) {
                foreach ($_GET['storage'] as $storage) {
                    echo '<input type="hidden" name="storage[]" value="' . htmlspecialchars($storage) . '">';
                    }
                }
        
                // Giữ lại price_min
                if (isset($_GET['price_min'])) {
                    echo '<input type="hidden" name="price_min" value="' . htmlspecialchars($_GET['price_min']) . '">';
                }
        
                // Giữ lại price_max
                if (isset($_GET['price_max'])) {
                    echo '<input type="hidden" name="price_max" value="' . htmlspecialchars($_GET['price_max']) . '">';
                }

                // Giữ lại keyword search
                if (isset($_GET['search'])) {
                    echo '<input type="hidden" name="search" value="' . htmlspecialchars($_GET['search']) . '">';
                }
                ?>
        
                <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="default" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'default') echo 'selected'; ?>>Mặc định</option>
                    <option value="price_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Giá từ thấp đến cao</option>
                    <option value="price_desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Giá từ cao đến thấp</option>
                    <option value="name_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Tên từ A - Z</option>
                    </select>
            </form>
        </div>
    </div>
</div>

<div class="modal fade filter-modal" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="bi bi-funnel-fill"></i> <?php echo htmlspecialchars(getSetting('shop.filter_title', 'Lọc Sản Phẩm')); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="GET" action="index.php" id="filterForm">
                    <input type="hidden" name="page" value="shop">

                    <!-- Giữ lại giá trị sort hiện tại -->
                    <?php if(isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                    <?php endif; ?>

                    <!-- ========== 0. SEARCH FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-search text-danger"></i> Tìm Kiếm
                        </h6>
                        <input type="text"
                               class="form-control"
                               name="search"
                               placeholder="Nhập từ khóa sản phẩm"
                               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <small class="text-muted d-block mt-2">Tìm theo tên, thương hiệu, danh mục hoặc mô tả.</small>
                    </div>

                    <hr>

                    <!-- ========== 1. BRAND FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-phone text-danger"></i> Thương Hiệu
                        </h6>
                        <div class="filter-options">
                            <?php if (!empty($filterOptions['brands'])): ?>
                                <?php foreach($filterOptions['brands'] as $brand): ?>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="brand[]"
                                               value="<?= htmlspecialchars($brand) ?>"
                                               id="brand-<?= htmlspecialchars($brand) ?>"
                                               <?= in_array($brand, $_GET['brand'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="brand-<?= htmlspecialchars($brand) ?>">
                                            <?= htmlspecialchars($brand) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small">Không có thương hiệu</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- ========== 2. CATEGORY FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-grid-3x3-gap text-danger"></i> Danh Mục
                        </h6>
                        <div class="filter-options">
                            <?php if (!empty($filterOptions['categories'])): ?>
                                <?php foreach($filterOptions['categories'] as $cat): ?>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="category"
                                               value="<?= htmlspecialchars($cat) ?>"
                                               id="cat-<?= htmlspecialchars($cat) ?>"
                                               <?= (isset($_GET['category']) && $_GET['category'] == $cat) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="cat-<?= htmlspecialchars($cat) ?>">
                                            <?= htmlspecialchars($cat) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="category"
                                           value=""
                                           id="cat-all"
                                           <?= !isset($_GET['category']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cat-all">
                                        Tất cả danh mục
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- ========== 3. STORAGE FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-sd-card text-danger"></i> Bộ Nhớ
                        </h6>
                        <div class="filter-options">
                            <?php if (!empty($filterOptions['storages'])): ?>
                                <?php foreach($filterOptions['storages'] as $storage): ?>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="storage[]"
                                               value="<?= htmlspecialchars($storage) ?>"
                                               id="storage-<?= htmlspecialchars($storage) ?>"
                                               <?= in_array($storage, $_GET['storage'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="storage-<?= htmlspecialchars($storage) ?>">
                                            <?= htmlspecialchars($storage) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- ========== 4. PRICE RANGE FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-currency-dollar text-danger"></i> Khoảng Giá
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number"
                                       class="form-control"
                                       name="price_min"
                                       placeholder="Giá từ"
                                       value="<?= isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '' ?>"
                                       min="0"
                                       step="100">
                            </div>
                            <div class="col-6">
                                <input type="number"
                                       class="form-control"
                                       name="price_max"
                                       placeholder="Giá đến"
                                       value="<?= isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '' ?>"
                                       min="0"
                                       step="100">
                            </div>
                        </div>
                    </div>

                    <div class="filter-actions d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-danger flex-grow-1">
                            <i class="bi bi-check-circle"></i> Áp dụng
                        </button>
                        <a href="?page=shop" class="btn btn-outline-secondary flex-grow-1 text-center">
                            <i class="bi bi-x-circle"></i> Xóa lọc
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="shop-page-section py-5">
    <div class="container">
        <div class="product-grid">
            
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        
                        <div class="product-image-wrapper">
                            <?php 
                                $defaultImg = 'assets/img/products/product_1.webp';
                                $imgUrl = $defaultImg;

                                $normalizedImage = '';
                                if (!empty($product['image'])) {
                                    $rawImage = str_replace('\\\\', '/', trim((string)$product['image']));
                                    if (stripos($rawImage, 'assets/img/') !== false) {
                                        $assetsPos = stripos($rawImage, 'assets/img/');
                                        $normalizedImage = substr($rawImage, $assetsPos);
                                    } elseif (stripos($rawImage, 'FE/assets/img/') !== false) {
                                        $assetsPos = stripos($rawImage, 'FE/assets/img/');
                                        $normalizedImage = substr($rawImage, $assetsPos + 3);
                                    } elseif (preg_match('/\.(jpg|jpeg|png|webp)$/i', $rawImage)) {
                                        $normalizedImage = 'assets/img/products/' . basename($rawImage);
                                    }
                                }

                                $imageCandidates = [];
                                if (!empty($normalizedImage)) {
                                    $imageCandidates[] = ltrim($normalizedImage, '/');
                                }

                                if (!empty($product['name'])) {
                                    $productName = trim((string)$product['name']);
                                    $imageCandidates[] = 'assets/img/products/' . $productName . '.jpg';
                                    $imageCandidates[] = 'assets/img/products/' . $productName . '.jpeg';
                                    $imageCandidates[] = 'assets/img/products/' . $productName . '.png';
                                    $imageCandidates[] = 'assets/img/products/' . $productName . '.webp';
                                }

                                $imageCandidates[] = $defaultImg;

                                foreach (array_unique($imageCandidates) as $candidate) {
                                    $absoluteCandidate = __DIR__ . '/../../' . ltrim($candidate, '/');
                                    if (file_exists($absoluteCandidate)) {
                                        $imgUrl = ltrim($candidate, '/');
                                        break;
                                    }
                                }
                            ?>
                            <a href="?page=product&id=<?= $product['id'] ?>" class="product-image-link">
                                <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                    alt="<?= htmlspecialchars($product['name']) ?>" 
                                    class="product-img">
                             </a>
                        
                            <div class="product-overlay">
                                <button class="btn-add-cart add-to-cart-btn"
                                        data-product-id="<?= $product['id'] ?>"
                                        data-quantity="1"
                                        title="Thêm vào giỏ">
                                    Thêm Vào Giỏ
                                </button>
                            </div>
                        </div>

                        <div class="product-info">
                            <!-- ✅ THÊM LINK VÀO TÊN SẢN PHẨM -->
                             <h3 class="product-name">
                                <a href="?page=product&id=<?= $product['id'] ?>" class="product-name-link">
                                <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>
        
                             <div class="product-category text-muted small mb-2">
                            <?= htmlspecialchars($product['category']) ?>
                             </div>
        
                            <div class="product-price">
                                <?= number_format($product['price'], 0, ',', '.') ?>đ
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted"><?php echo htmlspecialchars(getSetting('shop.no_products_message', 'No products found.')); ?></p>
                </div>
            <?php endif; ?>

        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-container mt-5 d-flex justify-content-center gap-2">
                <?php
                // Build query string giữ lại TẤT CẢ params trừ page number
                $queryParams = $_GET;
                unset($queryParams['p']);
                $baseQuery = http_build_query($queryParams);
                ?>
                
                <!-- Previous -->
                <?php if ($page > 1): ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $page - 1 ?>" 
                       class="btn btn-light border">
                        <i class="bi bi-chevron-left"></i> Prev
                    </a>
                <?php endif; ?>
                
                <!-- Page Numbers with Smart Range -->
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                // First page + ellipsis
                if ($startPage > 1): ?>
                    <a href="?<?= $baseQuery ?>&p=1" class="btn btn-light border">1</a>
                    <?php if ($startPage > 2): ?>
                        <span class="btn btn-light border disabled">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Range pages -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $i ?>" 
                       class="btn <?= $i == $page ? 'btn-warning text-white fw-bold' : 'btn-light border' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <!-- Last page + ellipsis -->
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="btn btn-light border disabled">...</span>
                    <?php endif; ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $totalPages ?>" class="btn btn-light border">
                        <?= $totalPages ?>
                    </a>
                <?php endif; ?>
                
                <!-- Next -->
                <?php if ($page < $totalPages): ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $page + 1 ?>" 
                       class="btn btn-light border">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<section class="features-bar py-5 bg-light mt-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <i class="bi bi-trophy fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">High Quality</h5>
                <p class="text-muted small">crafted from top materials</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-shield-check fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Warranty Protection</h5>
                <p class="text-muted small">Over 2 years</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-box-seam fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Free Shipping</h5>
                <p class="text-muted small">Order over 150 $</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-headset fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">24 / 7 Support</h5>
                <p class="text-muted small">Dedicated support</p>
            </div>
        </div>
    </div>
</section>
<script src="assets/javascript/filter.js"></script>

<?php include 'views/layouts/footer.php'; ?>
