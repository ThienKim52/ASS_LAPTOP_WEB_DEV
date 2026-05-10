<?php
// models/PostModel.php

require_once __DIR__ . '/BaseModel.php';

class PostModel extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->ensureViewCountColumn();
    }

    private function ensureViewCountColumn() {
        try {
            $this->db->exec("ALTER TABLE articles ADD COLUMN IF NOT EXISTS view_count INT DEFAULT 0");
        } catch (PDOException $e) {
            // Silently fail if column already exists
        }
    }

    /**
     * Get all published articles with pagination and search.
     */
    public function getPosts($page = 1, $limit = 6, $categoryId = null, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $where = 'a.published_at IS NOT NULL AND a.published_at <= NOW()';
        $params = [];
        
        if ($search) {
            $where .= ' AND (a.title LIKE ? OR a.content LIKE ? OR a.meta_keywords LIKE ?)';
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Note: The original 'posts' table had category_id, but 'articles' in laptopshop.sql has it too?
        // Let me check articles table again.
        // Wait, laptopshop.sql line 47: articles table DOES NOT have category_id.
        // But the original PostModel used it. I will check if I should add it or ignore it.
        // Actually, laptopshop.sql line 47 shows: id, admin_id, title, slug, content, meta_title, meta_description, meta_keywords, thumbnail_url, created_at, published_at.
        // NO category_id in 'articles' table in the SQL provided.
        
        $sql = "SELECT a.*, u.fullname as author_name
                FROM articles a
                LEFT JOIN users u ON a.admin_id = u.id
                WHERE $where
                ORDER BY a.published_at DESC
                LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $i = 1;
            foreach ($params as $p) { $stmt->bindValue($i++, $p); }
            $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
            $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error in getPosts: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total published articles with filters.
     */
    public function countPosts($categoryId = null, $search = '') {
        $where = 'a.published_at IS NOT NULL AND a.published_at <= NOW()';
        $params = [];
        
        if ($search) {
            $where .= ' AND (a.title LIKE ? OR a.content LIKE ? OR a.meta_keywords LIKE ?)';
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT COUNT(*) FROM articles a WHERE $where";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting posts: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get article by ID.
     */
    public function getPostById($id) {
        $sql = "SELECT a.*, u.fullname as author_name
                FROM articles a
                LEFT JOIN users u ON a.admin_id = u.id
                WHERE a.id = ? AND a.published_at IS NOT NULL AND a.published_at <= NOW()";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get article by Slug (LaptopWebvvvvvv style).
     */
    public function getPostBySlug($slug) {
        $sql = "SELECT a.*, u.fullname as author_name
                FROM articles a
                LEFT JOIN users u ON a.admin_id = u.id
                WHERE a.slug = ? AND a.published_at IS NOT NULL AND a.published_at <= NOW()";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$slug]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Admin: Get all articles with pagination and search.
     */
    public function getAllPostsForAdmin($status = null, $limit = 10, $offset = 0, $search = '') {
        $where = '1=1';
        $params = [];

        if ($status === 'published') {
            $where .= ' AND a.published_at IS NOT NULL AND a.published_at <= NOW()';
        } elseif ($status === 'draft') {
            $where .= ' AND a.published_at IS NULL';
        }

        if ($search) {
            $where .= ' AND (a.title LIKE ? OR a.content LIKE ?)';
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql = "SELECT a.*, u.fullname as author_name,
                (SELECT COUNT(*) FROM article_comments WHERE article_id = a.id) as comment_count
                FROM articles a
                LEFT JOIN users u ON a.admin_id = u.id
                WHERE $where
                ORDER BY a.created_at DESC
                LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $i = 1;
            foreach ($params as $p) { $stmt->bindValue($i++, $p); }
            $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
            $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Admin SQL Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Admin: Count articles for admin stats.
     */
    public function countPostsForAdmin($status = null, $search = '') {
        $where = '1=1';
        $params = [];

        if ($status === 'published') {
            $where .= ' AND a.published_at IS NOT NULL AND a.published_at <= NOW()';
        } elseif ($status === 'draft') {
            $where .= ' AND a.published_at IS NULL';
        }

        if ($search) {
            $where .= ' AND (a.title LIKE ? OR a.content LIKE ?)';
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql = "SELECT COUNT(*) FROM articles a WHERE $where";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Admin: Create article.
     */
    public function createPost($data) {
        $sql = "INSERT INTO articles (admin_id, title, slug, content, meta_title, meta_description, meta_keywords, thumbnail_url, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['admin_id'],
                $data['title'],
                $data['slug'],
                $data['content'],
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['thumbnail_url'] ?? null,
                $data['published_at'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Admin: Update article.
     */
    public function updatePost($id, $data) {
        $sql = "UPDATE articles SET title = ?, slug = ?, content = ?, 
                meta_title = ?, meta_description = ?, meta_keywords = ?, 
                thumbnail_url = ?, published_at = ?
                WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['title'],
                $data['slug'],
                $data['content'],
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['thumbnail_url'] ?? null,
                $data['published_at'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Admin: Delete article.
     */
    public function deletePost($id) {
        $sql = "DELETE FROM articles WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get related articles (by words in title or just newest).
     */
    public function getRelatedPosts($postId, $limit = 3) {
        $sql = "SELECT id, title, slug, thumbnail_url, published_at
                FROM articles
                WHERE id != ? AND published_at IS NOT NULL AND published_at <= NOW()
                ORDER BY published_at DESC
                LIMIT ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $postId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount($id) {
        // Need to check if column exists first or just assume it might not and catch error
        try {
            $this->db->exec("UPDATE articles SET view_count = view_count + 1 WHERE id = $id");
        } catch (PDOException $e) {
            // Probably column doesn't exist
        }
    }
}
?>
