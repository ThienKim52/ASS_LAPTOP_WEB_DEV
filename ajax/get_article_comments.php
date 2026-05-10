<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/ArticleCommentModel.php';

$articleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if (!$articleId) {
    echo json_encode(['items' => [], 'total' => 0]);
    exit;
}

$commentModel = new ArticleCommentModel();
$comments = $commentModel->getByArticle($articleId, $page);

// Count total (for simplicity, just return total count in this call)
$sql = "SELECT COUNT(*) FROM article_comments WHERE article_id = ? AND status = 'approved' AND is_hidden = 0";
$db = Database::getConnection();
$stmt = $db->prepare($sql);
$stmt->execute([$articleId]);
$total = (int)$stmt->fetchColumn();

echo json_encode([
    'items' => $comments,
    'total' => $total,
    'page' => $page
]);
?>
