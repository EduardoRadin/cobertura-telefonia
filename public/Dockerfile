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

# **[A CORREÇÃO]** - Configurar o .env para usar a base de dados da Render
# 1. Mudar a ligação para pgsql
RUN sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/g' .env
# 2. Apagar as variáveis de base de dados locais para forçar o uso da DATABASE_URL
RUN sed -i '/^DB_HOST=/d' .env
RUN sed -i '/^DB_PORT=/d' .env
RUN sed -i '/^DB_DATABASE=/d' .env
RUN sed -i '/^DB_USERNAME=/d' .env
RUN sed -i '/^DB_PASSWORD=/d' .env

# Gerar a chave da aplicação Laravel
RUN php artisan key:generate

# --- 3. CONFIGURAÇÃO DO SERVIDOR ---
# Assegurar que as permissões e pastas existem durante o build
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs
RUN touch storage/logs/laravel.log
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apontar o Apache para a pasta /public
RUN a2enmod rewrite
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expor a porta 80
EXPOSE 80

# --- 4. SCRIPT DE INICIALIZAÇÃO ---
# Script de inicialização para limpar caches e executar migrações
RUN echo '#!/bin/bash\n\
# Assegurar que as permissões estão corretas no arranque\n\
chown -R www-data:www-data /var/www/html/storage\n\
chmod -R 775 /var/www/html/storage\n\
\n\
echo "A limpar caches para ler as variáveis de ambiente..."\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
\n\
echo "A executar as migrações da base de dados..."\n\
php artisan migrate --force\n\
\n\
echo "A iniciar o servidor Apache..."\n\
apache2-foreground' > /usr/local/bin/start.sh

# Tornar o script executável
RUN chmod +x /usr/local/bin/start.sh

# Comando de inicialização
CMD ["/usr/local/bin/start.sh"]
