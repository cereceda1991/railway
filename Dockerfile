# Usar la imagen base de PHP 8.1
FROM php:8.1

# Establecer el directorio de trabajo en /var/www
WORKDIR /var/www

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libssl-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip

# Instalar extensiones de PHP requeridas por Laravel y Composer
RUN docker-php-ext-install pdo_mysql mbstring zip

# Instalar extensión de OpenSSL para PHP
RUN apt-get install -y openssl

# Instalar extensión de MongoDB para PHP
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copiar los archivos de la aplicación al contenedor
COPY . .

# Copiar el archivo .env.example como .env
RUN cp .env.example .env

# Generar la clave de la aplicación
RUN php artisan key:generate || true

# Establecer permisos adecuados
RUN chown -R www-data:www-data storage bootstrap/cache

# Instalar dependencias de Composer
RUN composer install --no-interaction --no-scripts --no-plugins --prefer-dist --ignore-platform-reqs --optimize-autoloader

# Exponer el puerto 80 del contenedor
EXPOSE 80

# Iniciar el servidor web de PHP
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=$PORT & php artisan queue:work"]
