<?php
require_once __DIR__ . '/BaseModel.php';

class ProductReviewModel extends BaseModel {

    // ========== USER-SIDE METHODS ==========
    
    /**
     * Tạo review mới
     */
    public function createReview($data) {
        try {
            $sql = "INSERT INTO reviews 
                    (product_id, user_id, rating, comment, status) 
                    VALUES (:product_id, :user_id, :rating, :comment, :status)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':product_id' => $data['product_id'],
                ':user_id' => $data['user_id'],
                ':rating' => $data['rating'],
                ':comment' => $data['comment'],
                ':status' => $data['status'] ?? 'pending'
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create review error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kiểm tra user đã mua sản phẩm chưa
     */
    public function hasUserPurchasedProduct($userId, $productId) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM order_items oi
                    INNER JOIN orders o ON oi.order_id = o.id
                    WHERE o.user_id = :user_id 
                    AND oi.product_id = :product_id 
                    AND o.status = 'completed'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Lấy các review đã được duyệt cho sản phẩm
     */
    public function getApprovedReviews($productId) {
        try {
            $sql = "SELECT r.*, u.fullname as user_name, u.avatar_url
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.product_id = :product_id AND r.status = 'approved'
                    ORDER BY r.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Alias for getApprovedReviews with pagination support
     */
    public function getApprovedReviewsByProduct($productId, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT r.*, u.fullname as user_name, u.avatar_url
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    WHERE r.product_id = :product_id AND r.status = 'approved'
                    ORDER BY r.created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Lấy thống kê reviews của 1 sản phẩm
     */
    public function getReviewStats($productId) {
        try {
            $sql = "SELECT
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM reviews
                    WHERE product_id = :product_id AND status = 'approved'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':product_id' => $productId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get review stats error: " . $e->getMessage());
            return null;
        }
    }

    // ========== ADMIN-SIDE METHODS ==========

    /**
     * Lấy danh sách review cho admin (có phân trang)
     */
    public function getAllReviewsAdmin($status = 'all', $limit = 15, $offset = 0) {
        try {
            $where = "";
            $params = [];
            
            if ($status !== 'all') {
                $where = " WHERE r.status = :status ";
                $params[':status'] = ($status === 'rejected' ? 'reject' : $status);
            }
            
            $sql = "SELECT r.*, u.fullname as user_name, u.email as user_email, p.name as product_name
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    JOIN products p ON r.product_id = p.id
                    $where
                    ORDER BY r.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get reviews admin error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số lượng review theo trạng thái
     */
    public function countReviewsByStatus($status = 'all') {
        try {
            $where = "";
            $params = [];
            
            if ($status !== 'all') {
                $where = " WHERE status = :status ";
                $params[':status'] = ($status === 'rejected' ? 'reject' : $status);
            }
            
            $sql = "SELECT COUNT(*) as count FROM reviews $where";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Cập nhật trạng thái review
     */
    public function updateReviewStatus($id, $status) {
        try {
            // Map 'rejected' to 'reject' for DB compatibility
            if ($status === 'rejected') $status = 'reject';
            
            $sql = "UPDATE reviews SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Xoá review
     */
    public function deleteReview($id) {
        try {
            $sql = "DELETE FROM reviews WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}