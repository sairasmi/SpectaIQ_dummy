#!/usr/bin/env bash
set -e

if [ ! -d "backend" ]; then
  echo "Creating Lumen project..."
  composer create-project --prefer-dist laravel/lumen backend
fi

cd backend

echo "Installing Composer dependencies..."
composer require razorpay/razorpay:^2.9 2>/dev/null || true
composer require firebase/php-jwt:^6.9 2>/dev/null || true
composer require guzzlehttp/guzzle:^7.8 2>/dev/null || true
composer require illuminate/mail:^10.0 2>/dev/null || true

if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  else
    echo "Creating .env file..."
    cat > .env << 'EOL'
APP_NAME=Elearn
APP_ENV=local
APP_DEBUG=true
APP_KEY=
APP_TIMEZONE=UTC

DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

RAZORPAY_KEY_ID=your_key_id
RAZORPAY_KEY_SECRET=your_key_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret

WHATSAPP_TOKEN=your_meta_token
WHATSAPP_PHONE_ID=your_phone_id
WHATSAPP_TEMPLATE_WELCOME=welcome_template

MAIL_MAILER=log
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME=Elearn
EOL
  fi
fi

echo "Generating app scaffolding..."
php ../artisan_make.php

echo "Running migrations..."
php artisan migrate --force

echo "Seeding demo data..."
php ../seed_data.php

echo "Bootstrap complete!"
