#!/bin/bash

# Скрипт автоматичного налаштування Славутського інвестиційного порталу
# Версія: 1.0.0
# Автор: CodeMaster

set -euo pipefail

# Кольори для виводу
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m' # No Color

# Константи
PROJECT_NAME="Славутський інвестиційний портал"
PROJECT_DIR="$(pwd)"
THEME_DIR="src/themes/slavutska-investment"
DOCKER_COMPOSE_FILE="docker/docker-compose.yml"
ENV_FILE=".env"
ENV_EXAMPLE=".env.example"

# Функції для логування
print_header() {
    echo -e "${PURPLE}========================================${NC}"
    echo -e "${WHITE}  $1${NC}"
    echo -e "${PURPLE}========================================${NC}"
}

print_step() {
    echo -e "${CYAN}[КРОК]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1" >&2
}

print_info() {
    echo -e "${BLUE}[i]${NC} $1"
}

# Функція для перевірки команд
check_command() {
    if ! command -v "$1" &> /dev/null; then
        print_error "Команда '$1' не знайдена. Будь ласка, встановіть $2"
        return 1
    fi
}

# Перевірка системних вимог
check_requirements() {
    print_step "Перевірка системних вимог..."
    
    local requirements_met=true
    
    # Перевірка Docker
    if ! check_command "docker" "Docker"; then
        requirements_met=false
    fi
    
    # Перевірка Docker Compose
    if ! check_command "docker-compose" "Docker Compose"; then
        requirements_met=false
    fi
    
    # Перевірка Node.js
    if ! check_command "node" "Node.js (версія 16+)"; then
        requirements_met=false
    else
        local node_version
        node_version=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
        if [ "$node_version" -lt 16 ]; then
            print_error "Потрібна Node.js версія 16 або вища. Поточна версія: $(node --version)"
            requirements_met=false
        fi
    fi
    
    # Перевірка npm
    if ! check_command "npm" "npm"; then
        requirements_met=false
    fi
    
    # Перевірка git
    if ! check_command "git" "Git"; then
        requirements_met=false
    fi
    
    if [ "$requirements_met" = false ]; then
        print_error "Не всі системні вимоги виконані. Будь ласка, встановіть необхідні інструменти."
        exit 1
    fi
    
    print_success "Всі системні вимоги виконані"
}

# Створення .env файлу
create_env_file() {
    print_step "Створення файлу конфігурації..."
    
    if [ ! -f "$ENV_EXAMPLE" ]; then
        print_error "Файл $ENV_EXAMPLE не знайдено"
        exit 1
    fi
    
    if [ -f "$ENV_FILE" ]; then
        print_warning "Файл $ENV_FILE вже існує"
        read -p "Перезаписати? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_info "Пропускаємо створення .env файлу"
            return
        fi
    fi
    
    cp "$ENV_EXAMPLE" "$ENV_FILE"
    
    # Генерація безпечних паролів
    local db_password
    local db_root_password
    local admin_password
    
    db_password=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)
    db_root_password=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)
    admin_password=$(openssl rand -base64 16 | tr -d "=+/" | cut -c1-12)
    
    # Заміна паролів у .env файлі
    sed -i.bak "s/secure_password_here/$db_password/g" "$ENV_FILE"
    sed -i.bak "s/root_secure_password_here/$db_root_password/g" "$ENV_FILE"
    
    # Генерація WordPress ключів безпеки
    print_info "Генерація WordPress ключів безпеки..."
    local wp_keys
    wp_keys=$(curl -s https://api.wordpress.org/secret-key/1.1/salt/ | grep define)
    
    # Додавання ключів до .env файлу
    {
        echo ""
        echo "# WordPress Security Keys"
        echo "$wp_keys"
    } >> "$ENV_FILE"
    
    # Видалення backup файлу
    rm -f "$ENV_FILE.bak"
    
    print_success "Файл .env створено з безпечними паролями"
    print_info "Пароль бази даних: $db_password"
    print_info "Root пароль бази даних: $db_root_password"
}

# Встановлення Node.js залежностей
install_node_dependencies() {
    print_step "Встановлення Node.js залежностей..."
    
    if [ ! -f "package.json" ]; then
        print_error "Файл package.json не знайдено"
        exit 1
    fi
    
    # Очищення node_modules якщо існує
    if [ -d "node_modules" ]; then
        print_info "Очищення існуючих node_modules..."
        rm -rf node_modules
    fi
    
    # Встановлення залежностей
    npm ci
    
    print_success "Node.js залежності встановлено"
}

# Збірка фронтенд асетів
build_assets() {
    print_step "Збірка фронтенд асетів..."
    
    # Розробницька збірка
    npm run build:dev
    
    print_success "Фронтенд асети зібрано"
}

# Створення необхідних директорій
create_directories() {
    print_step "Створення необхідних директорій..."
    
    local directories=(
        "src/themes/slavutska-investment/assets/dist"
        "src/themes/slavutska-investment/languages"
        "src/plugins"
        "logs"
        "backups"
        "uploads"
    )
    
    for dir in "${directories[@]}"; do
        if [ ! -d "$dir" ]; then
            mkdir -p "$dir"
            print_info "Створено директорію: $dir"
        fi
    done
    
    print_success "Необхідні директорії створено"
}

# Встановлення правильних прав доступу
set_permissions() {
    print_step "Встановлення прав доступу..."
    
    # Права для загальних файлів
    find . -type f -name "*.php" -exec chmod 644 {} \;
    find . -type f -name "*.css" -exec chmod 644 {} \;
    find . -type f -name "*.js" -exec chmod 644 {} \;
    
    # Права для виконуваних файлів
    find scripts/ -type f -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true
    
    # Права для директорій
    find src/ -type d -exec chmod 755 {} \; 2>/dev/null || true
    
    print_success "Права доступу встановлено"
}

# Запуск Docker контейнерів
start_docker() {
    print_step "Запуск Docker контейнерів..."
    
    if [ ! -f "$DOCKER_COMPOSE_FILE" ]; then
        print_error "Файл $DOCKER_COMPOSE_FILE не знайдено"
        exit 1
    fi
    
    # Зупинка існуючих контейнерів
    docker-compose -f "$DOCKER_COMPOSE_FILE" down 2>/dev/null || true
    
    # Збірка та запуск контейнерів
    docker-compose -f "$DOCKER_COMPOSE_FILE" up -d --build
    
    print_success "Docker контейнери запущено"
}

# Очікування готовності сервісів
wait_for_services() {
    print_step "Очікування готовності сервісів..."
    
    local max_attempts=60
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if curl -f http://localhost:80 &>/dev/null; then
            print_success "Сервіси готові до роботи"
            return 0
        fi
        
        if [ $attempt -eq $max_attempts ]; then
            print_error "Сервіси не запустилися після $max_attempts спроб"
            return 1
        fi
        
        print_info "Спроба $attempt/$max_attempts - очікування..."
        sleep 5
        ((attempt++))
    done
}

# Ініціалізація git репозиторію
init_git() {
    print_step "Ініціалізація Git репозиторію..."
    
    if [ -d ".git" ]; then
        print_info "Git репозиторій вже ініціалізовано"
        return
    fi
    
    git init
    
    # Створення .gitignore якщо не існує
    if [ ! -f ".gitignore" ]; then
        cat > .gitignore << 'EOF'
# Environment files
.env
.env.local
.env.production

# Dependencies
node_modules/
vendor/

# Build outputs
/src/themes/slavutska-investment/assets/dist/
*.min.js
*.min.css

# Logs
logs/
*.log
npm-debug.log*

# Runtime data
pids
*.pid
*.seed
*.pid.lock

# Coverage directory used by tools like istanbul
coverage/

# nyc test coverage
.nyc_output

# Dependency directories
jspm_packages/

# Optional npm cache directory
.npm

# Optional REPL history
.node_repl_history

# Output of 'npm pack'
*.tgz

# Yarn Integrity file
.yarn-integrity

# dotenv environment variables file
.env.test

# parcel-bundler cache (https://parceljs.org/)
.cache
.parcel-cache

# next.js build output
.next

# nuxt.js build output
.nuxt

# vuepress build output
.vuepress/dist

# Serverless directories
.serverless

# FuseBox cache
.fusebox/

# DynamoDB Local files
.dynamodb/

# WordPress
wp-config.php
wp-content/uploads/
wp-content/cache/

# IDE files
.vscode/
.idea/
*.swp
*.swo

# OS generated files
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# Backup files
*.sql
*.backup
backups/

# Docker
docker-compose.override.yml
EOF
    fi
    
    git add .
    git commit -m "Initial commit: Slavutska Investment Portal setup"
    
    print_success "Git репозиторій ініціалізовано"
}

# Виведення інформації про завершення
print_completion_info() {
    print_header "НАЛАШТУВАННЯ ЗАВЕРШЕНО"
    
    echo -e "${GREEN}🎉 Славутський інвестиційний портал успішно налаштовано!${NC}"
    echo ""
    echo -e "${WHITE}Доступ до сайту:${NC}"
    echo -e "  ${CYAN}HTTP:${NC}  http://localhost"
    echo -e "  ${CYAN}HTTPS:${NC} https://localhost"
    echo ""
    echo -e "${WHITE}Адміністративна панель:${NC}"
    echo -e "  ${CYAN}URL:${NC} http://localhost/wp-admin/"
    echo -e "  ${CYAN}Логін:${NC} admin"
    echo -e "  ${CYAN}Пароль:${NC} admin123"
    echo ""
    echo -e "${WHITE}База даних:${NC}"
    echo -e "  ${CYAN}phpMyAdmin:${NC} http://localhost:8080"
    echo -e "  ${CYAN}Хост:${NC} localhost:3306"
    echo -e "  ${CYAN}База:${NC} slavutska_investment"
    echo ""
    echo -e "${WHITE}Інші сервіси:${NC}"
    echo -e "  ${CYAN}MailHog:${NC} http://localhost:8025"
    echo ""
    echo -e "${WHITE}Команди розробки:${NC}"
    echo -e "  ${CYAN}npm run dev${NC}     - Збірка в режимі розробки з відстеженням"
    echo -e "  ${CYAN}npm run build${NC}   - Продакшн збірка"
    echo -e "  ${CYAN}npm run lint${NC}    - Перевірка коду"
    echo ""
    echo -e "${YELLOW}Примітка:${NC} Перший запуск може зайняти кілька хвилин для ініціалізації бази даних."
}

# Функція очищення при помилці
cleanup() {
    print_error "Сталася помилка під час налаштування"
    print_info "Очищення..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" down 2>/dev/null || true
    exit 1
}

# Основна функція
main() {
    # Встановлення trap для обробки помилок
    trap cleanup ERR INT TERM
    
    print_header "НАЛАШТУВАННЯ $PROJECT_NAME"
    
    # Перевірка що ми в правильній директорії
    if [ ! -f "package.json" ] || [ ! -f "$ENV_EXAMPLE" ]; then
        print_error "Запустіть скрипт з кореневої директорії проекту"
        exit 1
    fi
    
    # Виконання кроків налаштування
    check_requirements
    create_env_file
    create_directories
    install_node_dependencies
    build_assets
    set_permissions
    start_docker
    wait_for_services
    init_git
    
    # Виведення інформації про завершення
    print_completion_info
}

# Запуск скрипту
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi