# Use a imagem base oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# --- 1. INSTALAÇÃO DE DEPENDÊNCIAS ---
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www/html

# --- 2. BUILD DO PROJETO ---
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN npm install
RUN npm run build

# Criar o ficheiro .env a partir do exemplo
RUN cp .env.example .env
# Gerar a chave da aplicação Laravel
RUN php artisan key:generate

# --- 3. CONFIGURAÇÃO DO SERVIDOR ---
# **[CORREÇÃO #2]** - Apontar o Apache para a pasta /public
RUN a2enmod rewrite
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expor a porta 80
EXPOSE 80

# --- 4. SCRIPT DE INICIALIZAÇÃO ---
# **[CORREÇÃO #1]** - Script de inicialização final para corrigir permissões e cache
RUN echo '#!/bin/bash\n\
# Assegurar que as permissões estão sempre corretas na inicialização\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache\n\
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache\n\
\n\
echo "Limpando e cacheando configurações..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
echo "Executando migrações do banco de dados..."\n\
php artisan migrate --force\n\
\n\
echo "Iniciando servidor Apache..."\n\
apache2-foreground' > /usr/local/bin/start.sh

# Tornar o script executável
RUN chmod +x /usr/local/bin/start.sh

# Comando de inicialização
CMD ["/usr/local/bin/start.sh"]
