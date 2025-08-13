#!/bin/bash
set -euo pipefail

# Кольори для виводу
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Функція для логування
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}" >&2
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

# Перевірка змінних середовища
check_env_vars() {
    local required_vars=(
        "WORDPRESS_DB_HOST"
        "WORDPRESS_DB_USER" 
        "WORDPRESS_DB_PASSWORD"
        "WORDPRESS_DB_NAME"
    )
    
    for var in "${required_vars[@]}"; do
        if [[ -z "${!var:-}" ]]; then
            error "Required environment variable $var is not set"
            exit 1
        fi
    done
}

# Очікування доступності бази даних
wait_for_db() {
    log "Waiting for database connection..."
    
    local max_attempts=30
    local attempt=1
    
    while [[ $attempt -le $max_attempts ]]; do
        if mysqladmin ping -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" --silent; then
            log "Database is ready!"
            break
        fi
        
        if [[ $attempt -eq $max_attempts ]]; then
            error "Could not connect to database after $max_attempts attempts"
            exit 1
        fi
        
        warning "Database not ready yet, attempt $attempt/$max_attempts"
        sleep 2
        ((attempt++))
    done
}

# Створення wp-config.php
create_wp_config() {
    if [[ ! -f wp-config.php ]]; then
        log "Creating wp-config.php..."
        
        # Генерація ключів безпеки
        log "Generating WordPress security keys..."
        local keys
        keys=$(curl -s https://api.wordpress.org/secret-key/1.1/salt/)
        
        # Створення wp-config.php
        cat > wp-config.php << EOF
<?php
/**
 * WordPress конфігурація для Славутського інвестиційного порталу
 * Автоматично згенеровано Docker контейнером
 */

// ** Налаштування MySQL ** //
define('DB_NAME', '$WORDPRESS_DB_NAME');
define('DB_USER', '$WORDPRESS_DB_USER');
define('DB_PASSWORD', '$WORDPRESS_DB_PASSWORD');
define('DB_HOST', '$WORDPRESS_DB_HOST');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** Префікс таблиць ** //
\$table_prefix = '${WORDPRESS_TABLE_PREFIX:-wp_}';

// ** Ключі автентифікації ** //
$keys

// ** Налаштування локалізації ** //
define('WPLANG', 'uk');

// ** Налаштування безпеки ** //
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);
define('FORCE_SSL_ADMIN', true);

// ** Налаштування продуктивності ** //
define('WP_CACHE', true);
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('CONCATENATE_SCRIPTS', false);
define('WP_MEMORY_LIMIT', '512M');

// ** Налаштування Redis кешування ** //
define('WP_REDIS_HOST', 'redis');
define('WP_REDIS_PORT', 6379);
define('WP_REDIS_TIMEOUT', 1);
define('WP_REDIS_READ_TIMEOUT', 1);
define('WP_REDIS_DATABASE', 0);

// ** Налаштування дебагу ** //
define('WP_DEBUG', ${WORDPRESS_DEBUG:-false});
define('WP_DEBUG_LOG', ${WORDPRESS_DEBUG_LOG:-false});
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);

// ** Налаштування файлової системи ** //
define('FS_METHOD', 'direct');

// ** Налаштування тимчасових файлів ** //
define('WP_TEMP_DIR', '/tmp');

// ** Додаткові константи ** //
${WORDPRESS_CONFIG_EXTRA:-}

// ** Абсолютний шлях до WordPress ** //
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Налаштування WordPress змінних та підключення файлів. */
require_once ABSPATH . 'wp-settings.php';
EOF

        log "wp-config.php created successfully"
    else
        log "wp-config.php already exists"
    fi
}

# Встановлення WordPress
install_wordpress() {
    if ! wp core is-installed --allow-root 2>/dev/null; then
        log "Installing WordPress..."
        
        # Завантаження WordPress
        if [[ ! -f wp-includes/version.php ]]; then
            log "Downloading WordPress core..."
            wp core download --locale=uk --allow-root
        fi
        
        # Встановлення WordPress
        wp core install \
            --url="${WORDPRESS_URL:-http://localhost}" \
            --title="${WORDPRESS_TITLE:-Славутська громада - Інвестиційний портал}" \
            --admin_user="${WORDPRESS_ADMIN_USER:-admin}" \
            --admin_password="${WORDPRESS_ADMIN_PASSWORD:-admin123}" \
            --admin_email="${WORDPRESS_ADMIN_EMAIL:-admin@slavutska.local}" \
            --allow-root
        
        log "WordPress installed successfully"
    else
        log "WordPress is already installed"
    fi
}

# Активація теми
activate_theme() {
    local theme_name="slavutska-investment"
    
    if wp theme is-installed $theme_name --allow-root; then
        if ! wp theme is-active $theme_name --allow-root; then
            log "Activating theme: $theme_name"
            wp theme activate $theme_name --allow-root
        else
            log "Theme $theme_name is already active"
        fi
    else
        warning "Theme $theme_name is not found"
    fi
}

# Встановлення та активація плагінів
install_plugins() {
    local plugins=(
        "redis-cache"
        "wordpress-seo"
        "w3-total-cache"
        "wordfence"
        "updraftplus"
    )
    
    for plugin in "${plugins[@]}"; do
        if ! wp plugin is-installed $plugin --allow-root; then
            log "Installing plugin: $plugin"
            wp plugin install $plugin --allow-root
        fi
        
        if ! wp plugin is-active $plugin --allow-root; then
            log "Activating plugin: $plugin"
            wp plugin activate $plugin --allow-root
        fi
    done
}

# Створення необхідних сторінок
create_pages() {
    local pages=(
        "Політика конфіденційності|privacy-policy"
        "Умови використання|terms-of-use"
        "Контакти|contacts"
    )
    
    for page_data in "${pages[@]}"; do
        IFS='|' read -r title slug <<< "$page_data"
        
        if ! wp post exists --post_name="$slug" --post_type=page --allow-root; then
            log "Creating page: $title"
            wp post create \
                --post_type=page \
                --post_title="$title" \
                --post_name="$slug" \
                --post_status=publish \
                --allow-root
        fi
    done
}

# Налаштування permalink структури
setup_permalinks() {
    log "Setting up permalinks..."
    wp rewrite structure '/%postname%/' --allow-root
    wp rewrite flush --allow-root
}

# Оптимізація бази даних
optimize_database() {
    log "Optimizing database..."
    wp db optimize --allow-root
}

# Налаштування кешування
setup_caching() {
    if wp plugin is-active redis-cache --allow-root; then
        log "Enabling Redis cache..."
        wp redis enable --allow-root 2>/dev/null || true
    fi
}

# Створення директорій для логів
create_log_directories() {
    local log_dirs=(
        "/var/log/wordpress"
        "/var/www/html/wp-content/debug"
    )
    
    for dir in "${log_dirs[@]}"; do
        if [[ ! -d $dir ]]; then
            mkdir -p "$dir"
            chmod 755 "$dir"
        fi
    done
}

# Встановлення прав доступу
set_permissions() {
    log "Setting proper file permissions..."
    
    # Файли
    find /var/www/html -type f -exec chmod 644 {} \;
    
    # Директорії
    find /var/www/html -type d -exec chmod 755 {} \;
    
    # wp-config.php
    if [[ -f wp-config.php ]]; then
        chmod 600 wp-config.php
    fi
    
    # .htaccess
    if [[ -f .htaccess ]]; then
        chmod 644 .htaccess
    fi
}

# Очищення тимчасових файлів
cleanup_temp_files() {
    log "Cleaning up temporary files..."
    rm -rf /tmp/wp-*
    rm -rf /var/www/html/wp-content/cache/*
}

# Основна функція
main() {
    log "Starting Slavutska Investment Portal initialization..."
    
    # Перевірка змінних середовища
    check_env_vars
    
    # Очікування бази даних
    wait_for_db
    
    # Створення директорій
    create_log_directories
    
    # Створення конфігурації
    create_wp_config
    
    # Встановлення WordPress
    install_wordpress
    
    # Активація теми
    activate_theme
    
    # Встановлення плагінів
    install_plugins
    
    # Створення сторінок
    create_pages
    
    # Налаштування URL
    setup_permalinks
    
    # Налаштування кешування
    setup_caching
    
    # Оптимізація
    optimize_database
    
    # Встановлення прав доступу
    set_permissions
    
    # Очищення
    cleanup_temp_files
    
    log "Slavutska Investment Portal initialization completed!"
}

# Обробка сигналів
trap 'error "Initialization interrupted"; exit 1' INT TERM

# Виконання головної функції тільки якщо скрипт викликається напряму
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi

# Запуск оригінального entrypoint
exec docker-entrypoint.sh "$@"