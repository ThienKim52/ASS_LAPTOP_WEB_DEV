<?php
// models/ArticleCommentModel.php

require_once __DIR__ . '/BaseModel.php';

class ArticleCommentModel extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->ensureTablesAndColumns();
    }

    /**
     * Ensure necessary tables and columns exist for comments and reports.
     */
    private function ensureTablesAndColumns() {
        // Create comment_reports table if it doesn't exist
        $this->db->exec("CREATE TABLE IF NOT EXISTS comment_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            comment_id INT NOT NULL,
            user_id INT NOT NULL,
            reason VARCHAR(100) NOT NULL,
            description TEXT NULL,
            status ENUM('pending','resolved','rejected') NOT NULL DEFAULT 'pending',
            resolved_by INT NULL,
            resolved_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (comment_id) REFERENCES article_comments(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Add report_count and is_hidden to article_comments if they don't exist
        try {
            $this->db->exec("ALTER TABLE article_comments ADD COLUMN IF NOT EXISTS report_count INT DEFAULT 0");
            $this->db->exec("ALTER TABLE article_comments ADD COLUMN IF NOT EXISTS is_hidden TINYINT(1) DEFAULT 0");
            $this->db->exec("ALTER TABLE article_comments ADD COLUMN IF NOT EXISTS parent_id INT NULL DEFAULT NULL, ADD FOREIGN KEY IF NOT EXISTS (parent_id) REFERENCES article_comments(id) ON DELETE CASCADE");
        } catch (PDOException $e) {
            // Some MySQL versions don't support ADD COLUMN IF NOT EXISTS
            // Silently fail if they already exist
        }
    }

    /**
     * Get approved comments for an article.
     */
    public function getByArticle($articleId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT ac.*, u.fullname as commenter_name, u.avatar_url as commenter_avatar
                FROM article_comments ac
                JOIN users u ON ac.user_id = u.id
                WHERE ac.article_id = ? AND ac.status = 'approved' AND ac.is_hidden = 0
                ORDER BY ac.created_at DESC
                LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $articleId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Create a new comment.
     */
    public function create($userId, $articleId, $content, $parentId = null) {
        $sql = "INSERT INTO article_comments (user_id, article_id, parent_id, content, status) VALUES (?, ?, ?, ?, 'approved')";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $articleId, $parentId, $content]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Report a comment.
     */
    public function report($commentId, $userId, $reason, $description = null) {
        $sql = "INSERT INTO comment_reports (comment_id, user_id, reason, description) VALUES (?, ?, ?, ?)";
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$commentId, $userId, $reason, $description]);
            
            $updateSql = "UPDATE article_comments SET report_count = report_count + 1 WHERE id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([$commentId]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Admin: Get paginated comments with moderation info.
     */
    public function adminGetPaginated($page = 1, $limit = 10, $filters = []) {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND ac.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (ac.content LIKE ? OR u.fullname LIKE ? OR a.title LIKE ?)";
            $search = "%" . $filters['search'] . "%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql = "SELECT ac.*, u.fullname as commenter_name, a.title as article_title
                FROM article_comments ac
                JOIN users u ON ac.user_id = u.id
                JOIN articles a ON ac.article_id = a.id
                WHERE $where
                ORDER BY ac.created_at DESC
                LIMIT ? OFFSET ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $i = 1;
            foreach ($params as $p) { $stmt->bindValue($i++, $p); }
            $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
            $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll();

            $countSql = "SELECT COUNT(*) FROM article_comments ac JOIN users u ON ac.user_id = u.id JOIN articles a ON ac.article_id = a.id WHERE $where";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            return [
                'items' => $items,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ];
        } catch (PDOException $e) {
            return ['items' => [], 'total' => 0, 'total_pages' => 0];
        }
    }

    /**
     * Admin: Update comment status.
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE article_comments SET status = ? WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Admin: Toggle hidden.
     */
    public function toggleHidden($id, $isHidden) {
        $sql = "UPDATE article_comments SET is_hidden = ? WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$isHidden ? 1 : 0, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Admin: Get reports for a comment.
     */
    public function getCommentReports($commentId) {
        $sql = "SELECT cr.*, u.fullname as reporter_name
                FROM comment_reports cr
                JOIN users u ON cr.user_id = u.id
                WHERE cr.comment_id = ?
                ORDER BY cr.created_at DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$commentId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Admin: Resolve report.
     */
    public function resolveReport($reportId, $adminId, $status) {
        $sql = "UPDATE comment_reports SET status = ?, resolved_by = ?, resolved_at = NOW() WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $adminId, $reportId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
