# FILE: docker/Dockerfile (Фінальна версія)

# Беремо за основу офіційний образ WordPress з PHP-FPM
FROM wordpress:6.6-fpm-alpine

# Встановлюємо залежності для збірки, компілюємо Xdebug, а потім видаляємо зайве
RUN set -eux; \
    # Встановлюємо тимчасові залежності для збірки, включаючи linux-headers
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf g++ linux-headers; \
    # Встановлюємо Xdebug
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    # Видаляємо тимчасові залежності, щоб образ залишався маленьким
    apk del .build-deps