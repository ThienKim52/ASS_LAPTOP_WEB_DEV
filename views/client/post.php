<?php
require_once 'helpers/settings_helper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tin Tức & Bài Viết - <?php echo htmlspecialchars(getSetting('general.site_name', 'PhoneStore')); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --text-muted: #6c757d;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .article-hero {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .search-container {
            max-width: 600px;
            margin: 20px auto 0;
        }
        .search-input-group {
            background: white;
            padding: 5px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .search-input-group input {
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            box-shadow: none !important;
        }
        .search-input-group .btn {
            border-radius: 50px;
            padding: 10px 25px;
        }
        .article-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .article-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        .article-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .article-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(13, 110, 253, 0.9);
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .article-body { padding: 20px; }
        .article-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 3.5rem;
        }
        .article-title a { color: #212529; text-decoration: none; transition: color 0.2s; }
        .article-title a:hover { color: var(--primary-color); }
        .article-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 15px;
        }
        .article-excerpt {
            font-size: 0.95rem;
            color: #495057;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 20px;
            height: 4.2rem;
        }
        .btn-read-more {
            font-weight: 600;
            padding: 0;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .btn-read-more i { transition: transform 0.2s; }
        .btn-read-more:hover i { transform: translateX(5px); }
        
        .skeleton {
            background: #eee;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            border-radius: 5px;
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }
        @keyframes shine { to { background-position-x: -200%; } }
        .skeleton-card { height: 400px; }
    </style>
</head>
<body>
    <?php include 'views/layouts/header.php'; ?>
    
    <section class="article-hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Tin Tức & Cập Nhật</h1>
            <p class="lead opacity-75">Cập nhật xu hướng công nghệ và đánh giá sản phẩm mới nhất</p>
            
            <div class="search-container">
                <form id="searchForm" class="search-input-group d-flex">
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="form-control" 
                        placeholder="Tìm kiếm bài viết theo từ khóa..." 
                        aria-label="Search"
                    />
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-search me-2"></i>Tìm kiếm
                    </button>
                </form>
            </div>
        </div>
    </section>
    
    <div class="container mb-5">
        <div id="searchInfo" class="mb-4 d-none">
            <div class="d-flex justify-content-between align-items-center">
                <p class="text-muted mb-0" id="resultCount"></p>
                <button class="btn btn-sm btn-outline-secondary" id="clearSearch">
                    <i class="bi bi-x-circle me-1"></i>Xóa tìm kiếm
                </button>
            </div>
        </div>

        <div id="postsContainer" class="row g-4">
            <!-- Posts loaded via AJAX -->
            <?php for($i=0; $i<6; $i++): ?>
            <div class="col-lg-4 col-md-6 article-skeleton">
                <div class="article-card">
                    <div class="skeleton article-img-wrapper" style="height: 200px;"></div>
                    <div class="article-body">
                        <div class="skeleton mb-3" style="height: 25px; width: 80%;"></div>
                        <div class="skeleton mb-2" style="height: 15px; width: 100%;"></div>
                        <div class="skeleton mb-2" style="height: 15px; width: 100%;"></div>
                        <div class="skeleton" style="height: 15px; width: 60%;"></div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
        
        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-5 d-none" id="paginationNav">
            <ul class="pagination justify-content-center" id="paginationList">
                <!-- Pagination loaded via AJAX -->
            </ul>
        </nav>

        <!-- No Results -->
        <div id="noResults" class="text-center py-5 d-none">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3">Không tìm thấy bài viết nào</h3>
            <p class="text-muted">Thử thay đổi từ khóa tìm kiếm của bạn.</p>
            <button class="btn btn-primary mt-2" onclick="location.reload()">Xem tất cả bài viết</button>
        </div>
    </div>
    
    <?php include 'views/layouts/footer.php'; ?>
    
    <script>
        window.POST_API_URL = 'ajax/get_posts.php';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/javascript/post.js"></script>
</body>
</html>