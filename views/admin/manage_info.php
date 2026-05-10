<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">
    <div class="content-header mb-4">
        <h2><i class="bi bi-gear"></i> Quản lý thông tin trang</h2>
        <p>Cấu hình các nội dung hiển thị trên website như logo, số điện thoại, banner quảng cáo...</p>
    </div>
    
    <div class="content-card">
        <div class="container-xl">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" id="tab-general-btn" data-bs-toggle="tab" data-bs-target="#tab-general" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i>Tổng quan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="tab-header-btn" data-bs-toggle="tab" data-bs-target="#tab-header" type="button" role="tab">
                                <i class="bi bi-layout-header me-1"></i>Đầu trang (Header)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="tab-home-btn" data-bs-toggle="tab" data-bs-target="#tab-home" type="button" role="tab">
                                <i class="bi bi-house me-1"></i>Trang chủ (Banners)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="tab-contact-btn" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button" role="tab">
                                <i class="bi bi-telephone me-1"></i>Trang liên hệ
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="tab-footer-btn" data-bs-toggle="tab" data-bs-target="#tab-footer" type="button" role="tab">
                                <i class="bi bi-layout-footer me-1"></i>Chân trang (Footer)
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- Tab Tổng quan -->
                        <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                            <form id="form-general" data-group="general">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Tên Website</label>
                                    <input type="text" class="form-control" name="site_name" id="general_site_name">
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Tên này sẽ hiện trên tiêu đề tab của trình duyệt.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Logo chính</label>
                                    <input type="file" class="form-control mb-2" id="general_site_logo_file" accept="image/*">
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Logo hiển thị ở góc trên bên trái và dưới chân trang. Nên dùng file .png trong suốt.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Mô tả ngắn (SEO)</label>
                                    <textarea class="form-control" name="site_description" id="general_site_description" rows="2"></textarea>
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Giúp bộ máy tìm kiếm (Google) hiểu về trang web của bạn.</small>
                                </div>
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Lưu tổng quan</button>
                            </form>
                        </div>

                        <!-- Tab Header -->
                        <div class="tab-pane fade" id="tab-header" role="tabpanel">
                            <form id="form-header" data-group="header">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Hiển thị thanh thông báo đỏ</label>
                                    <select class="form-select" name="announcement_bar_enabled" id="header_announcement_bar_enabled">
                                        <option value="1">Hiển thị</option>
                                        <option value="0">Ẩn</option>
                                    </select>
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Bật/Tắt thanh chữ chạy màu đỏ ở trên cùng.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Nội dung chữ chạy</label>
                                    <input type="text" class="form-control" name="announcement_bar_text" id="header_announcement_bar_text">
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Nội dung khuyến mãi chạy ngang màn hình. Bỏ trống để dùng mặc định.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Số điện thoại Header</label>
                                    <input type="text" class="form-control" name="phone_number" id="header_phone_number">
                                    <small class="text-muted"><i class="bi bi-info-circle"></i> Số điện thoại hiển thị cạnh ô tìm kiếm (ví dụ: 1800 2097).</small>
                                </div>
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Lưu Header</button>
                            </form>
                        </div>

                        <!-- Tab Trang chủ -->
                        <div class="tab-pane fade" id="tab-home" role="tabpanel">
                            <form id="form-home" data-group="home">
                                <h5 class="mb-3 text-primary border-bottom pb-2">Carousel Banners (4 ảnh chạy slide chính)</h5>
                                <div class="row">
                                    <?php for($i=1; $i<=4; $i++): ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Banner <?= $i ?></label>
                                        <input type="file" class="form-control mb-1" id="home_banner_<?= $i ?>_image_file" accept="image/*">
                                        <input type="text" class="form-control" name="banner_<?= $i ?>_link" id="home_banner_<?= $i ?>_link" placeholder="Link khi click (ví dụ: ?page=shop)">
                                    </div>
                                    <?php endfor; ?>
                                </div>

                                <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">Sub Banners (3 ảnh nhỏ dưới slide)</h5>
                                <div class="row">
                                    <?php for($i=1; $i<=3; $i++): ?>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Ảnh nhỏ <?= $i ?></label>
                                        <input type="file" class="form-control mb-1" id="home_sub_banner_<?= $i ?>_image_file" accept="image/*">
                                        <input type="text" class="form-control" name="sub_banner_<?= $i ?>_link" id="home_sub_banner_<?= $i ?>_link" placeholder="Link">
                                    </div>
                                    <?php endfor; ?>
                                </div>

                                <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">Các Banner Khác</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Banner Sidebar Phải</label>
                                        <input type="file" class="form-control mb-1" id="home_right_sidebar_image_file" accept="image/*">
                                        <input type="text" class="form-control" name="right_sidebar_link" id="home_right_sidebar_link" placeholder="Link">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Dải Banner ngang (Promo Strip)</label>
                                        <input type="file" class="form-control mb-1" id="home_promo_strip_image_file" accept="image/*">
                                        <input type="text" class="form-control" name="promo_strip_link" id="home_promo_strip_link" placeholder="Link">
                                        <img id="home_promo_strip_image_preview" class="img-fluid rounded mt-2 border" alt="Promo Strip Preview" style="max-height: 120px; object-fit: cover; display: none;">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Banner dọc phần Nổi bật (Featured Sidebar)</label>
                                        <input type="file" class="form-control mb-1" id="home_featured_sidebar_image_file" accept="image/*">
                                        <input type="text" class="form-control" name="featured_sidebar_link" id="home_featured_sidebar_link" placeholder="Link">
                                        <small class="text-muted"><i class="bi bi-info-circle"></i> Đây là banner dọc nằm bên trái phần sản phẩm Laptop nổi bật.</small>
                                        <img id="home_featured_sidebar_image_preview" class="img-fluid rounded mt-2 border" alt="Featured Sidebar Preview" style="max-height: 180px; object-fit: cover; display: none;">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary px-4 mt-3"><i class="bi bi-save me-1"></i> Lưu Banner</button>
                            </form>
                        </div>

                        <!-- Tab Trang Liên hệ -->
                        <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                            <form id="form-contact" data-group="contact">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Tiêu đề chính</label>
                                    <input type="text" class="form-control" name="page_title" id="contact_page_title">
                                    <small class="text-muted">Ví dụ: LIÊN HỆ VỚI CHÚNG TÔI</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Tiêu đề phụ (Mô tả ngắn)</label>
                                    <textarea class="form-control" name="page_subtitle" id="contact_page_subtitle" rows="2"></textarea>
                                    <small class="text-muted">Mô tả ngắn về trang liên hệ</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Địa chỉ văn phòng</label>
                                    <textarea class="form-control" name="address" id="contact_address" rows="2"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Hotline hiển thị</label>
                                        <input type="text" class="form-control" name="phone" id="contact_phone">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Email hỗ trợ</label>
                                        <input type="email" class="form-control" name="email" id="contact_email">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Giờ làm việc</label>
                                    <textarea class="form-control" name="working_hours" id="contact_working_hours" rows="3"></textarea>
                                    <small class="text-muted">Mỗi dòng một khung giờ (ví dụ: Thứ 2 - Chủ Nhật: 8:00 - 22:00)</small>
                                </div>
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Lưu trang liên hệ</button>
                            </form>
                        </div>

                        <!-- Tab Footer -->
                        <div class="tab-pane fade" id="tab-footer" role="tabpanel">
                            <form id="form-footer" data-group="footer">
                                <h5 class="mb-3 text-danger border-bottom pb-2">Hotline hỗ trợ miễn phí (Chân trang)</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Gọi mua hàng</label>
                                        <input type="text" class="form-control" name="phone_sales" id="footer_phone_sales">
                                        <small class="text-muted">7h30 - 22h00</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Gọi khiếu nại</label>
                                        <input type="text" class="form-control" name="phone_complaints" id="footer_phone_complaints">
                                        <small class="text-muted">8h00 - 21h30</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Gọi bảo hành</label>
                                        <input type="text" class="form-control" name="phone_warranty" id="footer_phone_warranty">
                                        <small class="text-muted">8h00 - 21h00</small>
                                    </div>
                                </div>

                                <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">Thông tin công ty & Mạng xã hội</h5>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Giới thiệu ngắn (Chân trang)</label>
                                    <textarea class="form-control" name="about_text" id="footer_about_text" rows="3"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Link Facebook</label>
                                        <input type="url" class="form-control" name="social_facebook" id="footer_social_facebook">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Link Instagram</label>
                                        <input type="url" class="form-control" name="social_instagram" id="footer_social_instagram">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Link Twitter (X)</label>
                                        <input type="url" class="form-control" name="social_twitter" id="footer_social_twitter">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary px-4 mt-3"><i class="bi bi-save me-1"></i> Lưu chân trang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.BASE_PATH = '<?php echo dirname($_SERVER['PHP_SELF']) === '/' ? '' : dirname($_SERVER['PHP_SELF']); ?>';
</script>
<script src="assets/javascript/admin_manage_info.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>