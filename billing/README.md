# Billing System - Setup Guide

## Files Included
- `config.php`   — Database connection settings
- `index.php`    — New bill entry form
- `list.php`     — View all saved bills
- `view.php`     — View single bill detail
- `style.css`    — Stylesheet
- `db.sql`       — Database setup script

## Setup Steps

### 1. Database
Open phpMyAdmin (or MySQL CLI) and run:
```sql
source db.sql
```
Or copy-paste the contents of `db.sql` into your MySQL tool.

### 2. Configuration
Open `config.php` and update:
```php
define('DB_USER', 'root');   // your MySQL username
define('DB_PASS', '');       // your MySQL password
```

### 3. Deploy
Place all files in your web server folder:
- XAMPP: `C:/xampp/htdocs/billing/`
- WAMP:  `C:/wamp/www/billing/`
- Linux: `/var/www/html/billing/`

### 4. Open
Visit: `http://localhost/billing/`

## Features
- Auto-generated Bill No (BILL-0001, BILL-0002 ...)
- Add/remove multiple items dynamically
- Auto-calculates: Amount, Item Total, GST Amount, Grand Total
- Saves bill + all items to MySQL
- List view of all bills
- Detailed bill view with print support
- Input validation (required fields, numeric checks)
