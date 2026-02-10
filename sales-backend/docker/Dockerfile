FROM php:8.2-fpm

# Argumentos
ARG user=sales
ARG uid=1000

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
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário do sistema
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Instalar Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar permissões
COPY --chown=$user:$user . /var/www

# Mudar para usuário criado
USER $user

# Expor porta 9000 e iniciar php-fpm
EXPOSE 9000
CMD ["php-fpm"]