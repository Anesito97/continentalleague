# Usa la imagen oficial de PHP con Alpine (es ligera)
FROM php:8.3-fpm-alpine

# Instala dependencias del sistema operativo y extensiones de PHP comunes para Laravel
RUN apk add --no-cache \
    git \
    nginx \
    libpq-dev \
    sqlite-dev \
    curl \
    && docker-php-ext-install pdo_mysql pdo_pgsql opcache

# Instala Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configura el directorio de trabajo
WORKDIR /app

# Copia los archivos del proyecto (excepto lo ignorado en .dockerignore)
COPY . /app

# 🚨 LÍNEA DE CORRECCIÓN AÑADIDA 🚨
# Crea el archivo .env a partir del .env.example para que los comandos artisan puedan usarlos.
RUN cp .env.example .env

# Instala las dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Genera la clave de aplicación y limpia cachés
RUN php artisan key:generate
RUN php artisan config:clear
RUN php artisan cache:clear

# Establece los permisos correctos para Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Comando para iniciar Nginx y PHP-FPM (el servidor web)
CMD sh -c "nginx && php-fpm"

# Expón el puerto 80 (puerto estándar del servidor web)
EXPOSE 80
