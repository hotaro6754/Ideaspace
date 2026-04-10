# IdeaSync Production Setup Guide

## Overview
This guide covers the complete setup and deployment of the IdeaSync platform with all production-ready features including email verification, password reset, channel messaging, idea comments, event management, and admin controls.

## Prerequisites
- PHP 7.4+
- MySQL 5.7+ or MariaDB
- Composer (optional, for dependency management)
- Web server (Apache/Nginx)
- SMTP for email sending (or use PHP's mail function)

## Installation Steps

### 1. Database Setup

Run the migration script to create all required tables and add new columns:

```bash
php migrate.php
```

This will:
- Create all new tables (channels, comments, events, etc.)
- Add security columns to users table (email_verified, is_suspended, etc.)
- Create all necessary indexes for performance
- Set up default values and constraints

**Note:** The script is idempotent and safe to run multiple times.

### 2. Configuration

Update your environment configuration in `src/config/Database.php`:

```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'ideaspace_user');
define('DB_PASS', 'strong_password');
define('DB_NAME', 'ideaspace_prod');

// Base URL for email links
define('BASE_URL', 'https://ideasync.yourdomain.com');
```

Update email configuration in `src/services/EmailService.php`:

```php
private static $from = 'noreply@ideasync.yourdomain.com';
private static $site_name = 'IdeaSync';
private static $base_url = 'https://ideasync.yourdomain.com';
```

### 3. Security Setup

Ensure these security measures are in place:

#### HTTPS
- Configure SSL/TLS on your web server
- Update BASE_URL to use https://
- Force HTTPS redirect in your main index.php

#### Session Configuration
```php
// In your session initialization code
session_start();
ini_set('session.cookie_secure', 1);      // HTTPS only
ini_set('session.cookie_httponly', 1);    // No JavaScript access
ini_set('session.cookie_samesite', 'Strict');  // CSRF protection
ini_set('session.gc_maxlifetime', 3600);  // 1 hour timeout
```

#### File Permissions
```bash
chmod 755 src/
chmod 755 public/
chmod 755 logs/
chmod 644 .htaccess
# Make temp dirs writable
mkdir -p logs/
chmod 777 logs/
```

### 4. Email Service Setup

For production emails, configure an SMTP provider:

#### Option A: Using SendGrid
```php
// Update EmailService with SendGrid API
private static function send($to, $subject, $html_message) {
    // Use SendGrid instead of mail()
}
```

#### Option B: Using mailgun
Similar to SendGrid, update the send() method.

#### Option C: Using system mail()
Ensure your server has a mail server configured:
```bash
sudo apt-get install postfix  # On Ubuntu/Debian
```

### 5. Directory Structure Verification

Ensure these directories exist and are writable:

```
/workspaces/Ideaspace/
├── logs/                    # For security and error logs
├── uploads/                 # For file uploads
├── src/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   ├── services/
│   └── helpers/
├── public/                  # Web root
└── migrate.php             # Migration script
```

## Core Features Implementation

### 1. Email Verification
- Users must verify their email before logging in
- Verification link sent during registration
- Token expires in 24 hours
- Can resend verification email

**User Flow:**
1. Register → Email sent
2. Click verification link → Email verified
3. Can now login

### 2. Password Reset
- Users can request password reset via email
- Reset link valid for 2 hours
- Strong password requirements enforced
- All sessions invalidated after reset

**User Flow:**
1. Forgot password page
2. Enter email → Reset link sent
3. Click link → Create new password
4. Redirected to login

### 3. Security Features

#### Rate Limiting
- Login attempts: 5 attempts per hour per IP
- Password reset: 3 attempts per hour per email
- Prevents brute force attacks

#### CSRF Protection
- All forms include CSRF token
- Token validated on all POST/PUT/DELETE
- Token expires after 1 hour

#### Logging & Audit Trail
- All authentication events logged
- Admin can view audit trail
- IP addresses and user agents tracked

### 4. Channel Messaging (Team-Based)
- Create channels within collaborations
- Automatic member assignment
- Message reactions (emoji)
- Unread message tracking
- Soft delete for messages

**Usage:**
```javascript
// Create channel
POST /src/controllers/channels.php?action=create
{
    "collaboration_id": 1,
    "name": "General Discussion",
    "type": "general"
}

// Add message
POST /src/controllers/channels.php?action=addMessage
{
    "channel_id": 1,
    "content": "Hello team!",
    "csrf_token": "..."
}
```

### 5. Idea Comments with Threading
- Nested replies support
- Like system for comments
- Edit and delete (soft delete)
- Automatic notifications

**Usage:**
```javascript
// Create comment
POST /src/controllers/comments.php?action=create
{
    "idea_id": 1,
    "content": "Great idea!",
    "parent_comment_id": null  // For replies
}

// Get comments
GET /src/controllers/comments.php?action=get&idea_id=1
```

### 6. Event Management
- Create events for collaborations
- RSVP tracking (attending, maybe, not attending)
- Event capacity management
- Event statistics

**Usage:**
```javascript
// Create event
POST /src/controllers/events.php?action=create
{
    "title": "Team Standup",
    "start_time": "2026-04-15 10:00:00",
    "end_time": "2026-04-15 10:30:00",
    "event_type": "standup",
    "collaboration_id": 1
}

// RSVP to event
POST /src/controllers/events.php?action=rsvp
{
    "event_id": 1,
    "status": "attending"
}
```

### 7. Admin Dashboard
- User management (suspend, deactivate)
- Content moderation (review reports)
- Audit trail viewing
- System statistics

**Admin Functions:**
- Suspend users (temporarily or permanently)
- Deactivate user accounts
- Review and resolve content reports
- View security audit trail

## Testing

### Test Email Verification
1. Register new account
2. Check spam folder for verification email
3. Click verification link
4. Verify email confirmed
5. Login with verified account

### Test Password Reset
1. Login as test user
2. Click "Forgot Password"
3. Enter email
4. Click reset link in email
5. Create new password
6. Login with new password

### Test Channel Messaging
1. Create collaboration
2. Create channel in collaboration
3. Send messages in channel
4. Add emoji reactions
5. Verify message unread count

### Test Event Management
1. Create event in collaboration
2. Test RSVP functionality
3. Test capacity limits
4. View event statistics

### Test Admin Functions
1. Login as admin user
2. Access admin dashboard
3. View user list
4. Suspend test user
5. Review suspension in audit trail

## Performance Optimization

### Database Optimization
```sql
-- Analyze tables for query optimization
ANALYZE TABLE users;
ANALYZE TABLE ideas;
ANALYZE TABLE collaborations;
-- etc.
```

### Caching Strategy
- Use Redis for sessions (optional)
- Cache frequently accessed user preferences
- Cache recent activity logs

### Query Optimization
- All controllers use parameterized queries (SQL injection prevention)
- Proper indexes on foreign keys and frequently filtered columns
- Pagination implemented (limit 50-100 per request)

## Monitoring & Logging

### Error Logging
Configure in your PHP.ini:
```ini
error_log = /var/log/php_errors.log
log_errors = On
display_errors = Off  # Production
```

### Security Logging
All security events logged to database and file:
```
logs/security.log
```

Monitor these events:
- Failed login attempts
- Password reset requests
- Suspended users
- Content reports

## Backup Strategy

### Database Backups
```bash
# Daily backup
mysqldump -u root -p ideaspace_prod > backup_$(date +%Y%m%d).sql

# Set up automated backups via cron
0 2 * * * mysqldump -u root -p ideaspace_prod | gzip > /backups/ideaspace_$(date +\%Y\%m\%d_\%H\%M\%S).sql.gz
```

### File Backups
```bash
# Backup uploads directory
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/

# Backup configuration
tar -czf config_backup_$(date +%Y%m%d).tar.gz src/config/
```

## Deployment Checklist

- [ ] Database migrations run successfully
- [ ] All environment variables configured
- [ ] HTTPS enabled and SSL certificates valid
- [ ] Email service configured and tested
- [ ] File upload directories exist and writable
- [ ] Log directories exist and writable
- [ ] Security headers configured
- [ ] Rate limiting working
- [ ] CSRF tokens being generated and validated
- [ ] Email verification working
- [ ] Password reset working
- [ ] Channels and messaging working
- [ ] Comments and threading working
- [ ] Events and RSVPs working
- [ ] Admin dashboard accessible only to admins
- [ ] Backups configured
- [ ] Monitoring configured

## Troubleshooting

### Email not sending
1. Check SMTP configuration
2. Verify email service credentials
3. Check logs/security.log for errors
4. Test mail() function: `php -r "mail('test@test.com', 'Test', 'Test');"`

### Registration stuck on email verification
1. Check email_verifications table
2. Verify token expiration logic
3. Check EmailService logs
4. Resend verification email

### Rate limiting too strict/loose
1. Adjust `MAX_ATTEMPTS` in RateLimit model
2. Adjust `WINDOW_MINUTES` as needed
3. Clear rate_limits table to reset

### Database connection errors
1. Verify credentials in Database.php
2. Check MySQL is running
3. Verify user has correct permissions
4. Check firewall rules

## Support & Updates

For issues or questions:
1. Check ANALYSIS_EXECUTIVE_SUMMARY.md
2. Review CRITICAL_FEATURES_IMPLEMENTATION.md
3. Check git logs for recent changes
4. Review security.log for error details

## Next Steps

1. **Real-time Features** (WebSocket or Server-Sent Events)
   - Live message updates in channels
   - Real-time notification delivery
   - Typing indicators

2. **Advanced Analytics**
   - User engagement metrics
   - Collaboration success rates
   - Event attendance analytics

3. **Integrations**
   - GitHub webhook integration
   - Slack notifications
   - Calendar integration

4. **Performance Enhancements**
   - Redis caching layer
   - CDN for static assets
   - Database query optimization

---

**Status:** Production Ready  
**Last Updated:** 2026-04-10  
**Next Review:** 2026-04-17
