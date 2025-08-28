# Sara's Pretty Picks - E-commerce Website

A complete e-commerce website built with PHP, MySQL, HTML, CSS, and JavaScript. This website allows users to browse products, add them to cart, manage favorites, and complete the checkout process.

## Features

### 🛍️ Core E-commerce Functionality
- **Product Browsing**: Browse all products with search functionality
- **Product Details**: View detailed product information
- **Shopping Cart**: Add/remove products, view cart contents
- **Favorites**: Save products to favorites list
- **User Authentication**: Register, login, and logout functionality
- **Checkout Process**: Complete orders with order confirmation
- **Order Management**: View order history and details

### 🎨 User Interface
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern UI**: Beautiful, user-friendly interface
- **Search Functionality**: Find products by name or category
- **Product Categories**: Organized product browsing
- **Image Gallery**: High-quality product images

### 🔧 Technical Features
- **Session Management**: Secure user sessions
- **Database Integration**: MySQL database with proper relationships
- **Security**: Password hashing, SQL injection prevention
- **Error Handling**: Comprehensive error management
- **Admin Panel**: Product and user management (separate admin area)

## File Structure

```
online_shop/
├── config/
│   └── db.php                 # Database configuration
├── includes/
│   ├── auth.php              # Authentication functions
│   ├── functions.php         # Helper functions
│   ├── header.php            # Common header
│   └── footer.php            # Common footer
├── assets/
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript files
│   └── images/               # Product images
├── admin/                    # Admin panel
├── index.php                 # Homepage
├── products.php              # Product listing
├── product.php               # Product details
├── cart.php                  # Shopping cart
├── checkout.php              # Checkout process
├── favorites.php             # User favorites
├── login.php                 # User login
├── register.php              # User registration
├── logout.php                # User logout
├── add_to_cart.php           # Add to cart functionality
├── add_to_favorites.php      # Add to favorites functionality
├── order_confirmation.php    # Order confirmation page
├── setup_database.php        # Database setup script
├── test_functionality.php    # Functionality test script
└── README.md                 # This file
```

## Database Schema

### Tables
1. **users** - User accounts and authentication
2. **products** - Product catalog
3. **cart** - Shopping cart items
4. **favorites** - User favorite products
5. **orders** - Order information
6. **order_items** - Individual items in orders

## Setup Instructions

### Prerequisites
- XAMPP (or similar local server with PHP and MySQL)
- Web browser
- Text editor

### Installation Steps

1. **Clone/Download the Project**
   ```bash
   # Place the project in your XAMPP htdocs folder
   C:\xampp\htdocs\online_shop\
   ```

2. **Configure Database**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `online_shop`

3. **Update Database Configuration**
   - Edit `config/db.php`
   - Update database credentials if needed:
     ```php
     $host = 'localhost';
     $port = '3308';  // Default XAMPP MySQL port
     $dbname = 'online_shop';
     $username = 'root';
     $password = '';
     ```

4. **Setup Database Tables**
   - Visit: `http://localhost/online_shop/setup_database.php`
   - This will create all required tables and insert sample products

5. **Test Functionality**
   - Visit: `http://localhost/online_shop/test_functionality.php`
   - Verify all components are working correctly

6. **Access the Website**
   - Visit: `http://localhost/online_shop/`
   - Register a new account or use existing credentials

## Usage Guide

### For Customers

1. **Registration/Login**
   - Click "Register" to create a new account
   - Or click "Login" if you already have an account

2. **Browsing Products**
   - Visit the homepage to see featured products
   - Click "Products" to browse all products
   - Use the search bar to find specific items

3. **Adding to Cart**
   - Click "Add" button on any product
   - View cart by clicking "Cart" in the navigation

4. **Managing Favorites**
   - Click "Add to Favorites" on any product
   - View favorites by clicking "Favorites" in the navigation

5. **Checkout Process**
   - Go to cart and click "Checkout"
   - Review order summary
   - Complete the order

### For Administrators

1. **Admin Access**
   - Navigate to `/admin/login.php`
   - Use admin credentials to access the admin panel

2. **Product Management**
   - Add, edit, or delete products
   - Manage product categories and images

3. **Order Management**
   - View and manage customer orders
   - Update order status

4. **User Management**
   - View customer accounts
   - Manage user roles and permissions

## Key Features Implementation

### Session Management
- Secure session handling for user authentication
- Cart persistence across browser sessions
- Proper logout functionality

### Security Features
- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Input validation and sanitization
- CSRF protection considerations

### Database Operations
- CRUD operations for all entities
- Proper foreign key relationships
- Transaction handling for checkout process

### Frontend Features
- Responsive CSS design
- JavaScript for enhanced user experience
- Product search and filtering
- Dynamic cart updates

## Testing

### Automated Testing
Run the functionality test script to verify all components:
```
http://localhost/online_shop/test_functionality.php
```

### Manual Testing Checklist
- [ ] User registration and login
- [ ] Product browsing and search
- [ ] Adding products to cart
- [ ] Adding products to favorites
- [ ] Cart management (add/remove items)
- [ ] Checkout process
- [ ] Order confirmation
- [ ] Responsive design on different devices

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify XAMPP MySQL service is running
   - Check database credentials in `config/db.php`
   - Ensure database `online_shop` exists

2. **Images Not Loading**
   - Verify image files exist in `assets/images/`
   - Check file permissions
   - Ensure correct image paths in database

3. **Session Issues**
   - Check PHP session configuration
   - Verify `session_start()` is called
   - Clear browser cookies if needed

4. **CSS/JS Not Loading**
   - Check file paths in HTML
   - Verify CSS/JS files exist
   - Check browser console for errors

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is for educational purposes. Feel free to use and modify as needed.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Run the functionality test script
3. Review error logs in XAMPP
4. Check browser console for JavaScript errors

---

**Happy Shopping! 🛍️**
