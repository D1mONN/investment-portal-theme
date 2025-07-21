<?php
/**
 * Інтеграція з Vite.js для роботи з асетами
 * 
 * @package SlavutaInvest
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
    
    /**
     * Порт Vite dev сервера
     */
    private $vite_port = 5173;
    
    /**
     * Хост Vite dev сервера
     */
    private $vite_host = 'http://localhost';
    
    /**
     * Шлях до manifest файлу
     */
    private $manifest_path;
    
    /**
     * Дані з manifest файлу
     */
    private $manifest = null;
    
    /**
     * Чи запущений dev режим
     */
    private $is_dev = false;
    
    /**
     * Конструктор
     */
    public function __construct() {
        $this->manifest_path = SLAVUTA_THEME_PATH . '/dist/.vite/manifest.json';
        $this->check_dev_mode();
        $this->load_manifest();
    }
    
    /**
     * Перевірка чи запущений Vite dev сервер
     */
    private function check_dev_mode() {
        // Перевіряємо лише в режимі розробки
        if (defined('WP_ENV') && WP_ENV === 'development') {
            $response = @file_get_contents($this->vite_host . ':' . $this->vite_port . '/@vite/client', false, stream_context_create([
                'http' => [
                    'timeout' => 1,
                ]
            ]));
            
            $this->is_dev = $response !== false;
        }
    }
    
    /**
     * Завантаження manifest файлу
     */
    private function load_manifest() {
        if (!$this->is_dev && file_exists($this->manifest_path)) {
            $this->manifest = json_decode(file_get_contents($this->manifest_path), true);
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
     * Отримання CSS файлів для entry
     */
    public function get_css_files($entry) {
        $css_files = array();
        
        if (!$this->is_dev && $this->manifest && isset($this->manifest[$entry])) {
            // Основний CSS файл
            if (isset($this->manifest[$entry]['css'])) {
                foreach ($this->manifest[$entry]['css'] as $css_file) {
                    $css_files[] = SLAVUTA_THEME_URI . '/dist/' . $css_file;
                }
            }
        }
        
        return $css_files;
    }
    
    /**
     * Вивід тегів для dev режиму
     */
    public function render_dev_tags() {
        if ($this->is_dev) {
            echo '<script type="module" src="' . $this->vite_host . ':' . $this->vite_port . '/@vite/client"></script>' . "\n";
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
    
    // Видалення стилів Gutenberg
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    
    // Якщо dev режим
    if ($slavuta_vite->is_dev) {
        // В dev режимі стилі підключаються через Vite
        wp_enqueue_script(
            'slavuta-main',
            $slavuta_vite->get_asset_url('assets/js/main.js'),
            array(),
            null,
            true
        );
    } else {
        // Production режим
        // Підключення CSS
        $css_files = $slavuta_vite->get_css_files('assets/js/main.js');
        foreach ($css_files as $index => $css_file) {
            wp_enqueue_style(
                'slavuta-style-' . $index,
                $css_file,
                array(),
                SLAVUTA_THEME_VERSION
            );
        }
        
        // Підключення JS
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
    
    // Локалізація скриптів
    wp_localize_script('slavuta-main', 'slavutaAjax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('slavuta-ajax-nonce'),
        'home_url' => home_url(),
        'is_rtl' => is_rtl(),
        'lang' => defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'uk',
    ));
    
    // Підключення Swiper.js з CDN (для продакшену)
    if (!$slavuta_vite->is_dev) {
        wp_enqueue_style(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            array(),
            '11.0.0'
        );
        
        wp_enqueue_script(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            array(),
            '11.0.0',
            true
        );
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
 * Префетч та прелоад для критичних ресурсів
 */
function slavuta_resource_hints($hints, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://cdn.jsdelivr.net';
    }
    
    if ('preconnect' === $relation_type) {
        $hints[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }
    
    return $hints;
}
add_filter('wp_resource_hints', 'slavuta_resource_hints', 10, 2);

/**
 * Додавання асинхронного завантаження для некритичних скриптів
 */
function slavuta_async_scripts($tag, $handle) {
    $async_scripts = array('swiper');
    
    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' async src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'slavuta_async_scripts', 10, 2);

/**
 * Inline критичних стилів
 */
function slavuta_inline_critical_css() {
    $critical_css = '
        /* Критичні стилі для першого рендеру */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .site-header { position: sticky; top: 0; z-index: 1000; background: #fff; }
        .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    ';
    
    echo '<style id="slavuta-critical-css">' . $critical_css . '</style>';
}
add_action('wp_head', 'slavuta_inline_critical_css', 5);