# 🚀 Railway Deployment Guide for IdeaSpace

## Quick Start (Automated)

### Step 1: Create Railway Account
Go to https://railway.app and sign up (login with GitHub recommended)

### Step 2: Connect Your GitHub Repository
1. Visit https://railway.app/dashboard
2. Click **"New Project"**
3. Select **"Deploy from GitHub"**
4. Authorize Railway to access your GitHub
5. Select this repository: `Ideaspace`
6. Click **"Deploy"**

Railway will automatically:
- Detect the Dockerfile
- Build the PHP application
- Create a deployment

### Step 3: Set Up Database

Railway will detect the Dockerfile and create a PHP service. Now add MySQL:

1. In Railway dashboard, click **"+ Add Services"**
2. Select **"MySQL"**
3. Railway creates a MySQL instance automatically
4. Get the connection details from the MySQL service settings

### Step 4: Configure Environment Variables

In Railway dashboard:
1. Click on your **PHP service**
2. Go to **"Variables"** tab
3. Add these variables:

```
DB_HOST=mysql.railway.internal
DB_NAME=railway
DB_USER=root
DB_PASSWORD=[from MySQL service details]
DB_PORT=3306
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
```

### Step 5: Run Database Migrations

After first deployment:
1. Connect to Railway via SSH or run migration script
2. Execute migrations:
```bash
mysql -h mysql.railway.internal -u root -p[PASSWORD] railway < migration/add-recommendation-system.sql
mysql -h mysql.railway.internal -u root -p[PASSWORD] railway < DATABASE_SCHEMA.sql
mysql -h mysql.railway.internal -u root -p[PASSWORD] railway < seed-data.sql
```

Alternatively, use Railway's exec feature:
```bash
railway exec mysql -h mysql.railway.internal -u root -p[PASSWORD] railway < migration/add-recommendation-system.sql
```

### Step 6: Get Your Live URL

After deployment completes:
1. Railway automatically assigns a URL: `https://ideasync-[random].railway.app`
2. Your app is live!
3. Visit: `https://ideasync-[random].railway.app/?page=home`

---

## Custom Domain Setup (Optional)

To use your own domain (e.g., ideaspace.codecollege.edu):

1. In Railway dashboard → Your PHP service
2. Go to **"Settings"** → **"Domains"**
3. Click **"+ Add Domain"**
4. Enter your domain: `ideaspace.codecollege.edu`
5. Railway gives you DNS records to add to your domain provider

Example (if using Route 53, CloudFlare, GoDaddy, etc.):
- Add CNAME record pointing to Railway
- Wait 2-5 minutes for DNS propagation
- Your custom domain is live!

---

## Environment Variables Explained

| Variable | Purpose | Example |
|----------|---------|---------|
| DB_HOST | MySQL host | mysql.railway.internal |
| DB_NAME | Database name | railway |
| DB_USER | Database user | root |
| DB_PASSWORD | Database password | (from MySQL settings) |
| DB_PORT | Database port | 3306 |
| APP_ENV | Environment | production |
| APP_DEBUG | Debug mode | false |
| APP_URL | Your live domain | https://ideasync.railway.app |

---

## Monitoring & Logs

In Railway dashboard:
- **Logs** tab: See real-time application logs
- **Metrics** tab: Monitor CPU, memory, network
- **Deployments** tab: View deployment history
- **Settings**: Scale resources if needed

---

## Troubleshooting

### App won't start
1. Check logs: **Logs** tab in Railway
2. Verify environment variables are set
3. Check database connection

### Database connection failed
1. Verify `DB_HOST=mysql.railway.internal`
2. Check password matches MySQL service details
3. Ensure migrations ran successfully

### Custom domain not working
1. Check DNS records propagated: `nslookup yourdomain.com`
2. Wait up to 24 hours for full DNS propagation
3. Check Railway domain settings

---

## Auto-Deployment (Continuous Integration)

Once connected, Railway automatically:
- Deploys on every push to `main` branch
- Builds Docker image
- Runs health checks
- Routes traffic to new version
- Keeps old version as fallback

Push code:
```bash
git push origin main
```

Railway deploys automatically ✨

---

## Costs

Railway pricing:
- **Usage-based**: Pay only for what you use
- **Free tier**: $5/month credit (development)
- **Production**: ~$10-50/month depending on traffic
  - PHP app: ~$5/month
  - MySQL: ~$15/month
  - Network egress: ~$0.10/GB

---

## Next Steps

1. ✅ Create Railway account
2. ✅ Connect GitHub repo
3. ✅ Add MySQL service
4. ✅ Configure environment variables
5. ✅ Run migrations
6. ✅ Visit your live app!
7. ⏳ (Optional) Add custom domain

**Your app will be live in < 10 minutes!** 🎉

---

For more help: https://railway.app/docs
