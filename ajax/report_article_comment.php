<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../models/ArticleCommentModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
    exit;
}

$userId = $_SESSION['user_id'];
$commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if (!$commentId || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$commentModel = new ArticleCommentModel();
$success = $commentModel->report($commentId, $userId, $reason, $description);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể gửi báo cáo.']);
}
?>
