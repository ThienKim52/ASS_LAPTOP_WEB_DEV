<?php
// controllers/PostController.php

require_once __DIR__ . '/../models/PostModel.php';

class PostController {
    private $postModel;
    
    public function __construct() {
        $this->postModel = new PostModel();
    }
    
    /**
     * Display posts list page
     */
    public function index() {
        // Just include the view, it will load data via AJAX
        include 'views/client/post.php';
    }
    
    /**
     * Display single post detail page
     */
    public function detail() {
        $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($postId <= 0) {
            header("Location: ?page=post");
            exit();
        }
        
        $post = $this->postModel->getPostById($postId);
        
        if (!$post) {
            header("Location: ?page=post");
            exit();
        }
        
        $this->postModel->incrementViewCount($postId);
        $relatedPosts = $this->postModel->getRelatedPosts($postId, 4);
        
        include 'views/client/post_detail.php';
    }
}
?>
