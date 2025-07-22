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
    private $vite_host = 'http://host.docker.internal';
    private $manifest_path;
    private $manifest = null;
    private $is_dev = false;
    
    public function __construct() {
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
    // -- КІНЕЦЬ ЗМІН --

    private function check_dev_mode() {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            // Використовуємо fsockopen для швидшої перевірки без таймаутів
            $connection = @fsockopen( 'host.docker.internal', $this->vite_port, $errno, $errstr, 0.1 );
            if ( is_resource( $connection ) ) {
                $this->is_dev = true;
                fclose( $connection );
            }
        }
    }
    
    private function load_manifest() {
        if (!$this->is_dev && file_exists($this->manifest_path)) {
            $this->manifest = json_decode(file_get_contents($this->manifest_path), true);
        }
    }
    
    public function get_asset_url($entry) {
        if ($this->is_dev) {
            return $this->vite_host . ':' . $this->vite_port . '/' . $entry;
        }
        
        if ($this->manifest && isset($this->manifest[$entry])) {
            return SLAVUTA_THEME_URI . '/dist/' . $this->manifest[$entry]['file'];
        }
        
        return false;
    }
    
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
    
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    
    // -- ПОЧАТОК ЗМІН --
    // Якщо dev режим (використовуємо публічний метод)
    if ($slavuta_vite->is_dev_mode()) {
    // -- КІНЕЦЬ ЗМІН --
        wp_enqueue_script(
            'slavuta-main',
            $slavuta_vite->get_asset_url('assets/js/main.js'),
            array(),
            null,
            true
        );
    } else {
        $css_files = $slavuta_vite->get_css_files('assets/js/main.js');
        foreach ($css_files as $index => $css_file) {
            wp_enqueue_style(
                'slavuta-style-' . $index,
                $css_file,
                array(),
                SLAVUTA_THEME_VERSION
            );
        }
        
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
    
    wp_localize_script('slavuta-main', 'slavutaAjax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('slavuta-ajax-nonce'),
    ));
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