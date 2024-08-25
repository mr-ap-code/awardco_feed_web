#!/bin/bash

# Install necessary dependencies
if [ "$(uname)" == "Darwin" ]; then
    # macOS
    brew install php
elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then
    # Linux
    sudo apt-get update
    sudo apt-get install -y php php-curl
fi

# Set up the project directory
mkdir -p /var/www/awardco-feed
cp -r * /var/www/awardco-feed

# Set permissions
sudo chown -R www-data:www-data /var/www/awardco-feed

# Start PHP built-in web server
php -S localhost:8080 -t public &
echo "PHP built-in web server started at http://localhost:8080"