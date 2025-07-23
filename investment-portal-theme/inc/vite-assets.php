<?php
/**
 * Інтеграція з Vite.js для роботи з асетами
 * * @package SlavutaInvest
 */

// FILE: inc/vite-assets.php

// Захист від прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Клас для роботи з Vite
 */
class SlavutaViteAssets {
    
    private $vite_port = 5173;
    // localhost.ip це локальний IP комп'ютера прописується у файлі host, використовується з метою уніфікації середовища розробки
    private $vite_host = 'http://172.26.89.134';
    private $manifest_path;
    private $manifest = null;
    private $is_dev = false;
    
    public function __construct() {
        // Отримуємо налаштування з wp-config.php або використовуємо дефолтні
        if (defined('VITE_HOST')) {
            $this->vite_host = VITE_HOST;
        }
        if (defined('VITE_PORT')) {
            $this->vite_port = VITE_PORT;
        }
        $this->manifest_path = SLAVUTA_THEME_PATH . '/dist/.vite/manifest.json';
        $this->check_dev_mode();
        $this->load_manifest();
    }
    
    // -- ПОЧАТОК ЗМІН --
    /**
     * Публічний метод для перевірки режиму розробки.
     * @return bool
     */
    public function is_dev_mode() {
        return $this->is_dev;
    }

    /**
     * Перевірка режиму розробки
     */
    private function check_dev_mode() {
        // Перевіряємо тільки якщо WP_DEBUG увімкнено
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            // Перевіряємо чи визначено VITE_DEV_MODE в wp-config.php
            if (defined('VITE_DEV_MODE') && VITE_DEV_MODE === true) {
                $this->is_dev = true;
                return;
            }

            // Автоматична перевірка доступності Vite сервера
            $host = parse_url($this->vite_host, PHP_URL_HOST);
            $connection = @fsockopen($host ?: 'localhost', $this->vite_port, $errno, $errstr, 0.1);
            if (is_resource($connection)) {
                $this->is_dev = true;
                fclose($connection);
            }
        }
    }

    /**
     * Завантаження manifest файлу
     */
    private function load_manifest() {
        if (!$this->is_dev && file_exists($this->manifest_path)) {
            $manifest_content = file_get_contents($this->manifest_path);
            if ($manifest_content) {
                $this->manifest = json_decode($manifest_content, true);
            }
        }
    }

    /**
     * Отримання URL для асету
     */
    public function get_asset_url($entry) {
        if ($this->is_dev) {
            return $this->vite_host . ':' . $this->vite_port . '/' . $entry;
        }

        if ($this->manifest && isset($this->manifest[$entry])) {
            return SLAVUTA_THEME_URI . '/dist/' . $this->manifest[$entry]['file'];
        }

        return false;
    }

    /**
     * Отримання CSS файлів для ентрі
     */
    public function get_css_files($entry) {
        $css_files = array();

        if (!$this->is_dev && $this->manifest && isset($this->manifest[$entry])) {
            if (isset($this->manifest[$entry]['css'])) {
                foreach ($this->manifest[$entry]['css'] as $css_file) {
                    $css_files[] = SLAVUTA_THEME_URI . '/dist/' . $css_file;
                }
            }
        }

        return $css_files;
    }

    /**
     * Рендер тегів для dev режиму
     */
    public function render_dev_tags() {
        if ($this->is_dev) {
            echo '<script type="module" src="' . esc_url($this->vite_host . ':' . $this->vite_port . '/@vite/client') . '"></script>' . "\n";
        }
    }
}

// Ініціалізація
$slavuta_vite = new SlavutaViteAssets();

/**
 * Підключення стилів та скриптів теми
 */
function slavuta_enqueue_assets() {
    global $slavuta_vite;

    // Видаляємо непотрібні стилі WordPress
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style'); // WooCommerce блоки
    wp_dequeue_style('classic-theme-styles');

    // Видаляємо глобальні стилі WordPress 5.9+
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

    // Основні асети теми
    if ($slavuta_vite->is_dev_mode()) {
        // Dev режим - тільки JS з HMR
        wp_enqueue_script(
            'slavuta-main',
            $slavuta_vite->get_asset_url('assets/js/main.js'),
            array(),
            null,
            true
        );
    } else {
        // Production режим
        // CSS файли
        $css_files = $slavuta_vite->get_css_files('assets/js/main.js');
        foreach ($css_files as $index => $css_file) {
            wp_enqueue_style(
                'slavuta-style-' . $index,
                $css_file,
                array(),
                SLAVUTA_THEME_VERSION
            );
        }

        // JS файл
        $js_url = $slavuta_vite->get_asset_url('assets/js/main.js');
        if ($js_url) {
            wp_enqueue_script(
                'slavuta-main',
                $js_url,
                array(),
                SLAVUTA_THEME_VERSION,
                true
            );
        }
    }

    // Локалізація для AJAX
    wp_localize_script('slavuta-main', 'slavutaAjax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('slavuta-ajax-nonce'),
        'i18n' => array(
            'loading' => __('Завантаження...', 'slavuta-invest'),
            'error' => __('Виникла помилка. Спробуйте пізніше.', 'slavuta-invest'),
            'no_results' => __('Нічого не знайдено', 'slavuta-invest'),
        ),
        'maps_api_key' => defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : '',
    ));

    // Видаляємо jQuery якщо не потрібно
    if (!is_admin()) {
        wp_deregister_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'slavuta_enqueue_assets');

/**
 * Додавання модульного типу для скриптів
 */
function slavuta_add_module_type($tag, $handle, $src) {
    if (strpos($handle, 'slavuta-') === 0) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}
add_filter('script_loader_tag', 'slavuta_add_module_type', 10, 3);

/**
 * Додавання Vite тегів в head
 */
function slavuta_vite_head_tags() {
    global $slavuta_vite;
    $slavuta_vite->render_dev_tags();
}
add_action('wp_head', 'slavuta_vite_head_tags', 1);

/**
 * Preload важливих ресурсів
 */
function slavuta_preload_resources() {
    global $slavuta_vite;

    if (!$slavuta_vite->is_dev_mode()) {
        // Preload шрифтів
        echo '<link rel="preload" href="' . SLAVUTA_THEME_URI . '/assets/fonts/your-font.woff2" as="font" type="font/woff2" crossorigin>' . "\n";

        // Preload критичних зображень (для головної сторінки)
        if (is_front_page()) {
            $hero_image = get_field('hero_background', 'option');
            if ($hero_image && isset($hero_image['url'])) {
                echo '<link rel="preload" href="' . esc_url($hero_image['url']) . '" as="image">' . "\n";
            }
        }
    }
}
add_action('wp_head', 'slavuta_preload_resources', 2);

/**
 * Додавання async/defer атрибутів до скриптів
 */
function slavuta_async_defer_scripts($tag, $handle, $src) {
    // Додаємо defer до некритичних скриптів
    $defer_scripts = array('slavuta-main');

    if (in_array($handle, $defer_scripts)) {
        return '<script src="' . esc_url($src) . '" defer></script>';
    }

    return $tag;
}
add_filter('script_loader_tag', 'slavuta_async_defer_scripts', 10, 3);