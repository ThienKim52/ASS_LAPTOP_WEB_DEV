<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../models/ArticleCommentModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
    exit;
}

$userId = $_SESSION['user_id'];
$articleId = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$parentId = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Nội dung không được để trống.']);
    exit;
}

$commentModel = new ArticleCommentModel();
$id = $commentModel->create($userId, $articleId, $content, $parentId);

if ($id) {
    echo json_encode(['success' => true, 'id' => $id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi server.']);
}
?>
