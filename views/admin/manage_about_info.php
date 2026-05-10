<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <h2><i class="bi bi-info-circle"></i> Nội dung trang Giới thiệu</h2>
                <p>Quản lý thông tin và nội dung hiển thị trên trang Giới thiệu công ty.</p>
            </div>

            <div class="content-card">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Chỉnh sửa nội dung</h3>
                    </div>
                    <div class="card-body">
                            <form id="form-about" data-group="about">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiêu đề trang</label>
                                    <input type="text" class="form-control" name="page_title" id="about_page_title">
                                    <small class="text-muted">Tiêu đề chính hiển thị ở đầu trang Giới thiệu</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiêu đề phụ Hero</label>
                                    <input type="text" class="form-control" name="hero_subtitle" id="about_hero_subtitle">
                                    <small class="text-muted">Dòng mô tả ngắn dưới tiêu đề chính</small>
                                </div>

                                <hr class="my-4">
                                <h4 class="mb-3">Phần Giới thiệu</h4>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiêu đề giới thiệu</label>
                                    <input type="text" class="form-control" name="intro_title" id="about_intro_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nội dung giới thiệu</label>
                                    <textarea class="form-control" name="intro" id="about_intro" rows="4"></textarea>
                                    <small class="text-muted">Giới thiệu tổng quan về công ty</small>
                                </div>

                                <hr class="my-4">
                                <h4 class="mb-3">Sứ mệnh & Tầm nhìn</h4>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiêu đề Sứ mệnh</label>
                                    <input type="text" class="form-control" name="mission_title" id="about_mission_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nội dung Sứ mệnh</label>
                                    <textarea class="form-control" name="mission" id="about_mission" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiêu đề Tầm nhìn</label>
                                    <input type="text" class="form-control" name="vision_title" id="about_vision_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nội dung Tầm nhìn</label>
                                    <textarea class="form-control" name="vision" id="about_vision" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tiêu đề Giá trị cốt lõi</label>
                                    <input type="text" class="form-control" name="values_title" id="about_values_title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nội dung Giá trị cốt lõi</label>
                                    <textarea class="form-control" name="values" id="about_values" rows="4"></textarea>
                                    <small class="text-muted">Các giá trị cốt lõi của công ty</small>
                                </div>

                                <hr class="my-4">
                                <h4 class="mb-3">Thống kê (Số liệu)</h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng Khách hàng</label>
                                            <input type="number" class="form-control" name="stats_customers" id="about_stats_customers">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nhãn Khách hàng</label>
                                            <input type="text" class="form-control" name="stats_customers_label" id="about_stats_customers_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng Sản phẩm</label>
                                            <input type="number" class="form-control" name="stats_products" id="about_stats_products">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nhãn Sản phẩm</label>
                                            <input type="text" class="form-control" name="stats_products_label" id="about_stats_products_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số năm kinh nghiệm</label>
                                            <input type="number" class="form-control" name="stats_years" id="about_stats_years">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nhãn Năm</label>
                                            <input type="text" class="form-control" name="stats_years_label" id="about_stats_years_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng Đánh giá</label>
                                            <input type="number" class="form-control" name="stats_reviews" id="about_stats_reviews">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nhãn Đánh giá</label>
                                            <input type="text" class="form-control" name="stats_reviews_label" id="about_stats_reviews_label">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-footer mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-floppy me-2"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
</div>

<script src="assets/javascript/admin_manage_info.js"></script>

<?php include 'views/layouts/admin_footer.php'; ?>