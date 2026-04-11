# IdeaSync - Railway Deployment (FINAL STEPS)

## Status: You're Connected to Railway ✅

Now complete these steps to go live:

---

## STEP 1: Wait for Initial Build (2-3 minutes)

In Railway dashboard:
1. Click on your project
2. Look at "Deployment" tab
3. Wait for status to turn 🟢 GREEN
4. You'll get a live URL (e.g., `ideaspace-prod.up.railway.app`)

**This happens automatically - Railway reads Procfile & deploys PHP 8.2**

---

## STEP 2: Connect to MySQL Database

In Railway dashboard:
1. Click "Add Service"
2. Search "MySQL"
3. Click "Deploy"
4. Wait for MySQL to start (1 minute)

Now you have:
- Live PHP app
- Live MySQL database
- Auto-configured connection

---

## STEP 3: Run Database Migrations (CRITICAL)

You need to run SQL migrations to create tables.

### Option A: Using Railway CLI (Recommended)

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login to Railway
railway login

# Connect to project
railway link

# Run migrations in order
railway run mysql -p < AGENT_SYSTEM_MIGRATION.sql
railway run mysql -p < PHASE_2_WORKFLOW_MIGRATION.sql
railway run mysql -p < PHASE_3_QUALITY_GATES_MIGRATION.sql
railway run mysql -p < PHASE_4_ANTIPATTERN_MIGRATION.sql
railway run mysql -p < PHASE_5_DESIGN_SYSTEM_MIGRATION.sql
```

### Option B: Using Railway Dashboard

1. Click on MySQL service
2. Click "Connect" → "Data" tab
3. Copy connection string
4. Use MySQL Workbench or CLI:
   ```bash
   mysql -h [host] -u root -p [database] < AGENT_SYSTEM_MIGRATION.sql
   (repeat for all 5 migration files)
   ```

---

## STEP 4: Verify Everything Works

Open your live URL in browser:

```
1. Homepage loads ✅
2. Click "Register"
3. Create test account
4. Login
5. Select agent role
6. See customized dashboard
7. Try creating an idea
8. Go through workflow
```

If all ✅ → **You're live!**

---

## STEP 5: Test Key Features

- [ ] User registration works
- [ ] Agent role selection works
- [ ] Dashboard shows (role-specific)
- [ ] Can create idea
- [ ] Can fill charter form
- [ ] Can advance to plan phase
- [ ] Can create project brief
- [ ] Can add wave tasks
- [ ] Leaderboard shows users
- [ ] Notifications work

---

## TROUBLESHOOTING

### Issue: "Database connection failed"
**Solution:** Migrations not run. Execute Step 3.

### Issue: "Page shows 404"
**Solution:** PHP routing issue. Verify Procfile has correct path.

### Issue: "CSRF token error"
**Solution:** Session issue. Restart Railway deployment:
1. Click deployment
2. Click "Redeploy"

### Issue: "White screen / no content"
**Solution:** 
1. Check Railway logs: Click project → Logs tab
2. Look for PHP errors
3. Common fix: Run missing migration

### Issue: "MySQL permission denied"
**Solution:** Use the AUTO-CONFIGURED connection string from Railway.

---

## YOUR LIVE APP IS AT:

**https://[YOUR-PROJECT-NAME].up.railway.app**

(Find exact URL in Railway Dashboard → Settings)

---

## NEXT: Tell Your Users

When everything works:

```
🎓 IdeaSync is LIVE at: https://[your-url].up.railway.app

Create your account and start collaborating on ideas!

Features available:
✅ 5 agent roles (Student, Faculty, Lead, Reviewer, Community)
✅ 5-phase idea workflow
✅ Smart mentor matching
✅ Quality gates & approvals
✅ Anti-pattern detection
✅ Leaderboard & achievements
✅ Team collaboration

Sign up now! 🚀
```

---

## DEPLOYMENT COMPLETE When:

- ✅ App loads at live URL
- ✅ Can create account
- ✅ Can select agent role
- ✅ Dashboard displays
- ✅ Can post ideas
- ✅ Can go through workflow

**That's it. You're done. System is live.**

---

## MONITORING (Optional)

Railway dashboard shows:
- 🔴 Errors (check Logs)
- 📈 CPU/Memory usage
- 📊 Request count
- 🌐 Deployments history

Everything auto-scales on Railway's free tier up to reasonable limits.

---

## SUPPORT

If issues:
1. Check Railway Logs (click project → Logs)
2. Read error messages carefully
3. Most issues = missing migration (Step 3)
4. Contact Railway support if platform issue

---

**Your system is ready. Students can start using it immediately after deployment.**

Good luck! 🚀
