<?php
// Determine active page
$currentPage = $_GET['page'] ?? 'admin_dashboard';
?>

<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="?page=admin_dashboard"><h2 style="color: white;">CellphoneS</h2></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="<?= $currentPage === 'admin_dashboard' ? 'active' : '' ?>">
                        <a href="?page=admin_dashboard"><i class="ti-dashboard"></i><span>Tổng quan</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_products' ? 'active' : '' ?>">
                        <a href="?page=manage_products"><i class="ti-package"></i><span>Sản phẩm</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_orders' ? 'active' : '' ?>">
                        <a href="?page=manage_orders"><i class="ti-receipt"></i><span>Đơn hàng</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_contacts' ? 'active' : '' ?>">
                        <a href="?page=manage_contacts"><i class="ti-email"></i><span>Liên hệ</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_profile' ? 'active' : '' ?>">
                        <a href="?page=manage_profile"><i class="ti-user"></i><span>Khách hàng</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_reviews' ? 'active' : '' ?>">
                        <a href="?page=manage_reviews"><i class="ti-star"></i><span>Đánh giá</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_posts' ? 'active' : '' ?>">
                        <a href="?page=manage_posts"><i class="ti-write"></i><span>Bài viết</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_qna' ? 'active' : '' ?>">
                        <a href="?page=manage_qna"><i class="ti-help-alt"></i><span>Hỏi & Đáp</span></a>
                    </li>
                    <li class="<?= in_array($currentPage, ['manage_about_info', 'manage_info']) ? 'active' : '' ?>">
                        <a href="javascript:void(0)"><i class="ti-settings"></i><span>Cài đặt</span></a>
                        <ul class="collapse <?= in_array($currentPage, ['manage_about_info', 'manage_info']) ? 'in' : '' ?>">
                            <li class="<?= $currentPage === 'manage_info' ? 'active' : '' ?>"><a href="?page=manage_info">Thông tin trang</a></li>
                            <li class="<?= $currentPage === 'manage_about_info' ? 'active' : '' ?>"><a href="?page=manage_about_info">Trang giới thiệu</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
