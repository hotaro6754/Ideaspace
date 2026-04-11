# IdeaSync - Campus Collaboration Platform Setup Guide

## ✅ What's Been Done

- ✓ Installed MySQL 8.0 database server
- ✓ Created `ideaspace_dev` database with complete schema
- ✓ Set up database user with proper credentials
- ✓ Installed PHP 8.3 with MySQL/MySQLi support
- ✓ Configured local `.env` file for development
- ✓ Seeded database with test data (4 users, 3 ideas, sample collaborations)
- ✓ Fixed Database.php to use TCP/IP connection (127.0.0.1)
- ✓ Created startup script for easy launching

## 🚀 Quick Start

### Option 1: Using the Start Script (Easiest)
```bash
cd /workspaces/Ideaspace
./start.sh
```

This will:
1. Start MySQL service
2. Verify the database exists
3. Launch PHP development server on port 8080

### Option 2: Manual Setup
```bash
# Start MySQL
sudo service mysql start

# Start PHP server
cd /workspaces/Ideaspace/public
/usr/bin/php8.3 -S localhost:8080
```

## 📱 Accessing the Application

Once the server is running, visit:
- **Home Page**: http://localhost:8080/?page=home
- **Dashboard**: http://localhost:8080/?page=dashboard  
- **Ideas List**: http://localhost:8080/?page=ideas

## 🔐 Test Credentials

### Users Available:
1. **Harshith Gangaraju** (Visionary)
   - Email: harshith@example.com
   - Password: (same as registration password)

2. **Priya Sharma** (Builder)
   - Email: priya@example.com

3. **Arjun Kumar** (Builder) 
   - Email: arjun@example.com

4. **Sofia Patel** (Visionary/Architect)
   - Email: sofia@example.com

## 📊 Database Information

- **Host**: localhost (127.0.0.1:3306)
- **Database**: ideaspace_dev
- **Username**: ideaspace_user
- **Password**: IdeaSpace@Local2024
- **Port**: 3306

### Available Tables:
- users
- ideas
- applications
- collaborations
- notifications
- messages
- builder_rank
- admin_actions
- github_profiles
- github_repos
- file_uploads
- upvotes

## 🔧 Environment Configuration

The `.env` file in the root directory contains:
```
DB_HOST=localhost
DB_NAME=ideaspace_dev
DB_USER=ideaspace_user
DB_PASSWORD=IdeaSpace@Local2024
DB_PORT=3306
APP_ENV=development
APP_DEBUG=true
```

## 📁 Project Structure

```
/workspaces/Ideaspace/
├── public/                  # Publicly accessible files
│   ├── index.php           # Main entry point
│   ├── assets/             # CSS, JS, images
│   └── ...
├── src/
│   ├── config/             # Database and Env configuration
│   │   ├── Database.php
│   │   └── Env.php
│   ├── views/              # PHP view templates
│   ├── controllers/        # Business logic
│   └── classes/            # Utility classes
├── .env                     # Environment variables (local dev)
├── DATABASE_SCHEMA.sql      # Full database schema
├── setup-db.sh             # Database setup script
├── start.sh                # Quick start script
└── README.md               # This file
```

## 🛠️ Common Tasks

### Reset Database
```bash
sudo mysql -e "DROP DATABASE IF EXISTS ideaspace_dev;"
./setup-db.sh
```

### View Database Contents
```bash
sudo mysql -u ideaspace_user -pIdeaSpace@Local2024 ideaspace_dev
```

### Test Database Connection
```bash
/usr/bin/php8.3 test-db-connection.php
```

### Check MySQL Status
```bash
sudo service mysql status
```

### Stop MySQL
```bash
sudo service mysql stop
```

## 🎯 Features

The IdeaSync platform includes:

1. **User Management**
   - Student registration (by roll number)
   - Role-based access (Visionary/Builder)
   - Profile management

2. **Idea Management**
   - Post project ideas
   - Browse and search ideas
   - Apply for collaborations
   - Track idea status

3. **Collaboration System**
   - Accept/reject team members
   - Track collaborations
   - Assign roles

4. **Gamification**
   - Builder ranks (INITIATE → LEGEND)
   - Points system
   - Achievement tracking

5. **Communication**
   - Direct messaging
   - Notifications
   - Activity feeds

6. **Admin Panel**
   - User management
   - Idea moderation
   - Usage reports

## 📝 Notes

- The application uses MySQLi for database connections
- All database connections use TCP/IP (127.0.0.1) to avoid socket permission issues
- Session management is configured with secure flags for production
- Development mode is enabled (`APP_DEBUG=true`)

## ⚠️ Important

- Never commit `.env` file (already in .gitignore)
- This setup is for LOCAL DEVELOPMENT ONLY
- For production deployment, use stronger passwords and proper SSL
- Update credentials in `.env` for any non-local deployments

## 🐛 Troubleshooting

### "Can't connect to MySQL"
- Ensure MySQL is running: `sudo service mysql status`
- Check if database exists: `sudo mysql -e "SHOW DATABASES;"`
- Verify ports are not blocked

### "PHP extension not found"
- Make sure using system PHP: `/usr/bin/php8.3 --version`
- Don't use the custom PHP at `/usr/local/php/`

### "Port 8080 already in use"
- Use different port: `php -S localhost:9000` (instead of 8080)
- Or kill existing process: `lsof -i :8080`

---

**Ready to go!** Run `./start.sh` to begin development. 🎉
