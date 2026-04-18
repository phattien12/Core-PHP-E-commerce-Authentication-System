# 🛍️ Core PHP E-commerce & Authentication System

A lightweight, functional e-commerce web application built with **Core PHP** and **MySQL**. This project focuses on user authentication workflows and product management, featuring secure email verification via **PHPMailer**.

[](https://www.php.net/)
[](https://opensource.org/licenses/MIT)
[](https://github.com/PHPMailer/PHPMailer)

-----

## 📖 Introduction

This application serves as a comprehensive starting point for learning backend web development. It demonstrates how to handle user sessions, interact with a relational database, and integrate third-party libraries for automated email services.

## 🚀 Key Features

### 👤 User Management

  * **Secure Registration**: Create new accounts with automated email activation.
  * **Email Activation**: Verification links sent to users to validate their identity.
  * **Authentication**: Secure Login/Logout system.
  * **Password Recovery**: "Forgot Password" flow with secure reset links.

### 🛒 Product Management

  * **Dynamic Catalog**: Display products directly from the MySQL database on the homepage.
  * **Inventory Input**: Interface for adding new products, including image uploads.

### 📧 Automated Messaging

  * Integration with **PHPMailer** for reliable SMTP email delivery.

-----

## 🗂️ Project Structure

```text
Core-PHP-E-commerce-Authentication-System/
│
├── index.php              # Homepage & Product Display
├── login.php              # User Authentication
├── register.php           # User Registration
├── logout.php             # Session Termination
├── activate.php           # Account Activation Logic
├── forgot.php             # Password Recovery Request
├── reset_password.php     # New Password Input
├── add_product.php        # Product Management
├── connection.php         # Database Configuration
│
├── images/                # Product Image Repository
├── vendor/                # Composer Dependencies (PHPMailer)
├── composer.json          # Dependency Manifest
└── composer.lock
```

-----

## ⚙️ System Requirements

  * **PHP**: 7.4 or higher
  * **Database**: MySQL or MariaDB
  * **Web Server**: Apache (XAMPP, Laragon, or WAMP)
  * **Dependency Manager**: Composer

-----

## 🛠️ Installation & Setup

### 1\. Clone/Download

Clone this repository or extract the ZIP file into your server's root directory (e.g., `htdocs` or `www`).

```bash
git clone <repo-url>
```

### 2\. Database Configuration

Create a new MySQL database (e.g., `web_ban_hang`) and import the provided `.sql` file (if available).

### 3\. Connect to Database

Open `connection.php` and update your credentials:

```php
$host = "localhost";
$user = "root";
$password = "your_password";
$database = "web_ban_hang";
```

### 4\. Install Dependencies

Run the following command in the project root to install PHPMailer:

```bash
composer install
```

### 5\. SMTP Email Setup

Configure your SMTP settings in `register.php` and `forgot.php`:

```php
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your_email@gmail.com';
$mail->Password = 'your_app_password'; // Use a Google App Password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
```

-----

## 🔐 Security Best Practices (Roadmap)

While this project is designed for educational purposes, consider implementing the following for a production environment:

  * **Password Hashing**: Always use `password_hash()` and `password_verify()`.
  * **SQL Injection**: Switch to **PDO** or **MySQLi Prepared Statements**.
  * **Input Validation**: Sanitize all user inputs.
  * **CSRF Protection**: Implement tokens to prevent Cross-Site Request Forgery.

-----

## 💡 Future Enhancements

  * [ ] **Shopping Cart**: Allow users to add multiple items to a cart.
  * [ ] **Payment Gateway**: Integrate Stripe or PayPal.
  * [ ] **Admin Dashboard**: A dedicated UI for managing orders and users.
  * [ ] **REST API**: Convert the backend to a JSON API for mobile support.

-----

**Author:** Phat  
**License:** This project is licensed under the MIT License - see the LICENSE file for details.

-----

*Found this project helpful? Give it a ⭐\!*
