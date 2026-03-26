FROM php:8.2-cli

# Instalar install-php-extensions (binarios precompilados — mucho más rápido)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        bcmath \
        opcache

# Instalar utilidades del sistema + Node.js 20
RUN apt-get update && apt-get install -y git curl unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Instalar dependencias PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Instalar dependencias Node y compilar assets
COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build

# Permisos para Laravel
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "start.sh"]
