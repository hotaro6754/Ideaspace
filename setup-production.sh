#!/bin/bash
# IdeaSync Production Deployment Quick-Start
# This script prepares your app for deployment

echo "🚀 IdeaSync Production Deployment Setup"
echo "========================================"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env created. Please edit with your values:"
    echo "   nano .env"
    echo ""
else
    echo "✅ .env file already exists"
fi

# Check if git is initialized
if [ ! -d ".git" ]; then
    echo "🔧 Initializing Git repository..."
    git init
    git remote add origin https://github.com/yourusername/ideaspace.git
    echo "✅ Git repository initialized"
else
    echo "✅ Git repository already initialized"
fi

# Check git ignore
if grep -q ".env" .gitignore 2>/dev/null; then
    echo "✅ .env is properly in .gitignore"
else
    echo "⚠️  .env not in .gitignore - fixing..."
    echo ".env" >> .gitignore
    echo "✅ .env added to .gitignore"
fi

# Create uploads directory
if [ ! -d "uploads" ]; then
    echo "📁 Creating uploads directory..."
    mkdir -p uploads
    chmod 777 uploads
    echo "✅ Uploads directory created"
else
    echo "✅ Uploads directory exists"
fi

# Create logs directory
if [ ! -d "logs" ]; then
    echo "📁 Creating logs directory..."
    mkdir -p logs
    chmod 777 logs
    echo "✅ Logs directory created"
else
    echo "✅ Logs directory exists"
fi

echo ""
echo "📋 Pre-Deployment Checklist:"
echo "================================"
echo "✓ .env configuration file prepared"
echo "✓ .gitignore configured (.env excluded)"
echo "✓ Uploads directory created"
echo "✓ Logs directory created"
echo ""
echo "🔐 IMPORTANT SECURITY REMINDERS:"
echo "================================"
echo "1. NEVER commit .env to Git"
echo "2. Use HTTPS in production"
echo "3. Update DB_PASSWORD in .env"
echo "4. Set APP_ENV=production"
echo "5. Use SMTP for emails"
echo ""
echo "📘 Next Steps:"
echo "1. Edit .env with your production values:"
echo "   nano .env"
echo ""
echo "2. Choose your deployment platform:"
echo "   • Railway (fastest): See DEPLOYMENT_GUIDE.md"
echo "   • DigitalOcean: See DEPLOYMENT_GUIDE.md"
echo "   • Other: See DEPLOYMENT_GUIDE.md"
echo ""
echo "3. Commit changes:"
echo "   git add -A"
echo "   git commit -m 'Production build ready'"
echo "   git push origin main"
echo ""
echo "4. Deploy to your chosen platform"
echo "   Run migrations: php migrate.php"
echo ""
echo "✅ Setup Complete! You're ready to deploy."
echo ""
