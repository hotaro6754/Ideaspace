# Railway Deployment - Complete Setup Guide

## Status: ✅ Ready for Production

All code, configuration, and database are fully prepared for deployment to Railway.

## What's Already Done

- ✅ Database schema created (13 tables)
- ✅ Test data seeded (4 users, 3 ideas, 10 skills)
- ✅ IdeaRecommendation model built (trending, recommendations, skill matching)
- ✅ UI/UX enhanced (personalized feeds, trending badges, perfect team suggestions)
- ✅ Docker configuration ready
- ✅ Environment variables configured
- ✅ All code committed to Git

## Deployment Steps

### Step 1: Accept Railway Invite
Open this link in your browser:
```
https://railway.com/invite/cnRG97ljLKC
```
This grants our AI assistant access to help with future deployments.

### Step 2: Deploy Repository to Railway

1. Go to your Railway dashboard: https://railway.app
2. Click **"+ New Project"**
3. Select **"Deploy from GitHub"**
4. Find and select **"Ideaspace"** repository
5. Choose **main** branch
6. Click **"Deploy"**

Railroad will automatically:
- Detect it's a PHP application
- Build the Docker container
- Start the deployment process

### Step 3: Configure Environment Variables

While the deployment builds, go to **Project Settings** → **Variables** and add:

```
DB_HOST=mainline.proxy.rlwy.net
DB_NAME=railway
DB_USER=root
DB_PASSWORD=GFVaFlrAeiTLfbFAUkjZedHjeCYIPaqh
DB_PORT=57598
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE=true
SESSION_HTTPONLY=true
```

### Step 4: Wait for Deployment

- Build time: 2-3 minutes
- Look for green checkmark ✅
- Copy the live URL (should be like `https://ideasync-production-xxxx.railway.app`)

## Testing the Live Application

### Test Accounts

**Harshith (Founder)**
- Email: harshith@ideaspace.com
- Password: password123

**Priya (React Expert)**
- Email: priya@ideaspace.com
- Password: password123

### Features to Test

1. **Personalized Feed**
   - Go to Dashboard → "Ideas For You"
   - Should see 3 ideas ranked by skill match (60%) + trending (40%)

2. **Trending Algorithm**
   - Go to Ideas List
   - Change sort to "Trending 🔥"
   - Should show top trending ideas

3. **Perfect Builders Matching**
   - Click on any idea
   - Scroll to "Perfect Builders for This Idea"
   - Shows builders with 50%+ skill match

4. **Skill Matching**
   - Ideas list shows green skill % badges
   - Badges show how well user's skills match required skills

5. **Similar Ideas**
   - In idea detail page, see "Similar Ideas in [Domain]"
   - Shows related ideas in the same field

## Live URL

Once deployed, your app will be live at:
```
https://ideasync-production-[random].railway.app
```

## Database Connection

Railway MySQL is already configured:
- Host: `mainline.proxy.rlwy.net:57598`
- Database: `railway`
- User: `root`
- Tables: 13 (users, ideas, user_skills, builder_rank, etc.)

## Important Notes

### Security
- ✅ SessionSecure enabled (HTTPS only)
- ✅ SessionHTTPOnly enabled (no JavaScript access)
- ✅ CSRF protection on forms
- ✅ SQL parameterized queries (no injection risk)
- ✅ Password hashing with bcrypt

### Performance
- ✅ Indexes created for fast queries
- ✅ Trending algorithm optimized for real-time queries
- ✅ Skill matching cached in database
- ✅ Database queries use appropriate pagination

### Monitoring
After deployment:
1. Check Railway dashboard for logs
2. Test the live URL in browser
3. Try logging in with test accounts
4. Navigate through all major features

## Troubleshooting

### "Database Connection Failed"
- Verify environment variables are exactly as shown above
- Check MySQL is running on Railway
- Ping mainline.proxy.rlwy.net to verify connectivity

### "Class 'mysqli' not found"
- Railway will automatically use PHP 8.3 with MySQLi
- No action needed

### Build Fails
- Check Railway build logs
- Ensure all files are committed to Git
- Verify .env.production exists

### Slow Performance
- First load might be slow (cold start)
- Subsequent loads will be faster
- Database indexes are already optimized

## Next Steps After Deployment

1. **Verify Live Features**
   - Test login with both test accounts
   - Navigate to all pages
   - Click through all features

2. **Monitor Performance**
   - Check Railway logs for errors
   - Monitor database connection usage
   - Watch for slow queries

3. **Add More Data**
   - Create new user accounts
   - Post new ideas
   - Apply to collaborations
   - Rate team members

4. **Optional: Custom Domain**
   - Go to Railway project settings
   - Add custom domain
   - Configure DNS records

## Architecture

```
┌─────────────────────┐
│   GitHub (Code)     │
└──────────┬──────────┘
           │
┌──────────▼──────────┐
│  Railway (PHP App)  │ ← You are here
│  └─ PHP 8.3         │
│  └─ Apache          │
└──────────┬──────────┘
           │
┌──────────▼──────────┐
│  Railway (MySQL)    │
│  mainline.proxy...  │
│  :57598             │
└─────────────────────┘
```

## Costs

- PHP application: $7-12/month (based on usage)
- MySQL database: $7-15/month
- **Total estimate: $13-25/month**

All data is persisted. No charges for stopped services.

## Support

If deployment fails:
1. Check Railway documentation: https://docs.railway.app
2. Review deployment logs in Railway dashboard
3. Verify environment variables match exactly

---

**Deployment Time: ~5 minutes**
**Status: ✅ Ready to Deploy**
