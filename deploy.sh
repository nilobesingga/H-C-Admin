#!/bin/bash
set -e  # Exit immediately if a command exits with a non-zero status

# Define variables
CONTAINER_NAME="cresco-v2-production-php"
PROJECT_PATH="/var/www/html"
LOG_FILE="$PROJECT_PATH/deploy.log"

echo "🚀 [PRODUCTION] Deployment started at $(date)" | tee -a $LOG_FILE

# Step 1: Run commands inside the Laravel Docker container
docker exec $CONTAINER_NAME sh -c "
    cd $PROJECT_PATH &&

    # Put Laravel in Maintenance Mode
    php artisan down || true

    echo '🔄 Pulling latest code from GitHub...' 
    git pull origin master

    echo '📦 Installing PHP dependencies (composer)...' 
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

    echo '🛠 Clearing caches...' 
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear

    echo '🔄 Running database migrations...' 
    php artisan migrate --force

    echo '📦 Installing Node.js dependencies (npm)...' 
    npm ci

    echo '⚡ Building assets with Vite...' 
    npm run build

    # Bring Laravel back up
    php artisan up

    echo '✅ [PRODUCTION] Deployment completed at $(date)' 
" | tee -a $LOG_FILE

echo "🎉 Deployment successful!"
exit 0
