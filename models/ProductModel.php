<?php
// models/ProductModel.php
// Extends BaseModel for singleton PDO. Queries follow ERD schema exactly.

require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {


    /**
     * Central base SELECT that JOINs products → brands, categories, product_variants
     * and aliases columns to match what FE views expect (brand, price, stock, image, etc.)
     */
    private function getBaseSelect() {
        return "SELECT 
            p.id, 
            p.name,
            p.slug,
            p.short_description,
            p.detail_description as description,
            p.is_featured,
            p.created_at,
            b.name   AS brand, 
            c.name   AS category,
            c.slug   AS category_slug,
            COALESCE((SELECT base_price FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1), 0) AS price,
            (SELECT storage    FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1) AS storage,
            (SELECT ram        FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1) AS ram,
            COALESCE((SELECT img_url FROM product_variants WHERE product_id = p.id AND img_url IS NOT NULL LIMIT 1), 'assets/img/placeholder.png') AS image,
            COALESCE((SELECT SUM(quantity) FROM product_variants WHERE product_id = p.id), 0) AS stock
        FROM products p
        LEFT JOIN brands     b ON p.brand_id    = b.id
        LEFT JOIN categories c ON p.category_id = c.id";
    }

    // ========================= PUBLIC LIST =========================

    public function getAllProducts($limit = 12, $offset = 0, $sort = 'default') {
        try {
            $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t";
            switch ($sort) {
                case 'price_asc':  $query .= " ORDER BY price ASC";  break;
                case 'price_desc': $query .= " ORDER BY price DESC"; break;
                case 'name_asc':   $query .= " ORDER BY name ASC";   break;
                default:           $query .= " ORDER BY created_at DESC";
            }
            $query .= " LIMIT :lim OFFSET :off";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':lim', (int)$limit,  PDO::PARAM_INT);
            $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllProducts: " . $e->getMessage());
            return [];
        }
    }

    public function countProducts() {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM products");
        return (int) $stmt->fetch()['total'];
    }

    // ========================= SINGLE PRODUCT =========================

    /**
     * Get product by ID
     * @param int $id
     * @return array|false
     */
    public function getProductById($id) {
        $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // ========================= FILTERED LIST =========================

    public function getFilteredProducts($filters = [], $limit = 12, $offset = 0, $sort = 'default') {
        $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $query .= " AND (name LIKE ? OR brand LIKE ? OR category LIKE ? OR short_description LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['brand'])) {
            $ph = implode(',', array_fill(0, count($filters['brand']), '?'));
            $query .= " AND brand IN ($ph)";
            $params = array_merge($params, $filters['brand']);
        }
        if (!empty($filters['price_min'])) {
            $query .= " AND price >= ?"; $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $query .= " AND price <= ?"; $params[] = $filters['price_max'];
        }
        if (!empty($filters['storage'])) {
            $ph = implode(',', array_fill(0, count($filters['storage']), '?'));
            $query .= " AND storage IN ($ph)";
            $params = array_merge($params, $filters['storage']);
        }
        if (!empty($filters['category'])) {
            $query .= " AND category = ?"; $params[] = $filters['category'];
        }

        switch ($sort) {
            case 'price_asc':  $query .= " ORDER BY price ASC";  break;
            case 'price_desc': $query .= " ORDER BY price DESC"; break;
            case 'name_asc':   $query .= " ORDER BY name ASC";   break;
            default:           $query .= " ORDER BY created_at DESC";
        }
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFilteredProducts($filters = []) {
        try {
            $query = "SELECT COUNT(*) AS total FROM (" . $this->getBaseSelect() . ") AS t WHERE 1=1";
            $params = [];

            if (!empty($filters['search'])) {
                $query .= " AND (name LIKE ? OR brand LIKE ? OR category LIKE ? OR short_description LIKE ? OR description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if (!empty($filters['brand']) && is_array($filters['brand'])) {
                $ph = implode(',', array_fill(0, count($filters['brand']), '?'));
                $query .= " AND brand IN ($ph)";
                $params = array_merge($params, $filters['brand']);
            }
            if (!empty($filters['category'])) {
                $query .= " AND category = ?"; $params[] = $filters['category'];
            }
            if (!empty($filters['storage']) && is_array($filters['storage'])) {
                $ph = implode(',', array_fill(0, count($filters['storage']), '?'));
                $query .= " AND storage IN ($ph)";
                $params = array_merge($params, $filters['storage']);
            }
            if (!empty($filters['price_min']) && is_numeric($filters['price_min'])) {
                $query .= " AND price >= ?"; $params[] = (float)$filters['price_min'];
            }
            if (!empty($filters['price_max']) && is_numeric($filters['price_max'])) {
                $query .= " AND price <= ?"; $params[] = (float)$filters['price_max'];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return (int)$stmt->fetch()['total'];
        } catch (PDOException $e) {
            error_log("countFilteredProducts: " . $e->getMessage());
            return 0;
        }
    }

    public function getFilterOptions() {
        return [
            'brands'     => $this->db->query("SELECT name FROM brands ORDER BY name")->fetchAll(PDO::FETCH_COLUMN),
            'storages'   => $this->db->query("SELECT DISTINCT storage FROM product_variants WHERE storage IS NOT NULL ORDER BY storage")->fetchAll(PDO::FETCH_COLUMN),
            'categories' => $this->db->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN),
        ];
    }

    // ========================= PRODUCT IMAGES =========================
    // View expects: ['image_url' => '...']

    public function getProductImages($productId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT img_url AS image_url 
                 FROM product_variants 
                 WHERE product_id = ? AND img_url IS NOT NULL"
            );
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ========================= VARIANTS (Grouped) =========================
    // The view expects each variant to have:
    //   id, variant_name, variant_value, price_modifier, stock, is_default

    public function getProductVariantsGrouped($productId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = ?");
            $stmt->execute([$productId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get the cheapest variant's base_price as reference
            $basePrice = PHP_FLOAT_MAX;
            foreach ($rows as $r) {
                if ((float)$r['base_price'] < $basePrice) $basePrice = (float)$r['base_price'];
            }

            $grouped = ['storage' => [], 'color' => [], 'ram' => []];
            $seenStorage = []; $seenColor = []; $seenRam = [];
            $isFirst = ['storage' => true, 'color' => true, 'ram' => true];

            foreach ($rows as $r) {
                $modifier = (float)$r['base_price'] - $basePrice;

                // Storage variants
                if (!empty($r['storage']) && !in_array($r['storage'], $seenStorage)) {
                    $seenStorage[] = $r['storage'];
                    $grouped['storage'][] = [
                        'id'             => $r['id'],
                        'variant_name'   => $r['storage'],
                        'variant_value'  => $r['storage'],
                        'price_modifier' => $modifier,
                        'stock'          => (int)$r['quantity'],
                        'is_default'     => $isFirst['storage'] ? 1 : 0,
                    ];
                    $isFirst['storage'] = false;
                }

                // Color variants
                if (!empty($r['color']) && !in_array($r['color'], $seenColor)) {
                    $seenColor[] = $r['color'];
                    $colorName = $r['color'];
                    $colorMap = [
                        'Black' => '#111111',
                        'Silver' => '#c0c0c0',
                        'Gray' => '#6c757d',
                        'Space Gray' => '#5c5f66',
                        'Space Black' => '#111827',
                        'Midnight' => '#1f2a44',
                        'Starlight' => '#f2e8cf',
                        'White' => '#f8f9fa',
                        'Blue' => '#4f6df5',
                    ];
                    $grouped['color'][] = [
                        'id'             => $r['id'],
                        'variant_name'   => $colorName,
                        'variant_value'  => $colorMap[$colorName] ?? $colorName,
                        'price_modifier' => 0,
                        'stock'          => (int)$r['quantity'],
                        'is_default'     => $isFirst['color'] ? 1 : 0,
                    ];
                    $isFirst['color'] = false;
                }

                // RAM variants
                if (!empty($r['ram']) && !in_array($r['ram'], $seenRam)) {
                    $seenRam[] = $r['ram'];
                    $grouped['ram'][] = [
                        'id'             => $r['id'],
                        'variant_name'   => $r['ram'],
                        'variant_value'  => $r['ram'],
                        'price_modifier' => 0,
                        'stock'          => (int)$r['quantity'],
                        'is_default'     => $isFirst['ram'] ? 1 : 0,
                    ];
                    $isFirst['ram'] = false;
                }
            }
            return $grouped;
        } catch (PDOException $e) {
            return ['storage' => [], 'color' => [], 'ram' => []];
        }
    }

    // ========================= DEFAULT VARIANTS =========================
    // View expects: [{id, price_modifier}]

    public function getDefaultVariants($productId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM product_variants WHERE product_id = ? ORDER BY base_price ASC LIMIT 1"
            );
            $stmt->execute([$productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return [];
            // The cheapest variant has price_modifier = 0
            $row['price_modifier'] = 0;
            return [$row];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getProductVariants($productId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = ?");
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function calculatePrice($productId, $selectedVariants = []) {
        $p = $this->getProductById($productId);
        return $p ? (float)$p['price'] : 0;
    }

    public function getProductReviews($productId, $limit = 10) {
        return []; // Handled by ProductReviewModel
    }

    public function getRelatedProducts($currentProductId, $category, $limit = 4) {
        try {
            $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t 
                      WHERE category = ? AND id != ? ORDER BY RAND() LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$category, $currentProductId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ========================= ADMIN FUNCTIONS =========================
    
    /**
     * Create a new product with variant
     */
    public function createProduct($data) {
        try {
            $this->db->beginTransaction();

            // Get or create brand
            $brandId = $this->getOrCreateBrand($data['brand'] ?? 'Unknown');
            if (!$brandId) {
                throw new Exception("Failed to create/get brand");
            }
            
            // Get or create category
            $categoryId = $this->getOrCreateCategory($data['category'] ?? 'Other');
            if (!$categoryId) {
                throw new Exception("Failed to create/get category");
            }
            
            // Create slug from name
            $slug = $this->createSlug($data['name']);
            
            // Ensure unique slug
            $slug = $this->makeUniqueSlug($slug);
            
            // Insert product
            $stmt = $this->db->prepare(
                "INSERT INTO products 
                 (name, slug, brand_id, category_id, short_description, detail_description, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );
            $stmt->execute([
                $data['name'],
                $slug,
                $brandId,
                $categoryId,
                substr($data['description'] ?? '', 0, 100), // short_description
                $data['description'] ?? ''
            ]);
            
            $productId = $this->db->lastInsertId();
            if (!$productId) {
                throw new Exception("Failed to get product ID after insert");
            }
            
            // Create product variant
            $skuCode = $this->generateSKU($productId);
            
            $stmt = $this->db->prepare(
                "INSERT INTO product_variants 
                 (product_id, sku_code, ram, storage, quantity, base_price, img_url) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $productId,
                $skuCode,
                $data['ram'] ?? null,
                $data['storage'] ?? null,
                (int)($data['stock'] ?? 0),
                (float)($data['price'] ?? 0),
                $data['image'] ?? 'assets/img/placeholder.png'
            ]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("createProduct error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing product
     */
    public function updateProduct($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Get or create brand
            $brandId = $this->getOrCreateBrand($data['brand'] ?? 'Unknown');
            
            // Get or create category
            $categoryId = $this->getOrCreateCategory($data['category'] ?? 'Other');
            
            // Update product
            $stmt = $this->db->prepare(
                "UPDATE products 
                 SET name = ?, brand_id = ?, category_id = ?, 
                     short_description = ?, detail_description = ?, updated_at = NOW()
                 WHERE id = ?"
            );
            $stmt->execute([
                $data['name'],
                $brandId,
                $categoryId,
                substr($data['description'] ?? '', 0, 100),
                $data['description'] ?? '',
                $id
            ]);
            
            // Update variant
            $stmt = $this->db->prepare(
                "UPDATE product_variants 
                 SET ram = ?, storage = ?, quantity = ?, base_price = ?"
                . (isset($data['image']) && $data['image'] ? ", img_url = ?" : "")
                . " WHERE product_id = ? 
                 LIMIT 1"
            );
            
            $params = [
                $data['ram'] ?? null,
                $data['storage'] ?? null,
                (int)($data['stock'] ?? 0),
                (float)($data['price'] ?? 0)
            ];
            
            if (isset($data['image']) && $data['image']) {
                $params[] = $data['image'];
            }
            
            $params[] = $id;
            $stmt->execute($params);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("updateProduct error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete product and its variants
     */
    public function deleteProduct($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("deleteProduct error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get or create brand by name
     * Returns brand ID or null on failure
     */
    private function getOrCreateBrand($brandName) {
        try {
            if (empty($brandName)) {
                $brandName = 'Unknown';
            }
            
            // Try to find existing brand
            $stmt = $this->db->prepare("SELECT id FROM brands WHERE name = ? LIMIT 1");
            $stmt->execute([$brandName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['id'])) {
                return (int)$result['id'];
            }
            
            // Create new brand
            $slug = $this->createSlug($brandName);
            $slug = $this->makeUniqueBrandSlug($slug);
            
            $stmt = $this->db->prepare(
                "INSERT INTO brands (name, slug, created_at) VALUES (?, ?, NOW())"
            );
            if (!$stmt->execute([$brandName, $slug])) {
                throw new Exception("Failed to insert brand");
            }
            
            $brandId = $this->db->lastInsertId();
            if (!$brandId) {
                throw new Exception("Failed to get brand ID");
            }
            
            return (int)$brandId;
        } catch (Exception $e) {
            error_log("getOrCreateBrand error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get or create category by name
     * Returns category ID or null on failure
     */
    private function getOrCreateCategory($categoryName) {
        try {
            if (empty($categoryName)) {
                $categoryName = 'Other';
            }
            
            // Try to find existing category
            $stmt = $this->db->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
            $stmt->execute([$categoryName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['id'])) {
                return (int)$result['id'];
            }
            
            // Create new category
            $slug = $this->createSlug($categoryName);
            $slug = $this->makeUniqueCategorySlug($slug);
            
            $stmt = $this->db->prepare(
                "INSERT INTO categories (name, slug, created_at) VALUES (?, ?, NOW())"
            );
            if (!$stmt->execute([$categoryName, $slug])) {
                throw new Exception("Failed to insert category");
            }
            
            $categoryId = $this->db->lastInsertId();
            if (!$categoryId) {
                throw new Exception("Failed to get category ID");
            }
            
            return (int)$categoryId;
        } catch (Exception $e) {
            error_log("getOrCreateCategory error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create URL-friendly slug from text
     */
    private function createSlug($text) {
        // Convert to lowercase
        $slug = strtolower($text);
        
        // Replace spaces with hyphens
        $slug = preg_replace('/\s+/', '-', $slug);
        
        // Remove special characters, keep only alphanumeric and hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Ensure slug is unique for products
     */
    private function makeUniqueSlug($slug, $originalSlug = null) {
        if (!$originalSlug) {
            $originalSlug = $slug;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM products WHERE slug = ?");
            $stmt->execute([$slug]);
            $result = $stmt->fetch();
            
            if ($result['cnt'] == 0) {
                return $slug;
            }
            
            // Add number suffix
            $i = 1;
            do {
                $newSlug = $originalSlug . '-' . $i;
                $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM products WHERE slug = ?");
                $stmt->execute([$newSlug]);
                $result = $stmt->fetch();
                if ($result['cnt'] == 0) {
                    return $newSlug;
                }
                $i++;
            } while ($i < 100);
            
            return $slug . '-' . time();
        } catch (PDOException $e) {
            return $slug . '-' . time();
        }
    }
    
    /**
     * Ensure slug is unique for brands
     */
    private function makeUniqueBrandSlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM brands WHERE slug = ?");
            $stmt->execute([$slug]);
            $result = $stmt->fetch();
            
            if ($result['cnt'] == 0) {
                return $slug;
            }
            
            return $slug . '-' . time();
        } catch (PDOException $e) {
            return $slug . '-' . time();
        }
    }
    
    /**
     * Ensure slug is unique for categories
     */
    private function makeUniqueCategorySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM categories WHERE slug = ?");
            $stmt->execute([$slug]);
            $result = $stmt->fetch();
            
            if ($result['cnt'] == 0) {
                return $slug;
            }
            
            return $slug . '-' . time();
        } catch (PDOException $e) {
            return $slug . '-' . time();
        }
    }
    
    /**
     * Generate unique SKU code
     */
    private function generateSKU($productId) {
        // Format: SKU-PRODUCTID-TIMESTAMP
        return 'SKU-' . str_pad($productId, 5, '0', STR_PAD_LEFT) . '-' . time();
    }
    
    public function getAllBrands() {
        return $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
