-- =============================================
-- LaptopShop Unified Database Schema
-- Merged from all branches with article comment enhancements
-- =============================================

CREATE DATABASE IF NOT EXISTS laptopshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE laptopshop;

-- =============================================
-- SYSTEM & SETTINGS
-- =============================================
CREATE TABLE IF NOT EXISTS site_settings (
    `key`      VARCHAR(100) PRIMARY KEY,
    `value`    TEXT         NOT NULL,
    updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- USERS & MEMBERSHIPS (Class Table Inheritance)
-- =============================================
CREATE TABLE IF NOT EXISTS membership_tiers (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(50)   NOT NULL,
    min_points       INT           NOT NULL DEFAULT 0,
    discount_percent DECIMAL(5,2)  NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id            INT          AUTO_INCREMENT PRIMARY KEY,
    fullname      VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    phone         VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    avatar_url    VARCHAR(255) DEFAULT NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS members (
    user_id INT PRIMARY KEY,
    tier_id INT DEFAULT NULL,
    points  INT NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tier_id) REFERENCES membership_tiers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- PRODUCTS CATALOG
-- =============================================
CREATE TABLE IF NOT EXISTS categories (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    is_featured TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS brands (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(100) NOT NULL UNIQUE,
    logo_url   VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id                 INT          AUTO_INCREMENT PRIMARY KEY,
    category_id        INT          DEFAULT NULL,
    brand_id           INT          DEFAULT NULL,
    name               VARCHAR(255) NOT NULL,
    slug               VARCHAR(255) NOT NULL UNIQUE,
    short_description  TEXT,
    detail_description LONGTEXT,
    is_featured        TINYINT(1)   NOT NULL DEFAULT 0,
    created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id)    REFERENCES brands(id)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_variants (
    id         INT            AUTO_INCREMENT PRIMARY KEY,
    product_id INT            NOT NULL,
    sku_code   VARCHAR(50)    NOT NULL UNIQUE,
    ram        VARCHAR(50),
    color      VARCHAR(50),
    storage    VARCHAR(50),
    quantity   INT            NOT NULL DEFAULT 0,
    base_price DECIMAL(15,2)  NOT NULL,
    img_url    VARCHAR(255)   DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SHOPPING & ORDERS
-- =============================================
CREATE TABLE IF NOT EXISTS carts (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    user_id    INT       NOT NULL UNIQUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES members(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cart_items (
    cart_id    INT           NOT NULL,
    variant_id INT           NOT NULL,
    quantity   INT           NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (cart_id, variant_id),
    FOREIGN KEY (cart_id)    REFERENCES carts(id)            ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    user_id          INT           DEFAULT NULL,
    order_code       VARCHAR(20)   NOT NULL UNIQUE,
    shipping_address TEXT          NOT NULL,
    total_amount     DECIMAL(15,2) NOT NULL,
    discount_amount  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    final_amount     DECIMAL(15,2) NOT NULL,
    payment_method   ENUM('cod','credit_card')                                     NOT NULL DEFAULT 'cod',
    payment_status   ENUM('unpaid','paid','refunded')                              NOT NULL DEFAULT 'unpaid',
    status           ENUM('pending','confirmed','shipping','completed','canceled')  NOT NULL DEFAULT 'pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES members(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
    id          INT            AUTO_INCREMENT PRIMARY KEY,
    order_id    INT            NOT NULL,
    variant_id  INT            DEFAULT NULL,
    quantity    INT            NOT NULL,
    unit_price  DECIMAL(15,2)  NOT NULL,
    total_price DECIMAL(15,2)  NOT NULL,
    UNIQUE KEY uq_order_variant (order_id, variant_id),
    FOREIGN KEY (order_id)   REFERENCES orders(id)           ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS coupons (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    code             VARCHAR(50)   NOT NULL UNIQUE,
    discount_percent DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    is_active        TINYINT(1)    NOT NULL DEFAULT 1,
    description      VARCHAR(255)  DEFAULT NULL,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTENT: ARTICLES
-- =============================================
CREATE TABLE IF NOT EXISTS articles (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    admin_id         INT          DEFAULT NULL,
    title            VARCHAR(255) NOT NULL,
    slug             VARCHAR(255) NOT NULL UNIQUE,
    content          LONGTEXT     NOT NULL,
    meta_title       VARCHAR(255) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    meta_keywords    VARCHAR(255) DEFAULT NULL,
    thumbnail_url    VARCHAR(255) DEFAULT NULL,
    view_count       INT          DEFAULT 0,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    published_at     TIMESTAMP    NULL DEFAULT NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Article comments with nested replies and moderation support
CREATE TABLE IF NOT EXISTS article_comments (
    id           INT       AUTO_INCREMENT PRIMARY KEY,
    article_id   INT       NOT NULL,
    user_id      INT       NOT NULL,
    parent_id    INT       DEFAULT NULL,
    content      TEXT      NOT NULL,
    status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    report_count INT       DEFAULT 0,
    is_hidden    TINYINT(1) DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id)         ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES members(user_id)     ON DELETE CASCADE,
    FOREIGN KEY (parent_id)  REFERENCES article_comments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Comment reports for moderation
CREATE TABLE IF NOT EXISTS comment_reports (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    comment_id  INT NOT NULL,
    user_id     INT NOT NULL,
    reason      VARCHAR(100) NOT NULL,
    description TEXT NULL,
    status      ENUM('pending','resolved','rejected') NOT NULL DEFAULT 'pending',
    resolved_by INT NULL,
    resolved_at TIMESTAMP NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id)  REFERENCES article_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)     REFERENCES users(id)            ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id)            ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTENT: FAQs
-- =============================================
CREATE TABLE IF NOT EXISTS faqs (
    id         INT        AUTO_INCREMENT PRIMARY KEY,
    question   TEXT       NOT NULL,
    answer     TEXT       NOT NULL,
    sort_order INT        NOT NULL DEFAULT 0,
    is_active  TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- REVIEWS
-- =============================================
CREATE TABLE IF NOT EXISTS reviews (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    user_id    INT       NOT NULL,
    product_id INT       NOT NULL,
    rating     TINYINT   NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT,
    status     ENUM('pending','approved','reject') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_product (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- CONTACTS
-- =============================================
CREATE TABLE IF NOT EXISTS contacts (
    id             INT          AUTO_INCREMENT PRIMARY KEY,
    customer_name  VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    subject        VARCHAR(255) NOT NULL,
    message        TEXT         NOT NULL,
    status         ENUM('unread','read','replied') NOT NULL DEFAULT 'unread',
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
