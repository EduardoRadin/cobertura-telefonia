# Use a imagem base oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instalar dependências do sistema
# - libpq-dev: para a extensão PostgreSQL
# - nodejs & npm: para o build do frontend
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

# Copiar os arquivos de dependência primeiro para otimizar o cache do Docker
COPY composer.json composer.lock ./
# Instalar dependências do PHP sem pacotes de desenvolvimento
RUN composer install --optimize-autoloader --no-interaction --no-plugins --no-scripts --no-dev

COPY package.json package-lock.json ./
# Instalar dependências do Node.js
RUN npm install

# Copiar o restante dos arquivos do projeto
COPY . .

# Executar o restante dos scripts do composer e o build do frontend
RUN composer install --optimize-autoloader --no-dev
RUN npm run build

# Criar o arquivo .env a partir do exemplo (as variáveis da Render irão sobrescrevê-lo)
RUN cp .env.example .env

# Ajustar permissões para o Laravel
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# Habilitar mod_rewrite do Apache para URLs amigáveis
RUN a2enmod rewrite

# Expor a porta 80
EXPOSE 80

# Script de inicialização corrigido
# Adicionado "php artisan config:cache" para carregar as variáveis de ambiente corretamente
RUN echo '#!/bin/bash\n\
echo "Limpando cache de configuração..."\n\
php artisan config:cache\n\
echo "Executando migrações do banco de dados..."\n\
php artisan migrate --force\n\
echo "Iniciando servidor Apache..."\n\
apache2-foreground' > /usr/local/bin/start.sh

# Tornar o script de inicialização executável
RUN chmod +x /usr/local/bin/start.sh

# Definir o comando de inicialização do container
CMD ["/usr/local/bin/start.sh"]
