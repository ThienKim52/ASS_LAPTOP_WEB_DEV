# LaptopShop - E-Commerce Platform

A custom MVC e-commerce application built with raw PHP, MySQL, HTML5, CSS3, and Vanilla JavaScript.

## Tech Stack

- **Backend**: PHP 7.0+ (No frameworks)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, Bootstrap 5, Vanilla JS
- **Architecture**: Custom MVC pattern

## Features

### Client-Side
- Product catalog with filtering (brand, price, storage)
- Product detail with variants (RAM, color, storage)
- Shopping cart functionality
- Checkout with COD/Credit Card
- User registration and authentication
- Product reviews and ratings
- Article/Blog system with comments
- FAQ page
- Contact form

### Admin Panel
- Dashboard overview
- Product management (CRUD)
- Order management
- Review moderation
- Article/Post management
- Article comment moderation
- FAQ management
- Contact message management
- Site settings configuration

## Installation

### Prerequisites
- PHP 7.0+ with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx) or PHP built-in server

### Setup

1. **Clone/Download** the project to your web server directory

2. **Create Database**
   ```bash
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seed.sql
   ```

3. **Configure Database Connection**
   
   Edit `config/constants.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'laptopshop');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Run Development Server**
   ```bash
   php -S localhost:8080
   ```

5. **Access the Application**
   - Frontend: http://localhost:8080
   - Admin: http://localhost:8080/?page=admin_dashboard

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@laptop.vn | Admin@123 |
| Member | nva@gmail.com | Admin@123 |
| Member | ttb@gmail.com | Admin@123 |

## Directory Structure

```
├── ajax/                   # AJAX handlers
│   └── admin/              # Admin-specific AJAX
├── assets/                 # Static assets
│   ├── css/                # Stylesheets
│   ├── img/                # Images
│   └── javascript/         # JS files
├── config/                 # Configuration
│   ├── constants.php       # App constants
│   └── db.php              # Database connection
├── controllers/            # MVC Controllers
│   └── admin/              # Admin controllers
├── database/               # SQL files
│   ├── schema.sql          # Database structure
│   └── seed.sql            # Sample data
├── helpers/                # Utility classes
├── middleware/             # Auth middleware
├── models/                 # MVC Models
├── uploads/                # User uploads
├── views/                  # MVC Views
│   ├── admin/              # Admin views
│   ├── client/             # Client views
│   └── layouts/            # Shared layouts
└── index.php               # Front controller (router)
```

## Database Tables

| Table | Description |
|-------|-------------|
| users | User accounts |
| admins | Admin role mapping |
| members | Member details + points |
| membership_tiers | Loyalty tiers |
| products | Product catalog |
| product_variants | Product variants (RAM, color, storage) |
| categories | Product categories |
| brands | Product brands |
| carts | Shopping carts |
| cart_items | Cart line items |
| orders | Customer orders |
| order_items | Order line items |
| coupons | Discount codes |
| reviews | Product reviews |
| articles | Blog posts |
| article_comments | Article comments |
| comment_reports | Comment moderation reports |
| faqs | FAQ entries |
| contacts | Contact form submissions |
| site_settings | Site configuration |

## API Endpoints

### AJAX Handlers
- `ajax/cart_handler.php` - Cart operations
- `ajax/checkout_handler.php` - Order processing
- `ajax/submit_review.php` - Submit product review
- `ajax/send_contact.php` - Contact form
- `ajax/get_article_comments.php` - Load comments
- `ajax/submit_article_comment.php` - Post comment
- `ajax/report_article_comment.php` - Report comment

### Admin AJAX
- `ajax/admin/update_review_status.php` - Moderate reviews
- `ajax/admin/delete_review.php` - Delete review
- `ajax/admin/article_comment_handler.php` - Manage comments

## Security Features

- PDO prepared statements (SQL injection prevention)
- XSS auto-sanitization in BaseController
- CSRF tokens in forms
- Password hashing with bcrypt
- Session-based authentication
- Admin route protection via middleware

## License

University Assignment Project - Educational Use Only
