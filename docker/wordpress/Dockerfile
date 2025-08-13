# Dockerfile для WordPress з оптимізаціями
FROM wordpress:6.4-php8.2-fpm

# Встановлення додаткових PHP розширень
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libxslt-dev \
    unzip \
    wget \
    curl \
    vim \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        zip \
        intl \
        mbstring \
        xml \
        xsl \
        soap \
        bcmath \
        exif \
        opcache

# Встановлення Redis для кешування
RUN pecl install redis \
    && docker-php-ext-enable redis

# Встановлення ImageMagick
RUN apt-get install -y libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Оптимізація PHP конфігурації
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Встановлення WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Встановлення Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Створення користувача для безпеки
RUN groupadd -r slavutska && useradd -r -g slavutska slavutska

# Встановлення правильних прав доступу
RUN chown -R slavutska:slavutska /var/www/html

# Копіювання скриптів ініціалізації
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Встановлення робочої директорії
WORKDIR /var/www/html

# Налаштування логування
RUN mkdir -p /var/log/wordpress \
    && chown slavutska:slavutska /var/log/wordpress

# Очищення кешу
RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Відкриття порту
EXPOSE 9000

# Перемикання на користувача slavutska
USER slavutska

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]