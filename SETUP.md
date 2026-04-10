# IdeaSync - Setup & Installation Guide

Complete guide to setting up and running the IdeaSync platform locally.

## System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache, Nginx, or built-in PHP server
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)

Verify your PHP version:
```bash
php --version
```

## Step-by-Step Installation

### Step 1: Install/Start MySQL

**Windows (XAMPP/WAMP)**
- Start MySQL service from control panel

**macOS**
```bash
# Using Homebrew
brew services start mysql
```

**Linux**
```bash
sudo systemctl start mysql
```

### Step 2: Create Database

**Using MySQL Command Line:**
```bash
mysql -u root -p -e "CREATE DATABASE ideaSync_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Press Enter when prompted for password (default is empty for root).

**Using phpMyAdmin:**
1. Open `http://localhost/phpmyadmin`
2. Click "New" in left sidebar
3. Database name: `ideaSync_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 3: Import Database Schema

**Using MySQL Command Line:**
```bash
cd /path/to/Ideaspace
mysql -u root -p ideaSync_db < DATABASE_SCHEMA.sql
```

**Using phpMyAdmin:**
1. Select `ideaSync_db` database
2. Go to "Import" tab
3. Click "Choose File" and select `DATABASE_SCHEMA.sql`
4. Click "Import"

### Step 4: Verify Database Connection

Edit `/src/config/Database.php` and check credentials:

```php
private $host = 'localhost';      // Database host
private $db_name = 'ideaSync_db'; // Database name
private $user = 'root';           // MySQL user
private $password = '';           // MySQL password (empty by default)
private $port = 3306;             // MySQL port
```

Update if your MySQL setup is different.

### Step 5: Start the Application

**Option A: PHP Built-in Server (Recommended for Development)**

Navigate to project directory and run:

```bash
cd /path/to/Ideaspace/public
php -S localhost:8000
```

Then open browser: `http://localhost:8000`

**Option B: Apache/Nginx**

Copy project to web root:
- **XAMPP**: `C:\xampp\htdocs\Ideaspace`
- **WAMP**: `C:\wamp\www\Ideaspace`
- **Apache Linux**: `/var/www/html/Ideaspace`

Then access: `http://localhost/Ideaspace/public`

### Step 6: Setup Database & Demo Users

Open browser and visit: **`http://localhost:8000/setup.php`**

This page will:
- ✓ Check database connection
- ✓ Create demo users
- ✓ Show login credentials

### Step 7: Load Demo Ideas (Optional)

Visit: **`http://localhost:8000/seed.php`**

This will populate 5 sample ideas for testing collaboration features.

## Demo Credentials

After setup.php, use these to test the platform:

| Role | Roll Number | Password | Branch | Year |
|------|-------------|----------|--------|------|
| Visionary | `LID001` | `demo123456` | CSE | 3 |
| Builder | `LID002` | `demo123456` | CSE | 2 |

**⚠️ Important:** Change these passwords immediately in a production environment.

## Project Structure

```
Ideaspace/
├── public/                 # Web accessible folder
│   ├── index.php          # Main entry point
│   ├── setup.php          # Database setup
│   └── seed.php           # Demo data
├── src/
│   ├── config/
│   │   └── Database.php   # DB connection
│   ├── controllers/       # Business logic
│   │   ├── auth.php
│   │   └── ideas.php
│   ├── models/            # Database models
│   │   ├── User.php
│   │   ├── Idea.php
│   │   ├── Application.php
│   │   ├── BuilderRank.php
│   ├── views/             # HTML templates
│   │   ├── home.php
│   │   ├── dashboard.php
│   │   ├── auth/
│   │   ├── ideas/
│   │   └── admin/
│   └── assets/
│       └── css/
│           └── main.css   # Styles & design tokens
├── DATABASE_SCHEMA.sql    # Database tables
├── README.md              # Project docs
└── SETUP.md              # This file
```

## Common Issues & Solutions

### "Connection refused" or "Can't connect to MySQL"
- ✓ Ensure MySQL is running
- ✓ Check credentials in `Database.php`
- ✓ Verify database `ideaSync_db` exists

**Solution:**
```bash
mysql -u root -p -e "SHOW DATABASES;"
```

### "Table doesn't exist" error
- ✓ Check if DATABASE_SCHEMA.sql was imported
- ✓ Verify database contains tables

**Solution:**
```bash
mysql -u root -p ideaSync_db -e "SHOW TABLES;"
```

### "Access denied" error
- ✓ Check username/password in `Database.php`
- ✓ Verify user has required privileges

**Solution:**
```bash
mysql -u root -p
# Inside MySQL:
GRANT ALL PRIVILEGES ON ideaSync_db.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Page shows blank or 404
- ✓ Verify URL is correct
- ✓ Check PHP error logs
- ✓ Ensure `index.php` is in public folder

### File permissions issue (Linux/Mac)
```bash
chmod -R 755 /path/to/Ideaspace
chmod -R 777 /path/to/Ideaspace/src  # Writable by web server
```

## Testing the Application

### 1. Homepage
Visit: `http://localhost:8000`
- Should see landing page
- Navigation menu visible

### 2. User Registration
Visit: `http://localhost:8000/?page=register`
- Create new account with roll number starting with `LID`
- Example: `LID999`

### 3. User Login
Visit: `http://localhost:8000/?page=login`
- Use demo credentials or newly created account
- Should redirect to dashboard

### 4. Ideas Feed
Visit: `http://localhost:8000/?page=ideas`
- View all posted ideas
- Filter by domain

### 5. Post an Idea
From dashboard, click "Create Idea"
- Fill in title, description, domain, skills
- Submit to activate idea

## Database Backup

### Backup entire database:
```bash
mysqldump -u root -p ideaSync_db > backup.sql
```

### Restore from backup:
```bash
mysql -u root -p ideaSync_db < backup.sql
```

## Performance Tips

1. **Use PHP 8.0+** for better performance
2. **Enable MySQL query caching** for faster SELECT queries
3. **Add database indexes** for frequently searched fields
4. **Use UTF-8** for international character support (already configured)

## Security Checklist

- [ ] Change demo user passwords
- [ ] Update BASE_URL in index.php for production
- [ ] Enable HTTPS in production
- [ ] Set up firewall rules
- [ ] Regular database backups
- [ ] Monitor error logs
- [ ] Disable setup.php in production

## Production Deployment

### Before Going Live:
1. Change database credentials
2. Update BASE_URL to production domain
3. Disable setup.php and seed.php
4. Remove demo accounts
5. Enable HTTPS
6. Set proper file permissions
7. Configure web server properly
8. Set up error logging

### Recommended Hosting:
- **Cloud**: Railway, Heroku, DigitalOcean, AWS
- **Shared**: Bluehost, HostGator, SiteGround
- **VPS**: Linode, Vultr, DigitalOcean Droplets

## Next Steps

1. ✅ Setup complete!
2. 📝 Create your first idea
3. 👥 Apply to collaborate
4. 🏆 Build your builder rank
5. 📊 Check the admin dashboard

## Support

For issues:
- Check DATABASE_SCHEMA.sql for table structure
- Verify all PHP files are in correct locations
- Ensure PHP version supports used features
- Check error logs for detailed messages

## Resources

- [PHP Official Docs](https://php.net/docs)
- [MySQL Docs](https://dev.mysql.com/doc/)
- [IdeaSync README.md](./README.md) - Project overview
- [GitHub Issues](https://github.com) - For bugs/features

---

**Happy collaboration! 🚀**
