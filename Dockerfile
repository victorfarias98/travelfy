# Dockerfile
FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    cron \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do composer primeiro (para cache de layers)
COPY composer.json composer.lock ./

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copiar código da aplicação
COPY . .

# Executar scripts do composer após copiar todo o código
RUN composer run-script post-autoload-dump --no-interaction

# Definir permissões corretas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copiar configurações
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copiar arquivo de inicialização
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expor porta
EXPOSE 8000

# Comando de inicialização
CMD ["/usr/local/bin/start.sh"]