<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/UserModel.php';

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

$postModel = new PostModel();
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($action === 'list') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $offset = ($page - 1) * $limit;
    $posts = $postModel->getAllPostsForAdmin($status, $limit, $offset, $search);
    $total = $postModel->countPostsForAdmin($status, $search);
    
    // Stats
    $published = $postModel->countPostsForAdmin('published');
    $draft = $postModel->countPostsForAdmin('draft');
    
    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'total' => $total,
        'stats' => [
            'published' => $published,
            'draft' => $draft
        ]
    ]);
} elseif ($action === 'save') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $data = [
        'title' => $_POST['title'],
        'slug' => $_POST['slug'],
        'content' => $_POST['content'],
        'meta_title' => $_POST['meta_title'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? '',
        'meta_keywords' => $_POST['meta_keywords'] ?? '',
        'thumbnail_url' => $_POST['thumbnail_url'] ?? '',
        'published_at' => !empty($_POST['published_at']) ? str_replace('T', ' ', $_POST['published_at']) : null,
        'admin_id' => $_SESSION['user_id']
    ];

    if ($id) {
        $success = $postModel->updatePost($id, $data);
    } else {
        $success = $postModel->createPost($data) !== false;
    }

    echo json_encode(['success' => (bool)$success]);
} elseif ($action === 'delete') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $success = $postModel->deletePost($id);
    echo json_encode(['success' => $success]);
} elseif ($action === 'get') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    echo json_encode(['success' => true, 'post' => $post]);
}
?>
