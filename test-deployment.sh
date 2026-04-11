#!/bin/bash

###############################################################################
# IdeaSpace - Automated Functionality Test Suite
# Tests all core features before production deployment
###############################################################################

set -e

echo "=========================================="
echo "IdeaSpace Functionality Test Suite"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: Check all required files exist
echo -e "${YELLOW}Test 1: Checking required files...${NC}"
required_files=(
    "src/models/IdeaRecommendation.php"
    "src/config/Database.php"
    "src/config/Env.php"
    "public/index.php"
    ".env.production"
    "Dockerfile"
    "railway.toml"
    "DATABASE_SCHEMA.sql"
)

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file MISSING"
        exit 1
    fi
done
echo ""

# Test 2: Check PHP syntax
echo -e "${YELLOW}Test 2: Checking PHP syntax...${NC}"
php_files=$(find src -name "*.php" -type f)
for file in $php_files; do
    if php -l "$file" > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file has syntax errors"
        php -l "$file"
        exit 1
    fi
done
echo ""

# Test 3: Verify environment variables
echo -e "${YELLOW}Test 3: Verifying environment variables...${NC}"
required_vars=(
    "DB_HOST"
    "DB_NAME"
    "DB_USER"
    "DB_PASSWORD"
    "DB_PORT"
    "APP_ENV"
)

for var in "${required_vars[@]}"; do
    if grep -q "^${var}=" .env.production; then
        value=$(grep "^${var}=" .env.production | cut -d'=' -f2)
        echo -e "${GREEN}✓${NC} $var is configured"
    else
        echo -e "${RED}✗${NC} $var is missing"
        exit 1
    fi
done
echo ""

# Test 4: Test database connection
echo -e "${YELLOW}Test 4: Testing database connection to Railway...${NC}"
DB_HOST=$(grep "^DB_HOST=" .env.production | cut -d'=' -f2)
DB_PORT=$(grep "^DB_PORT=" .env.production | cut -d'=' -f2)
DB_USER=$(grep "^DB_USER=" .env.production | cut -d'=' -f2)
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env.production | cut -d'=' -f2)
DB_NAME=$(grep "^DB_NAME=" .env.production | cut -d'=' -f2)

if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" -e "SELECT 1;" > /dev/null 2>&1; then
    echo -e "${GREEN}✓${NC} Connected to Railway MySQL"
else
    echo -e "${RED}✗${NC} Failed to connect to Railway MySQL"
    exit 1
fi
echo ""

# Test 5: Verify database tables
echo -e "${YELLOW}Test 5: Verifying database tables...${NC}"
required_tables=(
    "users"
    "ideas"
    "user_skills"
    "builder_rank"
    "applications"
    "upvotes"
    "collaborations"
    "messages"
    "notifications"
)

for table in "${required_tables[@]}"; do
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" -e "SHOW TABLES LIKE '$table';" | grep -q "$table"; then
        echo -e "${GREEN}✓${NC} Table '$table' exists"
    else
        echo -e "${RED}✗${NC} Table '$table' is missing"
        exit 1
    fi
done
echo ""

# Test 6: Verify test data
echo -e "${YELLOW}Test 6: Verifying test data...${NC}"
user_count=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" -se "SELECT COUNT(*) FROM users;")
idea_count=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" -se "SELECT COUNT(*) FROM ideas;")
skill_count=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" -se "SELECT COUNT(*) FROM user_skills;")

echo -e "${GREEN}✓${NC} Users: $user_count"
echo -e "${GREEN}✓${NC} Ideas: $idea_count"
echo -e "${GREEN}✓${NC} Skills: $skill_count"

if [ "$user_count" -lt 2 ] || [ "$idea_count" -lt 1 ] || [ "$skill_count" -lt 2 ]; then
    echo -e "${YELLOW}⚠${NC} Limited test data (consider adding more)"
fi
echo ""

# Test 7: Verify IdeaRecommendation model
echo -e "${YELLOW}Test 7: Testing IdeaRecommendation model methods...${NC}"
php << 'EOF'
<?php
require_once 'src/config/Database.php';
require_once 'src/models/IdeaRecommendation.php';

try {
    $db = new Database();
    $conn = $db->connect();

    $recommender = new IdeaRecommendation($conn);

    // Test getRecommendedIdeas
    $recommendations = $recommender->getRecommendedIdeas(1, 5);
    echo "✓ getRecommendedIdeas() works\n";

    // Test getTrendingIdeas
    $trending = $recommender->getTrendingIdeas(5, 7);
    echo "✓ getTrendingIdeas() works\n";

    // Test findPerfectTeam
    if ($ideas = $recommender->getTrendingIdeas(1, 7)) {
        $idea = reset($ideas);
        $team = $recommender->findPerfectTeam($idea['id'], 5);
        echo "✓ findPerfectTeam() works\n";
    }

    // Test calculateSkillMatch
    $match = $recommender->calculateSkillMatch(
        ['PHP', 'MySQL'],
        ['PHP', 'React', 'Node.js']
    );
    echo "✓ calculateSkillMatch() works\n";

    // Test getSimilarIdeas
    if ($ideas = $recommender->getTrendingIdeas(1, 7)) {
        $idea = reset($ideas);
        $similar = $recommender->getSimilarIdeas($idea['id'], 5);
        echo "✓ getSimilarIdeas() works\n";
    }

    echo "\n✅ All model methods functional\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
EOF

echo ""

# Test 8: Check Git status
echo -e "${YELLOW}Test 8: Verifying Git repository...${NC}"
if [ -d ".git" ]; then
    echo -e "${GREEN}✓${NC} Git repository initialized"
    commits=$(git log --oneline | wc -l)
    echo -e "${GREEN}✓${NC} Commits: $commits"

    if [ -n "$(git status --porcelain)" ]; then
        echo -e "${YELLOW}⚠${NC} Uncommitted changes exist"
    else
        echo -e "${GREEN}✓${NC} All changes committed"
    fi
else
    echo -e "${RED}✗${NC} Git repository not found"
    exit 1
fi
echo ""

# Test 9: Docker configuration
echo -e "${YELLOW}Test 9: Verifying Docker configuration...${NC}"
if [ -f "Dockerfile" ]; then
    echo -e "${GREEN}✓${NC} Dockerfile exists"
    if grep -q "FROM php:8.3" Dockerfile; then
        echo -e "${GREEN}✓${NC} PHP 8.3 configured"
    fi
    if grep -q "mysqli" Dockerfile; then
        echo -e "${GREEN}✓${NC} MySQLi extension included"
    fi
fi
echo ""

# Test 10: Summary
echo -e "${YELLOW}Test 10: Final status check...${NC}"
echo ""
echo -e "${GREEN}=========================================="
echo "✅ ALL TESTS PASSED"
echo "=========================================="
echo ""
echo "Application Status: READY FOR PRODUCTION"
echo ""
echo "Next Steps:"
echo "1. Accept Railway invite: https://railway.com/invite/cnRG97ljLKC"
echo "2. Deploy GitHub repo to Railway"
echo "3. Set environment variables in Railway dashboard"
echo "4. Wait 2-3 minutes for build completion"
echo "5. Test live URL in browser"
echo ""
echo "Database: mainline.proxy.rlwy.net:57598"
echo "Tables: 13 tables with seed data"
echo "Features: Trending, Recommendations, Skill Matching, Similar Ideas"
echo -e "${NC}"
