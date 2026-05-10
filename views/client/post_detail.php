<?php
// views/client/post_detail.php
require_once 'helpers/settings_helper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo htmlspecialchars(getSetting('general.site_name', 'PhoneStore')); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .article-header { padding: 60px 0 40px; background: white; border-bottom: 1px solid #eee; }
        .article-title { font-size: 2.5rem; font-weight: 800; line-height: 1.2; margin-bottom: 20px; }
        .article-meta { color: #6c757d; font-size: 0.95rem; display: flex; align-items: center; gap: 20px; }
        .article-thumbnail { width: 100%; max-height: 500px; object-fit: cover; border-radius: 20px; margin: 40px 0; }
        .article-content { font-size: 1.1rem; line-height: 1.8; color: #333; }
        .article-content p { margin-bottom: 25px; }
        .article-content img { max-width: 100%; border-radius: 15px; margin: 20px 0; }
        
        .comment-section { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-top: 60px; }
        .comment-form textarea { border-radius: 15px; padding: 15px; border: 1px solid #eee; background: #fdfdfd; }
        .comment-form textarea:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.1); }
        
        .comment-item { padding: 25px 0; border-bottom: 1px solid #f1f1f1; }
        .comment-item:last-child { border-bottom: none; }
        .comment-avatar { width: 45px; height: 45px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #999; }
        .comment-author { font-weight: 700; font-size: 1rem; margin-bottom: 2px; }
        .comment-date { font-size: 0.85rem; color: #adb5bd; }
        .comment-text { margin-top: 10px; color: #495057; line-height: 1.6; }
        .comment-actions { margin-top: 10px; display: flex; gap: 15px; font-size: 0.85rem; }
        .comment-actions a { color: #6c757d; text-decoration: none; cursor: pointer; }
        .comment-actions a:hover { color: #0d6efd; }
        
        .sidebar-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .related-item { display: flex; gap: 15px; margin-bottom: 20px; text-decoration: none; color: inherit; transition: opacity 0.2s; }
        .related-item:hover { opacity: 0.8; }
        .related-img { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .related-title { font-weight: 600; font-size: 0.95rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body>
    <?php include 'views/layouts/header.php'; ?>
    
    <header class="article-header">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="?page=post">Tin tức</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bài viết</li>
                </ol>
            </nav>
            <h1 class="article-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="article-meta">
                <span><i class="bi bi-person me-2"></i><?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></span>
                <span><i class="bi bi-calendar3 me-2"></i><?php echo date('d/m/Y', strtotime($post['published_at'] ?? $post['created_at'])); ?></span>
                <span><i class="bi bi-eye me-2"></i><?php echo number_format($post['view_count'] ?? 0); ?> lượt xem</span>
                <span><i class="bi bi-chat-dots me-2"></i><span id="commentCount">0</span> bình luận</span>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <article>
                    <?php if(!empty($post['thumbnail_url'])): ?>
                        <img src="<?php echo htmlspecialchars($post['thumbnail_url']); ?>" class="article-thumbnail" alt="<?php echo htmlspecialchars($post['title']); ?>" onerror="this.onerror=null; this.src='assets/img/placeholder.png'">
                    <?php endif; ?>
                    
                    <div class="article-content">
                        <?php echo $post['content']; ?>
                    </div>
                </article>

                <!-- Share -->
                <div class="d-flex align-items-center gap-3 py-4 border-top border-bottom my-5">
                    <span class="fw-bold">Chia sẻ bài viết:</span>
                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-info btn-sm rounded-circle text-info"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-link-45deg"></i></a>
                </div>

                <!-- Comments -->
                <div class="comment-section">
                    <h3 class="mb-4 fw-bold">Bình luận (<span id="commentCount2">0</span>)</h3>
                    
                    <div class="comment-form mb-5">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form id="commentForm">
                                <input type="hidden" name="article_id" value="<?php echo $post['id']; ?>">
                                <div class="mb-3">
                                    <textarea name="content" class="form-control" rows="4" placeholder="Chia sẻ suy nghĩ của bạn về bài viết này..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                        <i class="bi bi-send me-2"></i>Gửi bình luận
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-light border text-center py-4 rounded-4">
                                <p class="mb-3">Vui lòng đăng nhập để gửi bình luận.</p>
                                <a href="?page=login_signup" class="btn btn-primary px-4">Đăng nhập ngay</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="commentsList">
                        <!-- Loaded via AJAX -->
                    </div>
                    
                    <div id="commentsPagination" class="mt-4"></div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-card">
                    <h5 class="fw-bold mb-4">Bài viết liên quan</h5>
                    <div id="relatedArticles">
                        <?php if (!empty($relatedPosts)): ?>
                            <?php foreach($relatedPosts as $r): ?>
                                <a href="?page=post_detail&id=<?php echo $r['id']; ?>" class="related-item">
                                    <img src="<?php echo !empty($r['thumbnail_url']) ? htmlspecialchars($r['thumbnail_url']) : 'assets/img/placeholder.png'; ?>" class="related-img" alt="" onerror="this.onerror=null; this.src='assets/img/placeholder.png'">
                                    <div>
                                        <div class="related-title"><?php echo htmlspecialchars($r['title']); ?></div>
                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($r['published_at'] ?? $r['created_at'])); ?></small>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small">Không có bài viết liên quan.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h5 class="fw-bold mb-4">Tìm kiếm</h5>
                    <form action="index.php" method="GET">
                        <input type="hidden" name="page" value="post">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control border-end-0" placeholder="Tìm bài viết...">
                            <button class="btn btn-outline-primary border-start-0" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Báo cáo bình luận</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        <input type="hidden" name="comment_id" id="reportCommentId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do báo cáo</label>
                            <select name="reason" class="form-select" required>
                                <option value="spam">Spam / Quảng cáo</option>
                                <option value="inappropriate">Nội dung không phù hợp</option>
                                <option value="offensive">Xúc phạm / Công kích</option>
                                <option value="other">Lý do khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô tả thêm (tùy chọn)</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger px-4">Gửi báo cáo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'views/layouts/footer.php'; ?>
    
    <script>
        window.ARTICLE_ID = <?php echo $post['id']; ?>;
        window.CURRENT_USER_ID = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/javascript/post_detail.js"></script>
</body>
</html>
