# IdeaSync Deployment Guide

**Current Status:** Production-Ready PHP Application  
**Last Updated:** 2026-04-10

---

## ⚠️ IMPORTANT: About Vercel & PHP

Vercel **does not natively support PHP** traditional web hosting. Vercel is a Node.js-focused platform that specializes in serverless functions.

**Your Options:**

1. **Recommended: Deploy to PHP-Compatible Hosting** ✅ (Keep as-is)
2. **Rewrite to Node.js/Express** ❌ (Major effort, not recommended)
3. **Use Docker on Vercel** ⏳ (Complex, limited support)

---

## OPTION 1: Recommended Deployment Solutions ✅

### A. Railway.app (EASIEST - RECOMMENDED)

**Why Railway?**
- ✅ Native PHP support
- ✅ Free tier available
- ✅ PostgreSQL included
- ✅ Easy GitHub integration
- ✅ 1-click deployments

**Setup:**

```bash
# 1. Create account at railway.app

# 2. Connect GitHub repo

# 3. Railway detects PHP automatically

# 4. Add environment variables in Railway dashboard:
DB_HOST=your_rail_db_host
DB_NAME=ideasync
DB_USER=postgres
DB_PASSWORD=auto_generated
# ... etc

# 5. Deploy button - done!
```

**Cost:** $5/month or pay-as-you-go  
**Deploy Time:** 2-5 minutes

### B. Heroku with PHP Buildback

**Setup:**

```bash
# 1. Install Heroku CLI
curl https://cli-assets.heroku.com/install.sh | sh

# 2. Login
heroku login

# 3. Create app
heroku create your-ideasync-app

# 4. Add MySQL add-on
heroku addons:create cleardb:ignite

# 5. Set environment variables
heroku config:set DB_HOST=your_db_host
heroku config:set DB_NAME=your_db_name
# ... etc

# 6. Deploy
git push heroku main

# 7. Run migrations
heroku run php migrate.php
```

**Cost:** Free tier available, paid plans from $7/month

### C. Hostinger / Bluehost / DigitalOcean

**DigitalOcean (Recommended for control):**

```bash
# 1. Create Droplet (Ubuntu 20.04, 2GB RAM = $6/month)
# 2. SSH into server
ssh root@your_ip

# 3. Install dependencies
apt-get update
apt-get install -y nginx php8.1-fpm php8.1-mysql mysql-server php8.1-curl php8.1-json

# 4. Clone repository
cd /var/www
git clone your-repo ideasync
cd ideasync

# 5. Copy .env.example to .env
cp .env.example .env
nano .env  # Edit with actual credentials

# 6. Configure Nginx
nano /etc/nginx/sites-available/ideasync
# Add server config

# 7. Enable site
ln -s /etc/nginx/sites-available/ideasync /etc/nginx/sites-enabled/

# 8. Restart Nginx
systemctl restart nginx

# 9. Run migrations
php migrate.php

# 10. Set permissions
chown -R www-data:www-data /var/www/ideasync
chmod -R 755 /var/www/ideasync
chmod -R 777 /var/www/ideasync/logs
```

**Cost:** $4-6/month  
**Control:** Full server access

---

## OPTION 2: Docker on Vercel (Advanced)

If you really want Vercel, you can containerize the app:

### Dockerfile

```dockerfile
FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    mysql-client \
    libz-dev \
    libmemcached-dev \
    zlib1g-dev \
    curl \
    git

RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    json

COPY . /var/www/html/

WORKDIR /var/www/html/

RUN composer install --no-dev --optimize-autoloader 2>/dev/null || true

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]
```

### vercel.json

```json
{
  "functions": {
    "api/**/*.php": {
      "runtime": "vercel-php@0.5.1"
    }
  },
  "env": {
    "DB_HOST": "@db_host",
    "DB_NAME": "@db_name",
    "DB_USER": "@db_user",
    "DB_PASSWORD": "@db_password"
  }
}
```

⚠️ **Note:** This requires restructuring the app significantly and has limitations.

---

## RECOMMENDED: Railway Quick Start

Here's the fastest way to deploy to production:

### Step-by-Step:

```bash
# 1. Commit all fixes
git add -A
git commit -m "Security fixes and production build"
git push origin main

# 2. Go to railway.app and sign up

# 3. Create new project → Deploy from GitHub

# 4. Select your ideaspace repository

# 5. Railway auto-detects PHP, creates database

# 6. In Railway dashboard, set environment variables:
# Variables → Add Variable
# Key: DB_HOST, Value: (Railway will provide)
# Key: DB_USER, Value: (Railway will provide)
# Key: DB_PASSWORD, Value: (Railway will provide)
# Key: DB_NAME, Value: ideasync
# Key: APP_ENV, Value: production
# Key: APP_URL, Value: your-domain.railway.app
# ... rest of .env variables

# 7. Wait for deployment (~2-3 minutes)

# 8. Go to URL → Run initial checks

# 9. SSH into Railway to run migrations:
railway login
railway link  # Follow prompts
railway shell
php migrate.php
exit
```

**Result:** Your app is live at `https://<app-name>.railway.app`

---

## CUSTOM DOMAIN SETUP

If you have a domain (e.g., ideasync.yourdomain.com):

### Railway:
```
1. In Railway dashboard → Settings
2. Domain → Add custom domain
3. ideasync.yourdomain.com
4. Point your DNS to Railway's servers (they'll show CNAME)
```

### DigitalOcean:
```
1. Update DNS A record to your droplet IP
2. Update SSL certificate (certbot):
   certbot certonly --webroot -w /var/www/ideasync -d ideasync.yourdomain.com
3. Configure Nginx with SSL
```

---

## POST-DEPLOYMENT CHECKLIST

- [ ] App loads without errors
- [ ] Registration works
- [ ] Email verification works (check logs)
- [ ] Password reset works
- [ ] Login works
- [ ] Channels work
- [ ] Comments work
- [ ] Events work
- [ ] Admin dashboard works
- [ ] File uploads work
- [ ] HTTPS working (not mixed content)
- [ ] Security headers present (`curl -i` to check)
- [ ] Database migrations ran
- [ ] Logs accessible and not leaking info
- [ ] Backups configured

---

## MONITORING & LOGS

### Railway:
```
1. Dashboard → Logs tab
2. See real-time logs
3. Check for errors
4. View database queries
```

### DigitalOcean:
```bash
# View PHP errors
tail -f /var/log/nginx/error.log

# View app logs
tail -f /var/www/ideasync/logs/app.log

# Check security logs
tail -f /var/www/ideasync/logs/security.log

# Check database
mysql -u root -p
use ideasync;
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM ideas;
```

---

## PERFORMANCE OPTIMIZATION

### After Deployment:

#### 1. Enable Caching
```php
// Add to Database.php
ini_set('mysqli.cache_size', '2000');
```

#### 2. Database Optimization
```sql
-- Run on production database
ANALYZE TABLE users;
ANALYZE TABLE ideas;
ANALYZE TABLE collaborations;
OPTIMIZE TABLE users;
```

#### 3. Configure CDN (Optional)
```
1. Set up Cloudflare (free tier)
2. Point domain to Cloudflare
3. Enable caching for static assets
```

#### 4. Enable Gzip Compression
```nginx
# In Nginx config
gzip on;
gzip_types text/plain text/css application/json application/javascript;
gzip_disable "msie6";
```

---

## BACKUP & DISASTER RECOVERY

### Automated Backups:

#### Railway:
```
1. Dashboard → Backup settings
2. Enable daily backups
3. Backups stored and restorable
```

#### DigitalOcean:
```bash
# Daily backup script (cron job)
# Add to /root/backup.sh:

#!/bin/bash
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Database backup
mysqldump -u root -p$DB_PASSWORD ideasync | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Files backup
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/ideasync/uploads/

# Delete old backups (keep 30 days)
find $BACKUP_DIR -type f -mtime +30 -delete

# Then add to crontab:
crontab -e
# Add: 0 2 * * * /root/backup.sh
```

---

## TESTING THE PRODUCTION DEPLOYMENT

### Security Test:
```bash
# Check HTTPS
curl -I https://your-domain.com
# Should show: Strict-Transport-Security

# Check CSP headers
curl -I https://your-domain.com | grep -i content-security

# Test CSRF protection
curl -X POST https://your-domain.com/api/file-upload \
  -F "file=@test.pdf"
# Should fail: "CSRF token validation failed"
```

### Functionality Test:
```bash
# Register new user
# Login
# Create idea
# Create comment
# Upload file
# Create event
# Access admin dashboard
```

---

## SCALING (Future)

If you grow to many users:

1. **Add Redis** for caching
2. **Add Database Replicas** for read scaling
3. **Add CDN** for static assets
4. **Implement ElasticSearch** for search scaling
5. **Add Message Queue** for async jobs
6. **Use Load Balancer** for multiple app instances

---

## SUPPORT & TROUBLESHOOTING

### Common Issues:

**"Database connection failed"**
- Check ENV variables in dashboard
- Verify database is running
- Check credentials

**"CORS errors"**
- Update headers in main index.php
- Check allowed origins

**"Files not uploading"**
- Check permissions: `chmod 777 /var/www/ideasync/uploads`
- Verify upload_max_filesize in PHP config

**"Emails not sending"**
- Configure real SMTP (SendGrid, Mailgun)
- Check EMAIL_* env variables
- Look at logs for errors

---

## FINAL DEPLOYMENT SUMMARY

| Solution | Cost | Setup Time | PHP Support | Recommendation |
|----------|------|-----------|-------------|---|
| Railway | $5/mo | 5 min | ✅ Native | ⭐⭐⭐⭐⭐ |
| DigitalOcean | $6/mo | 20 min | ✅ Native | ⭐⭐⭐⭐ |
| Heroku | Free-$15/mo | 10 min | ✅ Via buildpack | ⭐⭐⭐ |
| Vercel Docker | Varies | 30 min | ⚠️ Limited | ⭐⭐ |

**🎯 RECOMMENDATION: Use Railway**
- Easiest setup
- Best value
- Native PHP support
- Great for this type of app

---

**Next Step:** Choose your hosting provider and deploy! 🚀

