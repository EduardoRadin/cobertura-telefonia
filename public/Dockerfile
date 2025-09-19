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

# **[CORREÇÃO #1]** Criar explicitamente as pastas e o ficheiro de log durante o build
RUN mkdir -p storage/framework/{sessions,views,cache}
RUN mkdir -p storage/logs
RUN touch storage/logs/laravel.log

# --- 3. CONFIGURAÇÃO DO SERVIDOR ---
# Ajustar permissões durante o build
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage

# Apontar o Apache para a pasta /public
RUN a2enmod rewrite
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expor a porta 80
EXPOSE 80

# --- 4. SCRIPT DE INICIALIZAÇÃO ---
# **[CORREÇÃO #2]** Script de inicialização final e à prova de falhas
RUN echo '#!/bin/bash\n\
# Garantir que o ficheiro de log existe e tem as permissões corretas\n\
touch /var/www/html/storage/logs/laravel.log\n\
\n\
# Forçar as permissões corretas em toda a pasta storage no arranque\n\
chown -R www-data:www-data /var/www/html/storage\n\
chmod -R 775 /var/www/html/storage\n\
\n\
# Limpar todos os caches para forçar a releitura das variáveis de ambiente\n\
php artisan cache:clear\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
\n\
# Criar os ficheiros de cache com as configurações corretas\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
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
