#!/bin/bash

# Скрипт створення резервних копій Славутського інвестиційного порталу
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

# Конфігурація
PROJECT_NAME="slavutska-investment-portal"
BACKUP_DIR="backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_NAME="${PROJECT_NAME}_backup_${TIMESTAMP}"
DOCKER_COMPOSE_FILE="docker/docker-compose.yml"

# Читання конфігурації з .env файлу
if [ -f ".env" ]; then
    source .env
else
    echo -e "${RED}Файл .env не знайдено${NC}"
    exit 1
fi

# Налаштування бази даних
DB_CONTAINER_NAME="slavutska-investment-portal_mysql_1"
DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="${DB_NAME:-slavutska_investment}"
DB_USER="${DB_USER:-slavutska_user}"
DB_PASSWORD="${DB_PASSWORD:-}"

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

# Показ довідки
show_help() {
    cat << EOF
Скрипт створення резервних копій Славутського інвестиційного порталу

Використання: $0 [ОПЦІЇ]

ОПЦІЇ:
    -h, --help              Показати цю довідку
    -f, --full              Повна резервна копія (база даних + файли + конфігурації)
    -d, --database          Тільки база даних
    -F, --files             Тільки файли
    -c, --config            Тільки конфігурації
    -a, --auto              Автоматичний режим (без підтверджень)
    -o, --output DIR        Директорія для збереження (за замовчуванням: $BACKUP_DIR)
    -k, --keep DAYS         Кількість днів зберігання старих бекапів (за замовчуванням: 7)
    -e, --encrypt           Зашифрувати резервну копію
    -u, --upload            Завантажити в хмарне сховище (якщо налаштовано)

ПРИКЛАДИ:
    $0 -f                   Повна резервна копія
    $0 -d -a                База даних без підтверджень
    $0 -f -e -k 30          Повна зашифрована копія, зберігати 30 днів
    $0 -d -u                База даних з завантаженням в хмару

EOF
}

# Створення директорії для резервних копій
create_backup_dir() {
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
        print_info "Створено директорію для резервних копій: $BACKUP_DIR"
    fi
}

# Перевірка доступності Docker контейнера
check_docker_container() {
    if ! docker ps | grep -q "$DB_CONTAINER_NAME"; then
        # Спроба знайти контейнер з MySQL
        DB_CONTAINER_NAME=$(docker ps --format "table {{.Names}}" | grep mysql | head -n 1 || echo "")
        
        if [ -z "$DB_CONTAINER_NAME" ]; then
            print_error "MySQL контейнер не запущено"
            print_info "Запустіть контейнери командою: docker-compose up -d"
            exit 1
        fi
    fi
    print_info "Використовується MySQL контейнер: $DB_CONTAINER_NAME"
}

# Резервна копія бази даних
backup_database() {
    print_step "Створення резервної копії бази даних..."
    
    local db_backup_file="${BACKUP_DIR}/${BACKUP_NAME}_database.sql"
    
    # Перевірка контейнера
    check_docker_container
    
    # Створення дампу бази даних
    docker exec "$DB_CONTAINER_NAME" mysqldump \
        --user="$DB_USER" \
        --password="$DB_PASSWORD" \
        --host="$DB_HOST" \
        --port="$DB_PORT" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --hex-blob \
        --add-drop-database \
        --databases "$DB_NAME" \
        > "$db_backup_file"
    
    # Перевірка розміру файлу
    local file_size
    file_size=$(du -h "$db_backup_file" | cut -f1)
    
    if [ ! -s "$db_backup_file" ]; then
        print_error "Резервна копія бази даних порожня"
        rm -f "$db_backup_file"
        return 1
    fi
    
    print_success "База даних збережена: $db_backup_file ($file_size)"
    
    # Стиснення файлу
    print_info "Стиснення резервної копії бази даних..."
    gzip "$db_backup_file"
    
    local compressed_size
    compressed_size=$(du -h "${db_backup_file}.gz" | cut -f1)
    print_success "База даних стиснена: ${db_backup_file}.gz ($compressed_size)"
}

# Резервна копія файлів WordPress
backup_files() {
    print_step "Створення резервної копії файлів..."
    
    local files_backup="${BACKUP_DIR}/${BACKUP_NAME}_files.tar.gz"
    
    # Список директорій та файлів для включення
    local include_paths=(
        "src/themes/slavutska-investment"
        "src/plugins"
        "uploads"
    )
    
    # Список шаблонів для виключення
    local exclude_patterns=(
        "*/node_modules/*"
        "*/dist/*"
        "*.log"
        "*.tmp"
        "*/.git/*"
        "*/cache/*"
    )
    
    # Підготовка параметрів exclude
    local exclude_args=()
    for pattern in "${exclude_patterns[@]}"; do
        exclude_args+=(--exclude="$pattern")
    done
    
    # Створення архіву
    tar -czf "$files_backup" \
        "${exclude_args[@]}" \
        "${include_paths[@]}" \
        2>/dev/null || {
        print_warning "Деякі файли недоступні для архівування"
    }
    
    if [ -f "$files_backup" ]; then
        local file_size
        file_size=$(du -h "$files_backup" | cut -f1)
        print_success "Файли збережені: $files_backup ($file_size)"
    else
        print_error "Помилка створення архіву файлів"
        return 1
    fi
}

# Резервна копія конфігурацій
backup_configs() {
    print_step "Створення резервної копії конфігурацій..."
    
    local config_backup="${BACKUP_DIR}/${BACKUP_NAME}_configs.tar.gz"
    
    # Список конфігураційних файлів
    local config_files=(
        ".env.example"
        "docker/"
        "package.json"
        "webpack.config.js"
        "composer.json"
    )
    
    # Фільтрація існуючих файлів
    local existing_files=()
    for file in "${config_files[@]}"; do
        if [ -e "$file" ]; then
            existing_files+=("$file")
        fi
    done
    
    if [ ${#existing_files[@]} -eq 0 ]; then
        print_warning "Конфігураційні файли не знайдено"
        return 0
    fi
    
    # Створення архіву конфігурацій
    tar -czf "$config_backup" \
        --exclude="*.log" \
        --exclude="*.tmp" \
        "${existing_files[@]}"
    
    if [ -f "$config_backup" ]; then
        local file_size
        file_size=$(du -h "$config_backup" | cut -f1)
        print_success "Конфігурації збережені: $config_backup ($file_size)"
    else
        print_error "Помилка створення архіву конфігурацій"
        return 1
    fi
}

# Створення повної резервної копії
backup_full() {
    print_step "Створення повної резервної копії..."
    
    # Створення окремих резервних копій
    backup_database
    backup_files
    backup_configs
    
    # Об'єднання в один архів
    local full_backup="${BACKUP_DIR}/${BACKUP_NAME}_full.tar.gz"
    
    cd "$BACKUP_DIR"
    tar -czf "${BACKUP_NAME}_full.tar.gz" \
        "${BACKUP_NAME}_database.sql.gz" \
        "${BACKUP_NAME}_files.tar.gz" \
        "${BACKUP_NAME}_configs.tar.gz" \
        2>/dev/null
    
    # Видалення окремих файлів
    rm -f \
        "${BACKUP_NAME}_database.sql.gz" \
        "${BACKUP_NAME}_files.tar.gz" \
        "${BACKUP_NAME}_configs.tar.gz"
    
    cd - > /dev/null
    
    if [ -f "$full_backup" ]; then
        local file_size
        file_size=$(du -h "$full_backup" | cut -f1)
        print_success "Повна резервна копія створена: $full_backup ($file_size)"
    else
        print_error "Помилка створення повної резервної копії"
        return 1
    fi
}

# Шифрування резервної копії
encrypt_backup() {
    local backup_file="$1"
    
    if [ ! -f "$backup_file" ]; then
        print_error "Файл для шифрування не знайдено: $backup_file"
        return 1
    fi
    
    print_step "Шифрування резервної копії..."
    
    # Запит паролю
    echo -n "Введіть пароль для шифрування: "
    read -s password
    echo
    
    echo -n "Підтвердіть пароль: "
    read -s password_confirm
    echo
    
    if [ "$password" != "$password_confirm" ]; then
        print_error "Паролі не співпадають"
        return 1
    fi
    
    # Шифрування файлу
    openssl enc -aes-256-cbc -salt -in "$backup_file" -out "${backup_file}.enc" -pass pass:"$password"
    
    if [ -f "${backup_file}.enc" ]; then
        rm "$backup_file"
        print_success "Резервна копія зашифрована: ${backup_file}.enc"
    else
        print_error "Помилка шифрування"
        return 1
    fi
}

# Завантаження в хмарне сховище
upload_to_cloud() {
    local backup_file="$1"
    
    print_step "Завантаження в хмарне сховище..."
    
    # Перевірка налаштувань хмарного сховища
    if [ -z "${CLOUD_STORAGE_TYPE:-}" ]; then
        print_warning "Хмарне сховище не налаштовано"
        return 0
    fi
    
    case "${CLOUD_STORAGE_TYPE}" in
        "s3")
            upload_to_s3 "$backup_file"
            ;;
        "gcs")
            upload_to_gcs "$backup_file"
            ;;
        "azure")
            upload_to_azure "$backup_file"
            ;;
        *)
            print_warning "Невідомий тип хмарного сховища: ${CLOUD_STORAGE_TYPE}"
            ;;
    esac
}

# Завантаження в Amazon S3
upload_to_s3() {
    local backup_file="$1"
    
    if ! command -v aws &> /dev/null; then
        print_error "AWS CLI не встановлено"
        return 1
    fi
    
    local s3_bucket="${S3_BUCKET:-}"
    local s3_path="${S3_PATH:-backups/}"
    
    if [ -z "$s3_bucket" ]; then
        print_error "S3_BUCKET не налаштовано"
        return 1
    fi
    
    aws s3 cp "$backup_file" "s3://${s3_bucket}/${s3_path}$(basename "$backup_file")"
    
    if [ $? -eq 0 ]; then
        print_success "Резервна копія завантажена в S3"
    else
        print_error "Помилка завантаження в S3"
        return 1
    fi
}

# Очищення старих резервних копій
cleanup_old_backups() {
    local keep_days="${1:-7}"
    
    print_step "Очищення резервних копій старіших за $keep_days днів..."
    
    local deleted_count=0
    
    # Знаходження та видалення старих файлів
    while IFS= read -r -d '' file; do
        rm "$file"
        ((deleted_count++))
        print_info "Видалено: $(basename "$file")"
    done < <(find "$BACKUP_DIR" -name "${PROJECT_NAME}_backup_*" -type f -mtime +"$keep_days" -print0)
    
    if [ $deleted_count -eq 0 ]; then
        print_info "Старі резервні копії не знайдено"
    else
        print_success "Видалено $deleted_count старих резервних копій"
    fi
}

# Створення звіту про резервну копію
create_backup_report() {
    local backup_type="$1"
    local backup_files=("$@")
    shift
    
    local report_file="${BACKUP_DIR}/${BACKUP_NAME}_report.txt"
    
    cat > "$report_file" << EOF
Звіт про резервну копію Славутського інвестиційного порталу
============================================================

Дата створення: $(date)
Тип резервної копії: $backup_type
Версія скрипту: 1.0.0

Системна інформація:
- Операційна система: $(uname -s)
- Архітектура: $(uname -m)
- Hostname: $(hostname)

Конфігурація бази даних:
- База даних: $DB_NAME
- Користувач: $DB_USER
- Хост: $DB_HOST:$DB_PORT

Створені файли:
EOF

    for file in "${backup_files[@]}"; do
        if [ -f "$file" ]; then
            local file_size
            file_size=$(du -h "$file" | cut -f1)
            echo "- $(basename "$file") ($file_size)" >> "$report_file"
        fi
    done
    
    echo "" >> "$report_file"
    echo "Загальний розмір резервної копії:" >> "$report_file"
    du -sh "$BACKUP_DIR"/"${BACKUP_NAME}"* | grep -v "_report.txt" >> "$report_file"
    
    print_success "Звіт створено: $report_file"
}

# Основна функція
main() {
    local backup_type="full"
    local auto_mode=false
    local keep_days=7
    local encrypt_flag=false
    local upload_flag=false
    
    # Обробка параметрів командного рядка
    while [[ $# -gt 0 ]]; do
        case $1 in
            -h|--help)
                show_help
                exit 0
                ;;
            -f|--full)
                backup_type="full"
                shift
                ;;
            -d|--database)
                backup_type="database"
                shift
                ;;
            -F|--files)
                backup_type="files"
                shift
                ;;
            -c|--config)
                backup_type="configs"
                shift
                ;;
            -a|--auto)
                auto_mode=true
                shift
                ;;
            -o|--output)
                BACKUP_DIR="$2"
                shift 2
                ;;
            -k|--keep)
                keep_days="$2"
                shift 2
                ;;
            -e|--encrypt)
                encrypt_flag=true
                shift
                ;;
            -u|--upload)
                upload_flag=true
                shift
                ;;
            *)
                print_error "Невідомий параметр: $1"
                show_help
                exit 1
                ;;
        esac
    done
    
    print_header "СТВОРЕННЯ РЕЗЕРВНОЇ КОПІЇ"
    
    # Підтвердження (якщо не автоматичний режим)
    if [ "$auto_mode" = false ]; then
        echo -e "${YELLOW}Тип резервної копії: $backup_type${NC}"
        echo -e "${YELLOW}Директорія: $BACKUP_DIR${NC}"
        echo -e "${YELLOW}Зберігати: $keep_days днів${NC}"
        [ "$encrypt_flag" = true ] && echo -e "${YELLOW}Шифрування: увімкнено${NC}"
        [ "$upload_flag" = true ] && echo -e "${YELLOW}Завантаження в хмару: увімкнено${NC}"
        echo
        read -p "Продовжити? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_info "Скасовано користувачем"
            exit 0
        fi
    fi
    
    # Створення директорії для резервних копій
    create_backup_dir
    
    local created_files=()
    
    # Виконання резервного копіювання
    case $backup_type in
        "full")
            backup_full
            created_files+=("${BACKUP_DIR}/${BACKUP_NAME}_full.tar.gz")
            ;;
        "database")
            backup_database
            created_files+=("${BACKUP_DIR}/${BACKUP_NAME}_database.sql.gz")
            ;;
        "files")
            backup_files
            created_files+=("${BACKUP_DIR}/${BACKUP_NAME}_files.tar.gz")
            ;;
        "configs")
            backup_configs
            created_files+=("${BACKUP_DIR}/${BACKUP_NAME}_configs.tar.gz")
            ;;
    esac
    
    # Шифрування (якщо потрібно)
    if [ "$encrypt_flag" = true ] && [ ${#created_files[@]} -gt 0 ]; then
        local files_to_encrypt=("${created_files[@]}")
        created_files=()
        
        for file in "${files_to_encrypt[@]}"; do
            if encrypt_backup "$file"; then
                created_files+=("${file}.enc")
            fi
        done
    fi
    
    # Завантаження в хмару (якщо потрібно)
    if [ "$upload_flag" = true ] && [ ${#created_files[@]} -gt 0 ]; then
        for file in "${created_files[@]}"; do
            upload_to_cloud "$file"
        done
    fi
    
    # Створення звіту
    create_backup_report "$backup_type" "${created_files[@]}"
    
    # Очищення старих резервних копій
    cleanup_old_backups "$keep_days"
    
    # Підсумок
    print_header "РЕЗЕРВНА КОПІЯ ЗАВЕРШЕНА"
    print_success "Резервна копія успішно створена!"
    
    if [ ${#created_files[@]} -gt 0 ]; then
        echo -e "${WHITE}Створені файли:${NC}"
        for file in "${created_files[@]}"; do
            local file_size
            file_size=$(du -h "$file" | cut -f1)
            echo -e "  ${CYAN}$(basename "$file")${NC} ($file_size)"
        done
    fi
    
    local total_size
    total_size=$(du -sh "$BACKUP_DIR" | cut -f1)
    echo -e "${WHITE}Загальний розмір директорії резервних копій: ${CYAN}$total_size${NC}"
}

# Обробка сигналів
trap 'print_error "Операцію перервано"; exit 1' INT TERM

# Запуск скрипту
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi