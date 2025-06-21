# Project Documentation: WakaziLink

1. Introduction
WakaziLink is a digital platform designed to bridge the gap between skilled informal workers and clients seeking their services. The platform connects artisans, technicians, and service providers with individuals and businesses in need of their expertise.

1.1 Project Vision
To create an efficient marketplace where skilled workers can showcase their talents and clients can easily find reliable services, fostering economic growth in local communities.

1.2 Key Objectives
Provide visibility for skilled informal workers

Simplify the process of finding reliable service providers

Facilitate direct communication between workers and clients

Build trust through ratings and verification systems

Support local economic development

1. Technology Stack
1.1 Frontend
HTML5, CSS3, JavaScript

Bootstrap 5 for responsive design

Font Awesome for icons

jQuery for DOM manipulation

2.2 Backend
PHP 8.0+ for server-side logic

MySQL database for data storage

Apache 2.4 web server

2.3 Key Libraries
PDO for database access

GD Library for image processing

Select2 for enhanced select inputs

1. System Architecture
text
Client Browser
    │
    ▼
Apache Web Server
    │
    ▼
PHP Application
    │
    ├─── Public Directory (Web Root)
    │    ├── index.php        # Entry point
    │    ├── register.php     # User registration
    │    ├── login.php        # User authentication
    │    ├── profile.php      # Profile management
    │    ├── search.php       # Worker search
    │    └── worker.php       # Worker profile display
    │
    ├─── Includes Directory
    │    ├── db_connect.php   # Database connection
    │    ├── auth.php         # Authentication functions
    │    ├── functions.php    # Utility functions
    │    ├── header.php       # Common header
    │    └── footer.php       # Common footer
    │
    └─── Admin Directory
         ├── dashboard.php    # Admin dashboard
         └── manage_users.php # User management
1. Directory Structure
text
├── wakazilink/
│   ├── public/
│   │   ├── admin/
│   │   │   ├── dashboard.php
│   │   │   └── manage_users.php
│   │   ├── css/
│   │   ├── js/
│   │   ├── uploads/
│   │   ├── index.php
│   │   ├── register.php
│   │   ├── login.php
│   │   ├── profile.php
│   │   ├── search.php
│   │   └── worker.php
│   ├── includes/
│   │   ├── db_connect.php
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── auth.php
│   │   └── functions.php
│   └── .htaccess
1. Database Schema
1.1 Users Table
sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('worker', 'client', 'admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
1.2 Worker Profiles Table
sql
CREATE TABLE worker_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skills TEXT NOT NULL,
    location VARCHAR(100) NOT NULL,
    description TEXT,
    profile_image VARCHAR(255),
    experience VARCHAR(100),
    is_approved BOOLEAN DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
1. Core Features
1.1 User Registration and Authentication
Role-based registration (Worker/Client)

Secure password hashing

Session-based authentication

Profile completion tracking

1.2 Worker Profile Management
Skills listing with tagging system

Location-based service area

Profile image upload with validation

Social media integration

Profile verification system

1.3 Search Functionality
Skill-based filtering

Location-based search

Rating sorting

Availability indicators

1.4 Admin Dashboard
User management

Worker profile approval

Platform analytics

Content moderation

1. Security Measures
Password Security

bcrypt hashing with cost factor 12

Minimum password length requirement (8 characters)

SQL Injection Prevention

PDO prepared statements

Parameterized queries

XSS Prevention

Input sanitization with htmlspecialchars()

Output escaping

Session Security

Regenerate session ID on login

Session timeout after 30 minutes of inactivity

File Upload Security

MIME type verification

File size restriction (5MB max)

Image dimension validation

1. Configuration Guide
1.1 .htaccess Configuration
apache
# Enable rewrite engine
RewriteEngine On

# Redirect to public directory
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "\.(env|htaccess|sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>
1.2 Database Configuration (db_connect.php)
php
<?php
$host = 'localhost';
$db   = 'wakazilink';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
1. Deployment Guide
1.1 Requirements
Apache 2.4+ with mod_rewrite enabled

PHP 8.0+ with PDO MySQL extension

MySQL 5.7+ or MariaDB 10.2+

Composer (for dependency management)

1.2 Installation Steps
Clone the repository

bash
git clone https://github.com/your-username/wakazilink.git
cd wakazilink
Configure environment

Create database and import schema

Update db_connect.php with your credentials

Set proper permissions for public/uploads directory

Configure virtual host (Apache)

apache
<VirtualHost *:80>
    ServerName wakazilink.local
    DocumentRoot "/path/to/wakazilink/public"
    <Directory "/path/to/wakazilink/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
Enable the site and restart Apache

bash
sudo a2ensite wakazilink.conf
sudo systemctl restart apache2
1. Troubleshooting Guide
1.1 Common Issues
Internal Server Error

Check Apache error logs: /var/log/apache2/error.log

Verify mod_rewrite is enabled: sudo a2enmod rewrite

Ensure .htaccess is properly configured

Database Connection Issues

Verify credentials in db_connect.php

Check MySQL user permissions

Ensure MySQL server is running

File Upload Issues

Check directory permissions: chmod -R 755 public/uploads

Verify PHP file upload settings:

ini
upload_max_filesize = 10M
post_max_size = 12M
Page Not Found (404)

Ensure .htaccess is present in root directory

Verify mod_rewrite is enabled

Check base URL configuration

1. API Reference (Future Implementation)
1.1 Worker Search API
http
GET /api/v1/workers?skill=plumbing&location=nairobi
Response

json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "skills": ["Plumbing", "Pipe Installation"],
      "location": "Nairobi West",
      "rating": 4.8,
      "profile_image": "/uploads/profiles/john-doe.jpg"
    }
  ]
}
1. Maintenance and Support
1.1 Backup Strategy
Daily database backups using mysqldump

Weekly full system backups

Off-site backup storage

1.2 Update Procedure
Create staging environment

Test updates thoroughly

Backup production database

Deploy updates during low-traffic hours

Monitor system after deployment

1.3 Support Channels
Email: support@wakazilink.com

Help Center: help.wakazilink.com

Community Forum: community.wakazilink.com

1. Future Roadmap
Q3 2024
Mobile application development (iOS/Android)

Payment integration for service booking

Scheduling system

Q4 2024
Worker verification badges

Premium membership options

Multi-language support

Q1 2025
AI-powered job matching

Training resources for workers

Service guarantee program

1. License Information
WakaziLink is released under the MIT License. This allows for:

Commercial use

Modification

Distribution

Private use

The only conditions are to include the original copyright notice and disclaimer in all copies.

1. Contact Information