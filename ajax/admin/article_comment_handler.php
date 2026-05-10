<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/ArticleCommentModel.php';
require_once __DIR__ . '/../../models/UserModel.php';

// Check if user is admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userModel = new UserModel();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$commentModel = new ArticleCommentModel();
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'list') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $filters = ['status' => $status, 'search' => $search];
    $result = $commentModel->adminGetPaginated($page, 10, $filters);
    
    // Stats
    $db = Database::getConnection();
    $reportedCount = (int)$db->query("SELECT COUNT(*) FROM article_comments WHERE report_count > 0")->fetchColumn();
    
    $result['reported_count'] = $reportedCount;
    echo json_encode($result);
} elseif ($action === 'update_status') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = $_POST['status'];
    $success = $commentModel->updateStatus($id, $status);
    echo json_encode(['success' => $success]);
} elseif ($action === 'toggle_hidden') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $isHidden = (int)$_POST['is_hidden'];
    $success = $commentModel->toggleHidden($id, $isHidden);
    echo json_encode(['success' => $success]);
} elseif ($action === 'delete') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $db = Database::getConnection();
    $stmt = $db->prepare("DELETE FROM article_comments WHERE id = ?");
    $success = $stmt->execute([$id]);
    echo json_encode(['success' => $success]);
} elseif ($action === 'get_reports') {
    $commentId = isset($_GET['comment_id']) ? (int)$_GET['comment_id'] : 0;
    $reports = $commentModel->getCommentReports($commentId);
    echo json_encode($reports);
} elseif ($action === 'resolve_report') {
    $reportId = isset($_POST['report_id']) ? (int)$_POST['report_id'] : 0;
    $status = $_POST['status'];
    $success = $commentModel->resolveReport($reportId, $_SESSION['user_id'], $status);
    echo json_encode(['success' => $success]);
}
?>
