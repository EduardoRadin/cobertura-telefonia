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

# Instalar extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www/html


# --- 2. BUILD DO PROJETO ---
# Copiar os arquivos de dependência primeiro para otimizar o cache do Docker
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev

COPY package.json package-lock.json ./
RUN npm install

# Copiar o restante dos arquivos do projeto
COPY . .

# Gerar a chave da aplicação Laravel
RUN php artisan key:generate

# Construir os assets de frontend
RUN npm run build


# --- 3. CONFIGURAÇÃO DO SERVIDOR ---
# Ajustar permissões para o Laravel
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# **[CORREÇÃO DO PROBLEMA #2]** - Apontar o Apache para a pasta /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expor a porta 80
EXPOSE 80


# --- 4. SCRIPT DE INICIALIZAÇÃO ---
# **[CORREÇÃO DO PROBLEMA #1]** - Script de inicialização corrigido
RUN echo '#!/bin/bash\n\
echo "Limpando cache de configuração..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
echo "Executando migrações do banco de dados..."\n\
php artisan migrate --force\n\
echo "Iniciando servidor Apache..."\n\
apache2-foreground' > /usr/local/bin/start.sh

# Tornar o script de inicialização executável
RUN chmod +x /usr/local/bin/start.sh

# Definir o comando de inicialização do container
CMD ["/usr/local/bin/start.sh"]
