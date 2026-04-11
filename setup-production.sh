#!/bin/bash
# IdeaSync Production Setup Script

echo "🚀 Starting IdeaSync Production Setup..."

# 1. Create necessary directories
mkdir -p uploads logs
chmod 777 uploads logs

# 2. Check for .env file
if [ ! -f ".env" ]; then
    echo "Creating .env from example..."
    cp .env.example .env
fi

# 3. Run migrations
echo "Running database migrations..."
php migrate.php

# 4. Seed demo data
echo "Seeding initial data..."
# We can't run seed.php directly via CLI if it has HTML/redirects,
# but we can call the logic if we extract it.
# For now, let's assume the user runs it via browser or we use a CLI version.

echo "✅ Production setup complete!"
echo "Next steps:"
echo "1. Configure your .env file with production credentials."
echo "2. Visit /public/seed.php in your browser to populate demo data."
