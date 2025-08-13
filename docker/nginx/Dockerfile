# Dockerfile для Nginx з оптимізаціями
FROM nginx:1.25-alpine

# Встановлення додаткових пакетів
RUN apk add --no-cache \
    curl \
    wget \
    openssl \
    certbot \
    certbot-nginx

# Створення користувача nginx
RUN addgroup -g 1000 -S slavutska && \
    adduser -u 1000 -D -S -G slavutska slavutska

# Створення необхідних директорій
RUN mkdir -p /var/cache/nginx/client_temp \
    /var/cache/nginx/proxy_temp \
    /var/cache/nginx/fastcgi_temp \
    /var/cache/nginx/uwsgi_temp \
    /var/cache/nginx/scgi_temp \
    /var/log/nginx \
    /etc/nginx/ssl \
    /etc/nginx/conf.d \
    /var/www/html

# Налаштування прав доступу
RUN chown -R slavutska:slavutska /var/cache/nginx \
    /var/log/nginx \
    /etc/nginx/ssl \
    /var/www/html

# Копіювання конфігурації Nginx
COPY nginx.conf /etc/nginx/nginx.conf
COPY conf.d/ /etc/nginx/conf.d/

# Створення SSL сертифікатів для розробки
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/slavutska.key \
    -out /etc/nginx/ssl/slavutska.crt \
    -subj "/C=UA/ST=Khmelnytskyi/L=Slavuta/O=Slavutska Hromada/CN=slavutska.local"

# Встановлення прав доступу на SSL сертифікати
RUN chmod 600 /etc/nginx/ssl/slavutska.key \
    && chmod 644 /etc/nginx/ssl/slavutska.crt

# Копіювання скриптів
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Створення індексної сторінки за замовчуванням
RUN echo '<!DOCTYPE html><html><head><title>Slavutska Investment Portal</title></head><body><h1>Завантаження...</h1></body></html>' > /var/www/html/index.html

# Перевірка конфігурації Nginx
RUN nginx -t

# Встановлення робочої директорії
WORKDIR /var/www/html

# Відкриття портів
EXPOSE 80 443

# Перемикання на користувача slavutska
USER slavutska

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/nginx-health || exit 1

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]