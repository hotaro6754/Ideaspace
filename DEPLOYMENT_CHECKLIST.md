# 🚀 IdeaSpace - Railway Deployment Checklist

## Pre-Deployment (Already Done ✅)

- ✅ PHP application with MySQL database
- ✅ Dockerfile configured (PHP 8.3 + Apache)
- ✅ Railway configuration files added
- ✅ Database migrations prepared
- ✅ Environment templates created
- ✅ All code committed to Git

---

## Deployment in 5 Minutes ⚡

### 1️⃣ Create Railway Account (2 min)
```
1. Go to https://railway.app
2. Click "Get Started"
3. Login with GitHub (recommended)
4. Authorize Railway access
```

### 2️⃣ Deploy Your App (2 min)
```
1. Visit https://railway.app/dashboard
2. Click "+ New Project"
3. Click "Deploy from GitHub"
4. Select "Ideaspace" repository
5. Click "Deploy"

→ Railway deploys automatically!
```

### 3️⃣ Add MySQL Database (1 min)
```
In Railway Dashboard:
1. Click "+ Add Services"
2. Select "MySQL"
3. Configure (Railway handles everything)
4. Get credentials from MySQL service
```

### 4️⃣ Set Environment Variables (1 min)
```
In Railway Dashboard → PHP Service → Variables:

DB_HOST=mysql.railway.internal
DB_NAME=railway
DB_USER=root
DB_PASSWORD=[copy from MySQL service]
DB_PORT=3306
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[auto-generated].railway.app
```

### 5️⃣ Run Database Setup
```
In Railway Dashboard → MySQL Service:
1. Open Terminal (exec button)
2. Run migrations:

mysql -h localhost -u root -p[PASSWORD] railway < migration/add-recommendation-system.sql
mysql -h localhost -u root -p[PASSWORD] railway < DATABASE_SCHEMA.sql
mysql -h localhost -u root -p[PASSWORD] railway < seed-data.sql
```

---

## Result 🎉

**Your live app:**
```
https://ideaspace-[random].railway.app
```

**Test it:**
- Home: `/?page=home`
- Dashboard: `/?page=dashboard`
- Ideas: `/?page=ideas`

**Test Credentials:**
- Email: harshith@example.com
- Email: priya@example.com
- Email: arjun@example.com

---

## Optional: Custom Domain 🌐

After app is live, add your own domain:

1. Railway Dashboard → PHP Service → Settings → Domains
2. Click "+ Add Domain"
3. Enter: `ideaspace.youruni.edu`
4. Add DNS records to your domain provider
5. Wait 2-5 minutes
6. **LIVE on your domain!**

---

## Auto-Deployment (Continuous)

Every time you:
```bash
git push origin main
```

Railway automatically:
1. Rebuilds Docker image
2. Runs tests (if configured)
3. Deploys new version
4. Zero downtime deployment ✨

---

## Monitoring

In Railway Dashboard, track:
- 📊 **Metrics**: CPU, memory, network
- 📝 **Logs**: Real-time application logs
- 🔄 **Deployments**: Version history
- ⚙️ **Settings**: Scale resources

---

## Costs

With Railway:
- **First $5/month**: Free (development)
- **Beyond**: Usage-based pricing (~$10-50/month for small app)
- **Your app**: ~$5-20/month total

---

## Common Issues & Fixes

| Issue | Fix |
|-------|-----|
| App won't start | Check Logs tab, verify DB connection |
| Database error | Verify DB_HOST, password, migrations ran |
| Custom domain not working | Check DNS propagation: `nslookup yourdomain.com` |
| App slow | Check Metrics tab, Railway can auto-scale |

---

## Full Documentation

See: `RAILWAY_DEPLOYMENT.md` for detailed guide

---

## Summary

✅ App ready
✅ Database configured  
✅ Docker set up
✅ Just need Railway account

**Time to deploy: < 5 minutes**
**Time to live: < 10 minutes**

🚀 **Let's deploy!**

