FROM php:8.2.0-fpm

RUN apt-get update && apt-get install --no-install-recommends -y \
  curl git unzip nginx \
  libpng-dev libzip-dev libicu-dev \
  && docker-php-ext-install pdo_mysql zip gd intl \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# build Vue.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get install -y nodejs \
  && npm install -g npm

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

RUN npm install && npm run build

RUN chmod -R 777 storage bootstrap/cache

# Nginx
COPY ./docker/nginx/default.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

EXPOSE 80

CMD ["sh", "-c", "service nginx start && php-fpm -F"]
