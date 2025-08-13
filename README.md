# 🏛️ Інвестиційний портал Славутської громади

[![WordPress](https://img.shields.io/badge/WordPress-6.4+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net/)
[![Node.js](https://img.shields.io/badge/Node.js-16+-green.svg)](https://nodejs.org/)
[![Docker](https://img.shields.io/badge/Docker-20.10+-blue.svg)](https://docker.com/)
[![License](https://img.shields.io/badge/License-GPL--2.0-red.svg)](LICENSE)

Сучасний інвестиційний портал для Славутської міської територіальної громади Хмельницької області. Платформа створена для залучення інвестицій та презентації можливостей громади.

## 📋 Зміст

- [Особливості](#-особливості)
- [Технології](#-технології)
- [Системні вимоги](#-системні-вимоги)
- [Швидкий старт](#-швидкий-старт)
- [Встановлення](#-встановлення)
- [Конфігурація](#-конфігурація)
- [Розробка](#-розробка)
- [Деплоймент](#-деплоймент)
- [Резервне копіювання](#-резервне-копіювання)
- [API документація](#-api-документація)
- [Безпека](#-безпека)
- [Підтримка](#-підтримка)

## 🌟 Особливості

### 🎯 Основний функціонал
- **Інвестиційні пропозиції** - каталог актуальних інвестиційних можливостей
- **Земельні ділянки** - база доступних земельних ресурсів з детальною інформацією
- **Інтерактивні карти** - візуалізація розташування об'єктів
- **Контактні форми** - система зворотного зв'язку з інвесторами
- **Мультимовність** - повна підтримка української мови

### 🔧 Технічні особливості
- **Responsive дизайн** - адаптація під всі пристрої
- **SEO оптимізація** - структуровані дані та метатеги
- **Високу продуктивність** - кешування та оптимізація
- **Безпека** - захист від основних типів атак
- **REST API** - програмний доступ до даних

### 🎨 UX/UI
- **Сучасний дизайн** - використання актуальних трендів
- **Доступність** - відповідність стандартам WCAG
- **Швидкість завантаження** - оптимізовані асети
- **Інтуїтивна навігація** - зручність користування

## 🛠️ Технології

### Backend
- **WordPress 6.4+** - CMS
- **PHP 8.2+** - серверна мова
- **MySQL 8.0+** - база даних
- **Nginx** - веб-сервер
- **Redis** - кешування

### Frontend
- **HTML5** - розмітка
- **CSS3** - стилізація (Flexbox, Grid, Custom Properties)
- **JavaScript ES6+** - інтерактивність
- **Webpack** - збірка асетів

### DevOps
- **Docker** - контейнеризація
- **Docker Compose** - оркестрація
- **Git** - версіонування
- **npm** - управління пакетами

### Інструменти розробки
- **ESLint** - лінтинг JavaScript
- **Prettier** - форматування коду
- **Babel** - транспіляція
- **PostCSS** - обробка CSS

## 📋 Системні вимоги

### Мінімальні вимоги
- **Docker** 20.10+
- **Docker Compose** 1.29+
- **Node.js** 16+
- **npm** 8+
- **Git** 2.30+

### Рекомендовані вимоги
- **RAM** 4GB+
- **Дискове простір** 10GB+
- **CPU** 2+ ядра

### Підтримувані операційні системи
- Ubuntu 20.04+
- Debian 11+
- CentOS 8+
- macOS 11+
- Windows 10+ (з WSL2)

## 🚀 Швидкий старт

```bash
# Клонування репозиторію
git clone https://github.com/slavutska-hromada/investment-portal.git
cd investment-portal

# Автоматичне налаштування
chmod +x scripts/setup.sh
./scripts/setup.sh

# Доступ до сайту
open http://localhost
```

Після виконання цих команд портал буде доступний за адресою http://localhost

## 📥 Встановлення

### 1. Клонування репозиторію
```bash
git clone https://github.com/slavutska-hromada/investment-portal.git
cd investment-portal
```

### 2. Конфігурація середовища
```bash
# Копіювання файлу конфігурації
cp .env.example .env

# Редагування конфігурації
nano .env
```

### 3. Встановлення залежностей
```bash
# Node.js залежності
npm install

# Збірка асетів
npm run build:dev
```

### 4. Запуск Docker контейнерів
```bash
# Запуск всіх сервісів
docker-compose -f docker/docker-compose.yml up -d

# Перевірка статусу
docker-compose -f docker/docker-compose.yml ps
```

### 5. Ініціалізація WordPress
```bash
# Очікування готовності сервісів
sleep 30

# Перехід до сайту
open http://localhost/wp-admin
```

## ⚙️ Конфігурація

### Основні налаштування (.env)
```bash
# База даних
DB_NAME=slavutska_investment
DB_USER=slavutska_user
DB_PASSWORD=your_secure_password

# WordPress
WP_DEBUG=false
WP_TABLE_PREFIX=si_

# Домен
SITE_URL=https://your-domain.com
```

### Налаштування WordPress
1. Увійдіть в адміністративну панель: `/wp-admin`
2. Активуйте тему "Slavutska Investment"
3. Імпортуйте демо-контент (опціонально)
4. Налаштуйте permalink структуру: `/%postname%/`

### Налаштування кешування
```bash
# Активація Redis кешу
wp redis enable --allow-root

# Налаштування object cache
wp plugin activate redis-cache --allow-root
```

## 💻 Розробка

### Команди розробки
```bash
# Розробницький режим з відстеженням
npm run dev

# Продакшн збірка
npm run build

# Лінтинг коду
npm run lint

# Форматування коду
npm run format

# Тестування
npm test
```

### Структура проекту
```
slavutska-investment-portal/
├── docker/                     # Docker конфігурації
│   ├── docker-compose.yml
│   ├── nginx/
│   └── wordpress/
├── src/                        # Вихідний код
│   ├── themes/
│   │   └── slavutska-investment/
│   └── plugins/
├── scripts/                    # Допоміжні скрипти
├── docs/                       # Документація
├── backups/                    # Резервні копії
└── package.json               # Node.js конфігурація
```

### Робота з темою
```bash
# Перехід до директорії теми
cd src/themes/slavutska-investment/

# Структура теми
├── style.css                  # Основний файл теми
├── functions.php              # Функції теми
├── index.php                  # Головний шаблон
├── front-page.php             # Шаблон головної сторінки
├── single-investment.php      # Шаблон інвестицій
├── single-land-plot.php       # Шаблон земельних ділянок
├── archive-investment.php     # Архів інвестицій
├── archive-land-plot.php      # Архів земельних ділянок
├── header.php                 # Шапка сайту
├── footer.php                 # Футер сайту
├── assets/                    # Асети
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── template-parts/            # Частини шаблонів
├── inc/                       # Допоміжні файли
└── languages/                 # Файли локалізації
```

### Створення кастомних полів
```php
// Приклад додавання meta box
add_action('add_meta_boxes', function() {
    add_meta_box(
        'custom_fields',
        'Додаткові поля',
        'custom_fields_callback',
        'investment'
    );
});
```

### Робота з REST API
```javascript
// Отримання інвестицій через API
fetch('/wp-json/slavutska/v1/investments')
    .then(response => response.json())
    .then(data => console.log(data));
```

## 🚀 Деплоймент

### Продакшн сервер
```bash
# Клонування на сервер
git clone https://github.com/slavutska-hromada/investment-portal.git
cd investment-portal

# Налаштування продакшн конфігурації
cp .env.example .env.production
nano .env.production

# Збірка для продакшн
npm run build

# Запуск з продакшн конфігурацією
docker-compose -f docker/docker-compose.yml -f docker/docker-compose.prod.yml up -d
```

### SSL сертифікати
```bash
# Отримання Let's Encrypt сертифікатів
docker exec nginx_container certbot --nginx -d your-domain.com

# Автоматичне оновлення
echo "0 12 * * * /usr/bin/docker exec nginx_container certbot renew --quiet" | crontab -
```

### Налаштування CDN
1. Налаштуйте Cloudflare або інший CDN
2. Увімкніть кешування статичних ресурсів
3. Налаштуйте мінімізацію CSS/JS

## 💾 Резервне копіювання

### Автоматичне резервне копіювання
```bash
# Повна резервна копія
./scripts/backup.sh --full --auto

# Тільки база даних
./scripts/backup.sh --database --auto

# З шифруванням
./scripts/backup.sh --full --encrypt

# З завантаженням в хмару
./scripts/backup.sh --full --upload
```

### Налаштування cron
```bash
# Додавання до crontab
crontab -e

# Щоденне резервне копіювання о 2:00
0 2 * * * /path/to/project/scripts/backup.sh --full --auto --keep 7
```

### Відновлення з резервної копії
```bash
# Відновлення бази даних
docker exec mysql_container mysql -u user -p database < backup.sql

# Відновлення файлів
tar -xzf backup_files.tar.gz -C /path/to/wordpress/
```

## 📚 API документація

### Базовий URL
```
https://your-domain.com/wp-json/slavutska/v1/
```

### Ендпоінти

#### Інвестиції
```bash
# Отримання всіх інвестицій
GET /investments

# Фільтрація за категорією
GET /investments?category=industrial

# Фільтрація за сумою
GET /investments?amount_min=100000&amount_max=1000000

# Тільки рекомендовані
GET /investments?featured_only=true
```

#### Земельні ділянки
```bash
# Отримання всіх ділянок
GET /land-plots

# Фільтрація за площею
GET /land-plots?area_min=1&area_max=10

# Фільтрація за ціною
GET /land-plots?price_min=50000&price_max=100000

# Тільки з координатами
GET /land-plots?has_coordinates=true
```

#### Пошук
```bash
# Глобальний пошук
GET /search?query=промислові&post_types[]=investment&post_types[]=land_plot
```

#### Статистика
```bash
# Загальна статистика
GET /statistics
```

### Приклади відповідей
```json
{
  "investments": [
    {
      "id": 123,
      "title": "Промисловий парк",
      "excerpt": "Опис проекту...",
      "permalink": "https://example.com/investments/industrial-park/",
      "meta": {
        "investment_amount": 5000000,
        "expected_return": 15.5,
        "investment_period": 36,
        "is_featured": true
      }
    }
  ],
  "total": 1,
  "pages": 1,
  "current_page": 1
}
```

## 🔒 Безпека

### Основні заходи безпеки
- **Відключення файлового редактора WordPress**
- **Обмеження спроб входу**
- **Приховування версії WordPress**
- **Безпечні заголовки HTTP**
- **Захист від XSS та CSRF атак**
- **Регулярне оновлення компонентів**

### Рекомендації
1. Використовуйте сильні паролі
2. Увімкніть двофакторну автентифікацію
3. Регулярно оновлюйте систему
4. Слідкуйте за логами безпеки
5. Робіть регулярні резервні копії

### Моніторинг безпеки
```bash
# Перегляд логів безпеки
tail -f /var/log/nginx/error.log
tail -f wp-content/security.log

# Перевірка підозрілої активності
grep "failed login" wp-content/security.log
```

## 🐛 Виправлення проблем

### Часті проблеми

#### Проблема: Сайт не завантажується
```bash
# Перевірка статусу контейнерів
docker-compose ps

# Перегляд логів
docker-compose logs nginx
docker-compose logs wordpress

# Перезапуск сервісів
docker-compose restart
```

#### Проблема: База даних недоступна
```bash
# Перевірка підключення до MySQL
docker exec mysql_container mysqladmin -u user -p ping

# Перегляд логів MySQL
docker-compose logs mysql
```

#### Проблема: Асети не збираються
```bash
# Очищення node_modules
rm -rf node_modules package-lock.json

# Повторна установка
npm install

# Збірка
npm run build
```

### Логи та діагностика
```bash
# Логи Nginx
docker-compose logs nginx

# Логи WordPress
docker-compose logs wordpress

# Логи бази даних
docker-compose logs mysql

# Логи збірки
npm run build 2>&1 | tee build.log
```

## 📖 Додаткова документація

- [Інструкція з розгортання](docs/deployment.md)
- [Налаштування безпеки](docs/security-checklist.md)
- [Керівництво з кастомізації](docs/customization.md)
- [API референс](docs/api-reference.md)

## 🤝 Контрибьюція

Ми вітаємо внески в розвиток проекту! Будь ласка, ознайомтеся з [керівництвом для контрибьюторів](CONTRIBUTING.md).

### Як зробити внесок
1. Fork репозиторій
2. Створіть feature branch (`git checkout -b feature/amazing-feature`)
3. Зробіть коміт змін (`git commit -m 'Add amazing feature'`)
4. Push в branch (`git push origin feature/amazing-feature`)
5. Створіть Pull Request

## 📝 Ліцензія

Цей проект ліцензується під [GPL-2.0 License](LICENSE) - дивіться файл LICENSE для деталей.

## 🆘 Підтримка

### Офіційні канали
- **Email**: tech@slavutska.gov.ua
- **Телефон**: +380 123 456 789
- **Telegram**: @slavutska_support

### Корисні посилання
- [Офіційний сайт громади](https://slavutska.gov.ua)
- [Документація WordPress](https://wordpress.org/support/)
- [Docker документація](https://docs.docker.com/)

### Звітування про помилки
Для звітування про помилки, будь ласка, створіть [issue на GitHub](https://github.com/slavutska-hromada/investment-portal/issues) з детальним описом проблеми.

---

<div align="center">

**Зроблено з ❤️ для Славутської громади**

[![GitHub stars](https://img.shields.io/github/stars/slavutska-hromada/investment-portal?style=social)](https://github.com/slavutska-hromada/investment-portal)
[![GitHub forks](https://img.shields.io/github/forks/slavutska-hromada/investment-portal?style=social)](https://github.com/slavutska-hromada/investment-portal)

</div>