<?php 
require_once 'helpers/settings_helper.php';
require_once 'models/ProductModel.php';
$productModel = new ProductModel();
$featuredProducts = $productModel->getAllProducts(8, 0, 'default');
include 'views/layouts/header.php'; 

// Fetch Home Settings
$heroTitle = getSetting('home.hero_title', 'Laptop chính hãng');
$heroSubtitle = getSetting('home.hero_subtitle', 'Giá tốt nhất thị trường');
$heroButton = getSetting('home.hero_button_text', 'Mua ngay');

// Banners
$banners = [];
for ($i = 1; $i <= 4; $i++) {
    $img = getSetting("home.banner_{$i}_image");
    if (!$img) {
        // Fallback to defaults
        $defaults = [
            1 => 'assets/img/banners/s26-home-0526.webp',
            2 => 'assets/img/banners/iphone-17-pro-max_home_05_2026.webp',
            3 => 'assets/img/banners/Oppo find x9 ultra_pre_home_1.webp',
            4 => 'assets/img/banners/poco-pad-m1-home-1.webp'
        ];
        $img = $defaults[$i];
    }
    $banners[] = [
        'src' => getImageUrl($img),
        'link' => getSetting("home.banner_{$i}_link", 'index.php?page=shop')
    ];
}

$subBanners = [];
for ($i = 1; $i <= 3; $i++) {
    $img = getSetting("home.sub_banner_{$i}_image");
    if (!$img) {
        $defaults = [
            1 => 'assets/img/banners/mbannnmacpro.webp',
            2 => 'assets/img/banners/a-17.webp',
            3 => 'assets/img/banners/macbook-giao-xa-2026.webp'
        ];
        $img = $defaults[$i];
    }
    $subBanners[] = [
        'src' => getImageUrl($img),
        'link' => getSetting("home.sub_banner_{$i}_link", 'index.php?page=shop')
    ];
}

$rightSidebarImg = getSetting('home.right_sidebar_image', 'assets/img/banners/lenovo_home.webp');
$rightSidebarLink = getSetting('home.right_sidebar_link', 'index.php?page=shop');

$promoStripImg = getSetting('home.promo_strip_image', 'assets/img/banners/s-edu-2-0-special-desk.gif');
$promoStripLink = getSetting('home.promo_strip_link', 'index.php?page=shop');

$featuredSidebarImg = getSetting('home.featured_sidebar_image', 'assets/img/banners/01KK84Q078JE7HEGK1SF3GGZGZ.webp');
$featuredSidebarLink = getSetting('home.featured_sidebar_link', 'index.php?page=shop');

$banner1Img = getSetting('home.banner_1_image', 'assets/img/banner-1.JPG');
$banner2Img = getSetting('home.banner_2_image', 'assets/img/banner-2.jpg');

$featuredTitle = 'Laptop nổi bật';
?>

<style>
.home-hero-wrapper { height: 460px; }
.sub-banners-row { height: 110px; }
.featured-sticker-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}
.featured-sticker-card {
    display: block;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.featured-sticker-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
}
.featured-sticker-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.promo-strip-banner {
    width: 100%;
    height: auto;
    object-fit: contain;
    background: #fff;
    display: block;
}

/* Hide scrollbar for horizontal scrolling */
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@media (max-width: 991.98px) {
    .home-hero-wrapper { height: auto !important; flex-wrap: wrap !important; }
    .home-hero-main { flex: 0 0 100% !important; max-width: 100% !important; }
    
    .right-col { display: block !important; flex: 0 0 100% !important; max-width: 100% !important; margin-top: 0.5rem; }
    .right-col .flex-grow-1 { display: none; }
    
    #heroSlider { height: 250px !important; }
    
    .sub-banners-row { 
        flex-wrap: nowrap !important; 
        overflow-x: auto; 
        height: auto !important;
        padding-bottom: 5px;
    }
    .sub-banners-row .col-4 { 
        flex: 0 0 45% !important; 
        max-width: 45% !important; 
        height: 80px; 
    }
}

@media (max-width: 575.98px) {
    #heroSlider { height: 180px !important; }
    .sub-banners-row .col-4 { 
        flex: 0 0 75% !important; 
        max-width: 75% !important; 
        height: 90px; 
    }
    .featured-sticker-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
}
</style>

<main class="home-page-scroll" style="background-color: #f4f6f8; min-height: 100vh; padding-bottom: 20px; overflow-x: hidden;">
    <section class="home-hero container pt-4 pb-4">
        <div class="d-flex gap-3 home-hero-wrapper" style="flex-wrap: nowrap;">
            <aside class="d-none d-lg-block left-col" style="flex: 0 0 19.5%; max-width: 19.5%;">
                <div class="h-100 shadow-bottom-50 flex flex-col rounded-3 overflow-hidden bg-white py-2 text-neutral-800 sidebar-card categories" style="border-radius: 12px;">
                    <?php 
                    $leftCats = [
                        ['src' => 'assets/img/icons/icon-homepage-mobile.svg', 'parts' => [['text'=>'Điện thoại', 'href'=>'index.php?page=shop'], ['text'=>'Tablet', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-laptop.svg', 'parts' => [['text'=>'Laptop', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-audio-2.svg', 'parts' => [['text'=>'Âm thanh', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-watch.svg', 'parts' => [['text'=>'Đồng hồ', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-home-appliances.svg', 'parts' => [['text'=>'Đồ gia dụng', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-accessories.svg', 'parts' => [['text'=>'Phụ kiện', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-pc.svg', 'parts' => [['text'=>'PC', 'href'=>'index.php?page=shop'], ['text'=>'Màn hình', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-tv.svg', 'parts' => [['text'=>'Tivi', 'href'=>'index.php?page=shop']]],
                        ['src' => 'assets/img/icons/icon-homepage-trade-in.svg', 'parts' => [['text'=>'Thu cũ đổi mới', 'href'=>'#']]],
                        ['src' => 'assets/img/icons/icon-homepage-used-goods.svg', 'parts' => [['text'=>'Hàng cũ', 'href'=>'#']]],
                        ['src' => 'assets/img/icons/icon-homepage-promotions.svg', 'parts' => [['text'=>'Khuyến mãi', 'href'=>'#']]],
                        ['src' => 'assets/img/icons/icon-homepage-tech-news.svg', 'parts' => [['text'=>'Tin công nghệ', 'href'=>'#']]],
                        ['src' => 'assets/img/qna.svg', 'parts' => [['text'=>'Hỏi đáp', 'href'=>'http://localhost//LAPTOP_WEB_DEV-main/index.php?page=qna']]]
                    ];
                    foreach ($leftCats as $c): ?>
                    <div class="group flex h-10 cursor-pointer items-center px-3 hover:bg-neutral-100 d-flex align-items-center category-item" onclick="if(event.target.tagName !== 'A') window.location.href='index.php?page=shop'">
                        <img alt="Category" loading="lazy" width="28" height="28" decoding="async" class="me-3" src="<?= $c['src'] ?>" />
                        <div class="d-flex align-items-center parts-wrapper w-100">
                            <span class="text-truncate d-block w-100 fw-bold" style="font-size: 12px;">
                                <?php 
                                $total = count($c['parts']);
                                foreach ($c['parts'] as $i => $p): ?>
                                    <a class="hover-text-primary text-dark text-decoration-none fw-bold" href="<?= $p['href'] ?>"><?= $p['text'] ?></a><?= $i < $total - 1 ? ', ' : '' ?>
                                <?php endforeach; ?>
                            </span>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-secondary" style="font-size: 12px;"></i>
                    </div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <!-- CENTER: Banner Slider -->
            <main class="d-flex flex-column h-100 justify-content-between home-hero-main" style="flex: 1 1 0; min-width: 0;">
                <div id="heroSlider" class="carousel slide flex-grow-1" data-bs-ride="carousel" data-bs-interval="4000" style="border-radius: 12px; overflow: hidden; background: #fff; margin-bottom: 8px;">
                    <div class="carousel-indicators">
                        <?php foreach ($banners as $index => $banner): ?>
                            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner h-100">
                        <?php foreach ($banners as $index => $banner): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100">
                                <a href="<?= htmlspecialchars($banner['link']) ?>" class="d-block h-100">
                                    <img src="<?= htmlspecialchars($banner['src']) ?>" class="d-block w-100 h-100" style="object-fit: contain; background: #fff;">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>

                <!-- 3 Sub-Banners -->
                <div class="row small-cards g-2 sub-banners-row hide-scrollbar">
                    <?php foreach ($subBanners as $banner): ?>
                        <div class="col-md-4 col-4">
                            <a href="<?= htmlspecialchars($banner['link']) ?>">
                                <img src="<?= htmlspecialchars($banner['src']) ?>" class="img-fluid rounded-3 w-100 h-100" style="object-fit: contain; background: #fff;">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>

            <!-- RIGHT SIDEBAR -->
            <aside class="d-none d-lg-block right-col" style="flex: 0 0 19.5%; max-width: 19.5%;">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div class="right-card p-3 bg-white rounded-3 shadow-sm mb-2">
                        <h6 class="fw-bold text-dark text-uppercase mb-2" style="font-size: 13px;">Đăng ký nhận tin khuyến mãi</h6>
                        <p class="text-danger small mb-1 fw-bold">Nhận ngay voucher 10%</p>
                        <p class="text-muted mb-3" style="font-size: 11px;">Voucher sẽ được gửi sau 24h, chỉ áp dụng cho khách hàng mới</p>
                        <form id="newsletterForm" onsubmit="return handleNewsletter(event)">
                            <div class="mb-2">
                                <label class="form-label mb-1 text-dark" style="font-size: 12px; font-weight: 600;">Email</label>
                                <input type="email" class="form-control form-control-sm" name="newsletter_email" placeholder="Nhập email của bạn" required style="border-radius: 8px; border: 1px solid #ddd; padding: 8px 12px; font-size: 12px;">
                            </div>
                            <div class="mb-2">
                                <label class="form-label mb-1 text-dark" style="font-size: 12px; font-weight: 600;">Số điện thoại</label>
                                <input type="tel" class="form-control form-control-sm" name="newsletter_phone" placeholder="Nhập số điện thoại của bạn" required style="border-radius: 8px; border: 1px solid #ddd; padding: 8px 12px; font-size: 12px;">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="newsletterAgree" required style="margin-top: 3px;">
                                <label class="form-check-label text-muted" style="font-size: 11px;" for="newsletterAgree">
                                    Tôi đồng ý với điều khoản của <?= htmlspecialchars($siteName) ?>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 fw-bold" style="border-radius: 20px; padding: 8px; font-size: 13px;">ĐĂNG KÝ NGAY</button>
                        </form>
                        <div id="newsletterMsg" class="mt-2 text-center small" style="display: none;"></div>
                    </div>
                    <div class="flex-grow-1" style="border-radius: 12px; overflow: hidden; background: #e0f2f1;">
                        <a href="<?= htmlspecialchars($rightSidebarLink) ?>" class="d-block h-100">
                            <img src="<?= htmlspecialchars(getImageUrl($rightSidebarImg)) ?>" class="img-fluid w-100 h-100" style="object-fit: contain; background: #e0f2f1;">
                        </a>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Promo Strip -->
        <div class="mt-4">
            <a href="<?= htmlspecialchars($promoStripLink) ?>">
                <img src="<?= htmlspecialchars(getImageUrl($promoStripImg)) ?>" class="promo-strip-banner rounded-3" alt="Promo">
            </a>
        </div>
    </section>

    <!-- FEATURED PRODUCTS SECTION -->
    <section class="container pb-4">
        <div class="row gx-4">
            <div class="col-lg-3 d-none d-lg-block">
                <a href="<?= htmlspecialchars($featuredSidebarLink) ?>">
                    <img src="<?= htmlspecialchars(getImageUrl($featuredSidebarImg)) ?>" class="img-fluid rounded-3 w-100 h-100" style="object-fit: cover; min-height: 380px;">
                </a>
            </div>
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-fire text-danger me-2"></i><?= htmlspecialchars($featuredTitle) ?></h5>
                    <a href="index.php?page=shop" class="text-danger text-decoration-none small fw-bold">Xem tất cả <i class="bi bi-chevron-right"></i></a>
                </div>
                <!-- ... existing product grid logic ... -->
                <div class="row g-3">
                    <?php foreach ($featuredProducts as $product): 
                        $imgSrc = !empty($product['image']) ? $product['image'] : 'assets/img/placeholder.png';
                        $price = isset($product['price']) ? number_format($product['price'], 0, ',', '.') : '0';
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-4 col-6">
                        <a href="index.php?page=product&id=<?= $product['id'] ?>" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                                <div class="p-3 text-center" style="background: #fafafa;">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid" style="height: 140px; object-fit: contain;" onerror="this.src='assets/img/placeholder.png'">
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="card-title text-dark mb-2" style="font-size: 13px; line-height: 1.4; height: 36px; overflow: hidden;"><?= htmlspecialchars($product['name']) ?></h6>
                                    <div class="text-danger fw-bold" style="font-size: 15px;"><?= $price ?>đ</div>
                                    <?php if (!empty($product['brand'])): ?>
                                    <div class="text-muted small mt-1"><?= htmlspecialchars($product['brand']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    $featuredStickers = [
        ['src' => $banner1Img, 'link' => 'index.php?page=shop', 'alt' => 'Banner 1'],
        ['src' => $banner2Img, 'link' => 'index.php?page=shop', 'alt' => 'Banner 2'],
        ['src' => $promoStripImg, 'link' => $promoStripLink, 'alt' => 'Dải banner ngang'],
        ['src' => $featuredSidebarImg, 'link' => $featuredSidebarLink, 'alt' => 'Banner nổi bật']
    ];
    ?>

    <section class="container pb-4">
        <div class="featured-sticker-grid">
            <?php foreach ($featuredStickers as $sticker): ?>
                <a href="<?= htmlspecialchars($sticker['link']) ?>" class="featured-sticker-card" aria-label="<?= htmlspecialchars($sticker['alt']) ?>">
                    <img src="<?= htmlspecialchars(getImageUrl($sticker['src'])) ?>" alt="<?= htmlspecialchars($sticker['alt']) ?>">
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'views/layouts/footer.php'; ?>
