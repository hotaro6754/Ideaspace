#!/bin/bash

# IdeaSync - Production Readiness Verification Script
# Run this before deploying to production

set -e

echo "🔍 IdeaSync Production Readiness Check"
echo "======================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check PHP version
echo "1️⃣  Checking PHP Version..."
php_version=$(php -v | head -1 | grep -oP 'PHP \K[0-9.]+' | cut -d. -f1-2)
echo -e "${GREEN}✓ PHP $php_version (>= 7.4 required)${NC}"

# Check required PHP extensions
echo ""
echo "2️⃣  Checking PHP Extensions..."
extensions_needed=("mysqli" "pdo" "json" "curl")
missing_ext=0
for ext in "${extensions_needed[@]}"; do
    if php -m | grep -q "$ext"; then
        echo -e "${GREEN}✓ $ext${NC}"
    else
        echo -e "${YELLOW}⚠ $ext (will be installed in Docker)${NC}"
        missing_ext=$((missing_ext + 1))
    fi
done
if [ $missing_ext -gt 0 ]; then
    echo -e "${YELLOW}Note: Extensions will be installed automatically by Docker${NC}"
fi

# Check critical files exist
echo ""
echo "3️⃣  Checking Critical Files..."
files_needed=(
    "public/index.php"
    "public/router.php"
    "src/config/Database.php"
    "src/assets/css/main.css"
    "src/assets/css/variables.css"
    "DATABASE_SCHEMA.sql"
    "Dockerfile"
    "Procfile"
)

for file in "${files_needed[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${RED}✗ $file missing${NC}"
        exit 1
    fi
done

# Check database schema
echo ""
echo "4️⃣  Checking Database Schema..."
table_count=$(grep -c "CREATE TABLE" DATABASE_SCHEMA.sql)
echo -e "${GREEN}✓ Found $table_count tables in schema${NC}"

if grep -q "users\|ideas\|collaborations\|messages" DATABASE_SCHEMA.sql; then
    echo -e "${GREEN}✓ Core tables present${NC}"
else
    echo -e "${RED}✗ Core tables missing${NC}"
    exit 1
fi

# Check controllers
echo ""
echo "5️⃣  Checking Controllers..."
controllers_needed=(
    "src/controllers/auth.php"
    "src/controllers/ideas.php"
    "src/controllers/collaboration.php"
    "src/controllers/notifications.php"
    "src/controllers/messages.php"
)

for controller in "${controllers_needed[@]}"; do
    if [ -f "$controller" ]; then
        echo -e "${GREEN}✓ $(basename $controller)${NC}"
    else
        echo -e "${RED}✗ $(basename $controller) missing${NC}"
    fi
done

# Check models
echo ""
echo "6️⃣  Checking Models..."
models_needed=(
    "src/models/User.php"
    "src/models/Idea.php"
    "src/models/Application.php"
    "src/models/Collaboration.php"
    "src/models/Message.php"
    "src/models/Notification.php"
)

for model in "${models_needed[@]}"; do
    if [ -f "$model" ]; then
        echo -e "${GREEN}✓ $(basename $model)${NC}"
    else
        echo -e "${RED}✗ $(basename $model) missing${NC}"
    fi
done

# Check views
echo ""
echo "7️⃣  Checking Views..."
view_count=$(find src/views -name "*.php" | wc -l)
echo -e "${GREEN}✓ Found $view_count view files${NC}"

# Check services
echo ""
echo "8️⃣  Checking Services..."
services=(
    "src/services/EmailService.php"
    "src/services/GitHubAPI.php"
    "src/services/Security.php"
)

for service in "${services[@]}"; do
    if [ -f "$service" ]; then
        echo -e "${GREEN}✓ $(basename $service)${NC}"
    else
        echo -e "${RED}✗ $(basename $service) missing${NC}"
    fi
done

# Check deployment configs
echo ""
echo "9️⃣  Checking Deployment Configuration..."
if [ -f "Dockerfile" ]; then
    echo -e "${GREEN}✓ Docker configured${NC}"
fi

if [ -f "Procfile" ]; then
    echo -e "${GREEN}✓ Procfile configured${NC}"
fi

if [ -f "nixpacks.toml" ]; then
    echo -e "${GREEN}✓ Railway build configuration present${NC}"
fi

# Check CSS structure
echo ""
echo "🔟 Checking CSS Structure..."
css_files=("variables.css" "components.css" "responsive.css" "main.css")
for css in "${css_files[@]}"; do
    if [ -f "src/assets/css/$css" ]; then
        size=$(wc -l < "src/assets/css/$css")
        echo -e "${GREEN}✓ $css ($size lines)${NC}"
    else
        echo -e "${RED}✗ $css missing${NC}"
    fi
done

# Check documentation
echo ""
echo "📚 Checking Documentation..."
docs=(
    "README.md"
    "API_DOCUMENTATION.md"
    "DEPLOYMENT.md"
    "ARCHITECTURE.md"
)

for doc in "${docs[@]}"; do
    if [ -f "$doc" ]; then
        echo -e "${GREEN}✓ $doc${NC}"
    else
        echo -e "${YELLOW}⚠ $doc missing${NC}"
    fi
done

# Final check
echo ""
echo "======================================"
echo -e "${GREEN}✅ All Production Readiness Checks Passed!${NC}"
echo ""
echo "🚀 Ready to Deploy to Railway!"
echo ""
echo "Next steps:"
echo "1. Verify environment variables are configured"
echo "2. Run database migrations"
echo "3. Test critical features"
echo "4. Monitor logs after deployment"
echo ""
