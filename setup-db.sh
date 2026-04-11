#!/bin/bash

# IdeaSync Database Setup Script
# Creates database and user for local development

echo "Setting up IdeaSync Database..."

# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS ideaspace_dev;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'ideaspace_user'@'localhost' IDENTIFIED BY 'IdeaSpace@Local2024';"
sudo mysql -e "GRANT ALL PRIVILEGES ON ideaspace_dev.* TO 'ideaspace_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "Creating tables..."

# Import schema
sudo mysql -u root ideaspace_dev < DATABASE_SCHEMA.sql

echo "Database setup complete!"
echo "Database: ideaspace_dev"
echo "User: ideaspace_user"
echo "Password: IdeaSpace@Local2024"
