<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/PostModel.php';

$postModel = new PostModel();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$posts = $postModel->getPosts($page, $limit, null, $search);
$total = $postModel->countPosts(null, $search);

echo json_encode([
    'items' => $posts,
    'total' => $total,
    'page' => $page,
    'limit' => $limit,
    'total_pages' => ($limit > 0) ? ceil($total / $limit) : 0
]);
?>
