<?php
// Load settings helper
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../../helpers/settings_helper.php';
}
include 'views/layouts/header.php';
?>

<main class="home-page-scroll" style="background-color: #f4f6f8; min-height: 100vh; padding-bottom: 20px; overflow-x: hidden;">
    <!-- BREADCRUMB -->
    <div class="container pt-3 pb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="index.php?page=home" class="text-decoration-none text-danger"><i class="bi bi-house-door-fill"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
            </ol>
        </nav>
    </div>

    <section class="container pb-4">
        <div class="row g-4">
            <!-- LEFT COLUMN: Contact Form & Info -->
            <div class="col-lg-8">
                <!-- Promo Banner -->
                <div class="mb-3">
                    <a href="index.php?page=shop"><img src="assets/img/banners/s-edu-2-0-special-desk.gif" class="img-fluid w-100 rounded-3 shadow-sm" alt="Promo Banner"></a>
                </div>

                <div class="bg-white rounded-3 p-4 shadow-sm">
                    <h4 class="fw-bold mb-3 text-danger text-uppercase"><?= htmlspecialchars(getSetting('contact.page_title', 'Liên hệ với chúng tôi')) ?></h4>
                    <p class="text-muted mb-4" style="font-size: 14px;">
                        <?= htmlspecialchars(getSetting('contact.page_subtitle', "Hãy để lại lời nhắn cho chúng tôi nếu bạn cần hỗ trợ hoặc có bất kỳ câu hỏi nào. Đội ngũ nhân viên của chúng tôi sẽ phản hồi trong thời gian sớm nhất.")) ?>
                    </p>

                    <div class="row g-4 mt-1">
                        <div class="col-md-5">
                            <h5 class="fw-bold mb-3" style="font-size: 15px;">Thông tin liên hệ</h5>
                            <div class="d-flex mb-3">
                                <div class="text-danger me-3 fs-5"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 14px;">Địa chỉ</strong>
                                    <span class="text-muted" style="font-size: 13px;"><?= nl2br(htmlspecialchars(getSetting('contact.address', '123 Đường Nguyễn Trãi, Quận 5, TP.HCM'))) ?></span>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="text-danger me-3 fs-5"><i class="bi bi-telephone-fill"></i></div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 14px;">Điện thoại</strong>
                                    <span class="text-muted" style="font-size: 13px;"><?= htmlspecialchars(getSetting('contact.phone', '1800 1234')) ?></span>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="text-danger me-3 fs-5"><i class="bi bi-envelope-fill"></i></div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 14px;">Email</strong>
                                    <span class="text-muted" style="font-size: 13px;"><?= htmlspecialchars(getSetting('contact.email', 'cskh@laptopshop.vn')) ?></span>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="text-danger me-3 fs-5"><i class="bi bi-clock-fill"></i></div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 14px;">Giờ làm việc</strong>
                                    <span class="text-muted" style="font-size: 13px;"><?= nl2br(htmlspecialchars(getSetting('contact.working_hours', 'Thứ 2 - Chủ Nhật: 8:00 - 22:00'))) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <h5 class="fw-bold mb-3" style="font-size: 15px;">Gửi lời nhắn cho chúng tôi</h5>
                            <form id="contactForm" novalidate>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <input type="text" class="form-control" style="font-size: 13px; border-radius: 8px; border: 1px solid #ddd; padding: 10px;" id="contact_name" name="name" placeholder="Họ và tên của bạn *" required minlength="2" />
                                    </div>
                                    <div class="col-6">
                                        <input type="email" class="form-control" style="font-size: 13px; border-radius: 8px; border: 1px solid #ddd; padding: 10px;" id="contact_email" name="email" placeholder="Địa chỉ email *" required />
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <input type="text" class="form-control" style="font-size: 13px; border-radius: 8px; border: 1px solid #ddd; padding: 10px;" id="contact_subject" name="subject" placeholder="Tiêu đề (Không bắt buộc)" />
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" style="font-size: 13px; border-radius: 8px; border: 1px solid #ddd; padding: 10px;" id="contact_message" name="message" rows="4" placeholder="Nội dung lời nhắn *" required minlength="10"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 fw-bold" style="border-radius: 8px; font-size: 14px; padding: 10px;">
                                    <i class="bi bi-send me-1"></i> GỬI LỜI NHẮN
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Sidebar matching Homepage -->
            <div class="col-lg-4">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div class="p-3 bg-white rounded-3 shadow-sm mb-3">
                        <h6 class="fw-bold text-dark text-uppercase mb-2" style="font-size: 13px;">Đăng ký nhận tin khuyến mãi</h6>
                        <p class="text-danger small mb-1 fw-bold">Nhận ngay voucher 10%</p>
                        <p class="text-muted mb-3" style="font-size: 11px;">Voucher sẽ được gửi sau 24h, chỉ áp dụng cho khách hàng mới</p>
                        <form id="newsletterFormContact" onsubmit="return handleNewsletter(event)">
                            <div class="mb-2">
                                <label class="form-label mb-1 text-dark" style="font-size: 12px; font-weight: 600;">Email</label>
                                <input type="email" class="form-control form-control-sm" name="newsletter_email" placeholder="Nhập email của bạn" required style="border-radius: 8px; border: 1px solid #ddd; padding: 8px 12px; font-size: 12px;">
                            </div>
                            <div class="mb-2">
                                <label class="form-label mb-1 text-dark" style="font-size: 12px; font-weight: 600;">Số điện thoại</label>
                                <input type="tel" class="form-control form-control-sm" name="newsletter_phone" placeholder="Nhập số điện thoại của bạn" required style="border-radius: 8px; border: 1px solid #ddd; padding: 8px 12px; font-size: 12px;">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="newsletterAgreeContact" required style="margin-top: 3px;">
                                <label class="form-check-label text-muted" style="font-size: 11px;" for="newsletterAgreeContact">
                                    Tôi đồng ý với điều khoản của LaptopShop
                                </label>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 fw-bold" style="border-radius: 20px; padding: 8px; font-size: 13px;">ĐĂNG KÝ NGAY</button>
                        </form>
                    </div>

                    <div class="flex-grow-1 d-none d-lg-block" style="border-radius: 12px; overflow: hidden; background: #e0f2f1; min-height: 200px;">
                        <a href="index.php?page=shop" class="d-block h-100">
                            <img src="assets/img/banners/xiaomi-redmi-pad-2-9-7-inch-home-0526.webp" class="img-fluid w-100 h-100" alt="Banner Right" style="object-fit: cover; border-radius: 12px;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

  <?php include 'views/layouts/footer.php'; ?>

<!-- Script to send user contact info to ContactController -->
  <script src="assets/javascript/send_contact.js"></script>
