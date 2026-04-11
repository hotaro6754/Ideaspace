# IdeaSync Deployment Guide

## ⚠️ IMPORTANT: Platform Requirements

**This is a PHP application. Vercel does NOT support PHP.**

### Supported Deployment Platforms:
- ✅ **Railway** (RECOMMENDED - fully configured)
- ✅ DigitalOcean App Platform
- ✅ Heroku
- ✅ AWS App Runner
- ✅ Traditional VPS/Cloud Servers with PHP
- ❌ Vercel (Node.js only)
- ❌ Netlify (Node.js only)

---

## Deploy to Railway (Recommended)

### Prerequisites:
1. GitHub account (code is already here: https://github.com/hotaro6754/Ideaspace)
2. Railway account (free tier available)

### Steps:

1. **Go to Railway**
   - Visit https://railway.app
   - Sign in with GitHub
   - Create new project

2. **Connect Repository**
   - Click "Deploy from GitHub"
   - Select `hotaro6754/Ideaspace`
   - Authorize Railway

3. **Add MySQL Database**
   - Click "Add Service"
   - Select "MySQL"
   - Railway automatically creates database

4. **Configure Environment**
   - Railway reads `Procfile` and `nixpacks.toml`
   - Automatically sets up PHP 8.2
   - Database connection auto-configured

5. **Run Migrations**
   - SSH into Railway container
   - Run migration scripts in order:
     ```bash
     mysql < AGENT_SYSTEM_MIGRATION.sql
     mysql < PHASE_2_WORKFLOW_MIGRATION.sql
     mysql < PHASE_3_QUALITY_GATES_MIGRATION.sql
     mysql < PHASE_4_ANTIPATTERN_MIGRATION.sql
     mysql < PHASE_5_DESIGN_SYSTEM_MIGRATION.sql
     ```

6. **Done!**
   - Railway handles deployment automatically
   - App is live at provided URL
   - Auto-deploys on git push

---

## Alternative: Deploy to DigitalOcean

### Steps:

1. Create App Platform app
2. Connect GitHub repository
3. Create MySQL database
4. Deploy
5. SSH in and run migrations

---

## What NOT to Do

❌ **DO NOT** try to deploy to Vercel
❌ **DO NOT** try to deploy to Netlify
❌ **DO NOT** use `vercel.json` file

These platforms only support Node.js, not PHP.

---

## Verify Deployment

After deployment, test:

```
1. Open app URL
2. Sign up (create account)
3. Login
4. Select agent role
5. Create idea
6. Go through workflow phases
7. Check dashboard
```

If all works → Deployment successful! 🎉

---

## Common Issues

### "No PHP found"
→ Platform doesn't support PHP. Use Railway instead.

### "Database connection failed"
→ Run migrations in Railway MySQL:
   ```bash
   railway run mysql -u root -p < migrations.sql
   ```

### "Page not found (404)"
→ Procfile routing is incorrect. Check Procfile has:
   ```
   web: php -S 0.0.0.0:$PORT -t public public/router.php
   ```

### "CSRF token error"
→ Session may not be persisting. Restart app.

---

**Bottom Line: Use Railway. It's configured and ready. Everything works.**
