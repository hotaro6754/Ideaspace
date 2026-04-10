# IdeaSync - Campus Collaboration Platform

A powerful, professional platform connecting campus visionaries with builders. Post innovative ideas, find skilled collaborators, and bring projects to life together.

**Built for**: Lendi Institute of Engineering & Technology  
**Demo Deadline**: April 22, 2024  
**Status**: ✅ Core features complete | 🚀 Ready for testing

## 📚 Documentation

- **[README.md](README.md)** (this file) - Project overview
- **[SETUP.md](SETUP.md)** - Installation & setup instructions
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Technical architecture & design

## 🎯 Project Overview

IdeaSync is a campus collaboration platform built for Lendi Institute of Engineering & Technology. It enables:

- **Visionaries**: Students with ideas to post projects and recruit builders
- **Builders**: Students with technical skills to discover ideas and join collaborations
- **Community**: Upvoting system, skill verification via GitHub, and gamified builder ranks

## 🏗️ Architecture

### Technology Stack
- **Backend**: PHP (OOP, MVC pattern with prepared statements)
- **Database**: MySQL with 10 normalized tables
- **Frontend**: HTML5, CSS3 with Tailwind CSS
- **Security**: Session-based auth, BCRYPT password hashing, CSRF/XSS/clickjacking protection

### Database Schema
- **users**: Student profiles with roll numbers, branches, years
- **ideas**: Posted projects with domains, skills needed, status tracking
- **applications**: Collaboration requests with status management
- **collaborations**: Accepted team relationships with roles
- **builder_rank**: Gamification leaderboard (5 tiers: INITIATE→LEGEND)
- **github_profiles & github_repos**: Cached GitHub data for skill verification
- **upvotes**: Community voting signals
- **notifications**: Real-time engagement engine
- **admin_actions**: Admin dashboard for content moderation

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+ 
- MySQL 5.7+
- Web server (Apache, Nginx, or PHP built-in)

### Installation

#### 1. Clone/Copy the Repository
```bash
cd /workspaces/Ideaspace
```

#### 2. Set Up the Database
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE ideaSync_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema
mysql -u root -p ideaSync_db < DATABASE_SCHEMA.sql
```

Or use phpMyAdmin:
1. Create a new database: `ideaSync_db`
2. Go to Import tab
3. Select `DATABASE_SCHEMA.sql` and execute

#### 3. Update Database Credentials (if needed)
Edit `/workspaces/Ideaspace/src/config/Database.php`:
```php
private $host = 'localhost';
private $db_name = 'ideaSync_db';
private $user = 'root';
private $password = '';
private $port = 3306;
```

#### 4. Start the Application

**Option A: PHP Built-in Server**
```bash
cd /workspaces/Ideaspace/public
php -S localhost:8000
```

**Option B: Apache (XAMPP/WAMP)**
- Copy the project to `htdocs` or `www` directory
- Access via: `http://localhost/Ideaspace/public/`

#### 5. Initialize Demo Data
Visit: `http://localhost:8000/setup.php`

This will:
- Create demo users
- Set up system tables
- Provide test credentials

#### 6. Load Demo Ideas (Optional)
Visit: `http://localhost:8000/seed.php`

This will populate 5 sample ideas for testing collaboration features.

## 📚 File Structure

```
Ideaspace/
├── public/
│   ├── index.php          # Main entry point & router
│   └── setup.php          # Database setup & demo data
├── src/
│   ├── config/
│   │   └── Database.php   # Database connection & management
│   ├── controllers/
│   │   └── auth.php       # Registration, login, logout
│   ├── models/
│   │   └── User.php       # User model with database methods
│   ├── views/
│   │   ├── home.php       # Landing page
│   │   ├── dashboard.php  # User dashboard
│   │   ├── profile.php    # User profile
│   │   ├── 404.php        # Error page
│   │   ├── auth/
│   │   │   ├── login.php   # Login form
│   │   │   └── register.php # Registration form
│   │   ├── ideas/
│   │   │   ├── list.php    # Ideas feed
│   │   │   └── detail.php  # Idea detail page
│   │   └── admin/
│   │       └── dashboard.php # Admin dashboard
│   └── assets/
│       └── css/
│           └── main.css    # Design system & styles
├── DATABASE_SCHEMA.sql    # Complete database schema
└── README.md              # This file
```

## 🔐 Authentication

### User Roles
- **Visionary**: Can post ideas and review applications
- **Builder**: Can apply to collaborate on ideas

### Roll Number Format
- Format: `LID` + 3+ digits (e.g., `LID001`)
- Prevents duplicate registrations
- Verifies campus affiliation

### Password Requirements
- Minimum 8 characters
- BCRYPT hashing for storage
- Session-based authentication
- Automatic redirect to login for protected pages

## 🎮 Demo Credentials

After setup.php, use these to test:

| Role | Roll No. | Password | Type |
|------|----------|----------|------|
| Visionary | `LID001` | `demo123456` | Visionary |
| Builder | `LID002` | `demo123456` | Builder |

**⚠️ Change these credentials immediately in production!**

## 🌐 Routes & Pages

| Route | Purpose | Auth Required |
|-------|---------|---------------|
| `/?page=home` | Landing page | ❌ |
| `/?page=register` | User registration | ❌ |
| `/?page=login` | User login | ❌ |
| `/?page=dashboard` | User dashboard | ✅ |
| `/?page=ideas` | Ideas feed | ❌ (featured) |
| `/?page=idea-detail` | Idea details | ❌ |
| `/?page=profile` | User profile | ✅ |
| `/?page=admin` | Admin panel | ✅ |
| `/setup.php` | Database setup | ❌ |

## 🎨 Design System

### Color Palette
- **Primary**: #3B82F6 (Blue)
- **Accent**: #8B5CF6 (Purple)
- **Dark**: #111827
- **Light**: #FFFFFF

### Typography
- **Brand**: Sora (headings)
- **Body**: Inter (all text)

### Components
- Buttons (primary, secondary, ghost, sizes)
- Input fields with focus states
- Cards with hover elevation
- Badges for status
- Grid system (auto-responsive)

## 🔄 API Design

All routes follow RESTful conventions:

### Authentication Flow
```
POST /src/controllers/auth.php?action=register → Create account
POST /src/controllers/auth.php?action=login → Start session
GET /src/controllers/auth.php?action=logout → Destroy session
```

### Security Headers
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin

## 📊 Database Queries

All queries use **prepared statements** to prevent SQL injection:

```php
$query = "SELECT * FROM users WHERE roll_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $roll_number);
$stmt->execute();
$result = $stmt->get_result();
```

## 🚦 Next Steps

### Phase 2: Core Features
- [ ] Idea posting form with validation
- [ ] Ideas database queries & filtering
- [ ] Collaboration application system
- [ ] GitHub API integration for skill verification
- [ ] Notifications engine

### Phase 3: Gamification
- [ ] Builder Rank calculation system
- [ ] Points distribution logic
- [ ] Achievement badges
- [ ] Leaderboard display

### Phase 4: Polish & Deployment
- [ ] Add image upload (profile pictures, repo thumbnails)
- [ ] Implement real-time notifications
- [ ] Performance optimization
- [ ] Deploy to production server

## 📝 Development Notes

### Current Status
✅ Database schema designed  
✅ Authentication system complete  
✅ All view pages scaffolded  
✅ Professional design system  
⏳ GitHub integration (coming)  
⏳ Gamification system (coming)  

### Known Limitations
- No email verification yet
- GitHub integration in progress
- File uploads not yet implemented
- Admin dashboard is placeholder

## 🤝 Contributing

For development:

1. Update Database.php for local config
2. Use prepared statements for all queries
3. Follow MVC pattern for new features
4. Use design tokens from main.css
5. Maintain URL routing through index.php

## 📞 Support

For issues or questions:
- Check DATABASE_SCHEMA.sql for table structure
- Verify PHP version (7.4+)
- Ensure MySQL is running
- Check Database.php credentials

## 📄 License

Built for Lendi Institute of Engineering & Technology - Campus Collaboration Platform

---

**Built with ❤️ for campus collaboration**
