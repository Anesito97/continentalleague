# Usa la imagen oficial de PHP con Alpine (es ligera)
FROM php:8.3-fpm-alpine

# 1. INSTALACIÓN DE DEPENDENCIAS DEL SISTEMA
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

# 2. CONFIGURACIÓN DEL PROYECTO
# Configura el directorio de trabajo
WORKDIR /app

# Copia los archivos del proyecto
COPY . /app

# Crea el archivo .env a partir del .env.example.
# ESTO ES CRUCIAL para que los comandos artisan puedan ejecutarse sin el error "No such file".
RUN cp .env.example .env

# 3. OPTIMIZACIÓN DE LARAVEL
# Instala las dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev

# Genera la clave de aplicación y optimiza la app para producción.
# Estos comandos son SEGUROS ya que no dependen de la conexión a la Base de Datos.
# Se ha ELIMINADO 'cache:clear' para evitar el error de conexión a DB/SQLite.
RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan route:cache

# 4. PERMISOS Y ARRANQUE
# Establece los permisos correctos para Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Comando para iniciar Nginx y PHP-FPM (el servidor web)
CMD sh -c "nginx && php-fpm"

# Expón el puerto 80 (puerto estándar del servidor web)
EXPOSE 80
