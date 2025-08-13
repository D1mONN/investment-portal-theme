<?php
/**
 * Тема інвестиційного порталу Славутської громади
 * 
 * @package SlavutskaInvestment
 * @version 1.0.0
 * @author CodeMaster
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

// Константи теми
define('SLAVUTSKA_THEME_VERSION', '1.0.0');
define('SLAVUTSKA_THEME_PATH', get_template_directory());
define('SLAVUTSKA_THEME_URI', get_template_directory_uri());

/**
 * Основна конфігурація теми
 */
class SlavutskaInvestmentTheme 
{
    public function __construct() 
    {
        add_action('after_setup_theme', [$this, 'setup_theme']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('init', [$this, 'init_theme']);
        
        // Завантаження модулів
        $this->load_includes();
    }

    /**
     * Налаштування теми
     */
    public function setup_theme() 
    {
        // Підтримка локалізації
        load_theme_textdomain('slavutska-investment', SLAVUTSKA_THEME_PATH . '/languages');
        
        // Підтримка HTML5
        add_theme_support('html5', [
            'search-form', 'comment-form', 'comment-list', 
            'gallery', 'caption', 'script', 'style'
        ]);
        
        // Підтримка зображень для постів
        add_theme_support('post-thumbnails');
        
        // Розміри зображень
        add_image_size('investment-thumbnail', 400, 300, true);
        add_image_size('land-plot-image', 600, 400, true);
        add_image_size('hero-image', 1920, 1080, true);
        
        // Підтримка меню
        register_nav_menus([
            'primary' => __('Головне меню', 'slavutska-investment'),
            'footer' => __('Меню в футері', 'slavutska-investment'),
        ]);
        
        // Підтримка Gutenberg
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');
        
        // Підтримка широких блоків
        add_theme_support('align-wide');
        
        // Кастомні кольори для редактора
        add_theme_support('editor-color-palette', [
            [
                'name'  => __('Основний синій', 'slavutska-investment'),
                'slug'  => 'primary-blue',
                'color' => '#1e40af',
            ],
            [
                'name'  => __('Зелений акцент', 'slavutska-investment'),
                'slug'  => 'accent-green',
                'color' => '#059669',
            ],
            [
                'name'  => __('Золотий', 'slavutska-investment'),
                'slug'  => 'gold',
                'color' => '#f59e0b',
            ],
        ]);
    }

    /**
     * Підключення скриптів та стилів
     */
    public function enqueue_scripts() 
    {
        // Стилі
        wp_enqueue_style(
            'slavutska-investment-style',
            SLAVUTSKA_THEME_URI . '/assets/css/main.css',
            [],
            SLAVUTSKA_THEME_VERSION
        );
        
        // Responsive стилі
        wp_enqueue_style(
            'slavutska-investment-responsive',
            SLAVUTSKA_THEME_URI . '/assets/css/responsive.css',
            ['slavutska-investment-style'],
            SLAVUTSKA_THEME_VERSION
        );
        
        // Скрипти
        wp_enqueue_script(
            'slavutska-investment-main',
            SLAVUTSKA_THEME_URI . '/assets/js/main.js',
            ['jquery'],
            SLAVUTSKA_THEME_VERSION,
            true
        );
        
        // Локалізація скриптів
        wp_localize_script('slavutska-investment-main', 'slavutskaAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('slavutska_nonce'),
            'strings' => [
                'loading' => __('Завантаження...', 'slavutska-investment'),
                'error' => __('Сталася помилка', 'slavutska-investment'),
            ]
        ]);
        
        // Умовні скрипти
        if (is_front_page()) {
            wp_enqueue_script(
                'slavutska-investment-landing',
                SLAVUTSKA_THEME_URI . '/assets/js/modules/landing.js',
                ['slavutska-investment-main'],
                SLAVUTSKA_THEME_VERSION,
                true
            );
        }
    }

    /**
     * Скрипти для адміністрування
     */
    public function admin_enqueue_scripts($hook) 
    {
        // Тільки на сторінках редагування постів
        if (in_array($hook, ['post.php', 'post-new.php'])) {
            wp_enqueue_style(
                'slavutska-admin-style',
                SLAVUTSKA_THEME_URI . '/assets/css/admin.css',
                [],
                SLAVUTSKA_THEME_VERSION
            );
            
            wp_enqueue_script(
                'slavutska-admin-script',
                SLAVUTSKA_THEME_URI . '/assets/js/admin.js',
                ['jquery'],
                SLAVUTSKA_THEME_VERSION,
                true
            );
        }
    }

    /**
     * Ініціалізація теми
     */
    public function init_theme() 
    {
        // Реєстрація сайдбарів
        $this->register_sidebars();
    }

    /**
     * Реєстрація сайдбарів
     */
    private function register_sidebars() 
    {
        register_sidebar([
            'name'          => __('Основний сайдбар', 'slavutska-investment'),
            'id'            => 'primary-sidebar',
            'description'   => __('Головна бічна панель сайту', 'slavutska-investment'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ]);
        
        register_sidebar([
            'name'          => __('Футер', 'slavutska-investment'),
            'id'            => 'footer-widgets',
            'description'   => __('Віджети в футері', 'slavutska-investment'),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="footer-widget-title">',
            'after_title'   => '</h4>',
        ]);
    }

    /**
     * Завантаження додаткових файлів
     */
    private function load_includes() 
    {
        $includes = [
            'inc/custom-post-types.php',
            'inc/custom-fields.php',
            'inc/security.php',
            'inc/performance.php',
            'inc/admin-customization.php',
            'inc/rest-api-extensions.php',
        ];

        foreach ($includes as $file) {
            $filepath = SLAVUTSKA_THEME_PATH . '/' . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
    }
}

// Ініціалізація теми
new SlavutskaInvestmentTheme();

/**
 * Хелпер функції
 */

/**
 * Безпечне отримання опції теми
 */
function slavutska_get_option($option, $default = '') 
{
    return get_theme_mod($option, $default);
}

/**
 * Генерація безпечного URL
 */
function slavutska_secure_url($url) 
{
    return esc_url($url);
}

/**
 * Безпечний вивід тексту
 */
function slavutska_safe_text($text) 
{
    return wp_kses_post($text);
}

/**
 * Отримання зображення із безпекою
 */
function slavutska_get_image($id, $size = 'full', $attr = []) 
{
    if (!$id) return '';
    
    return wp_get_attachment_image($id, $size, false, $attr);
}

/**
 * Генерація nonce для форм
 */
function slavutska_nonce_field($action = 'slavutska_action') 
{
    wp_nonce_field($action, $action . '_nonce');
}

/**
 * Перевірка nonce
 */
function slavutska_verify_nonce($nonce, $action = 'slavutska_action') 
{
    return wp_verify_nonce($nonce, $action);
}