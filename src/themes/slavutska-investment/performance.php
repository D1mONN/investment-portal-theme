<?php
/**
 * Модуль продуктивності для інвестиційного порталу
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Клас для оптимізації продуктивності сайту
 */
class SlavutskaPerformance 
{
    private $cache_enabled = true;
    private $minify_enabled = true;
    private $lazy_loading_enabled = true;

    public function __construct() 
    {
        add_action('init', [$this, 'init_optimizations']);
        add_action('wp_enqueue_scripts', [$this, 'optimize_scripts_styles'], 999);
        add_action('wp_head', [$this, 'add_performance_meta'], 1);
        add_action('wp_footer', [$this, 'add_performance_scripts'], 999);
        
        // Фільтри для оптимізації
        add_filter('wp_lazy_loading_enabled', [$this, 'enable_lazy_loading']);
        add_filter('script_loader_tag', [$this, 'add_async_defer'], 10, 3);
        add_filter('style_loader_tag', [$this, 'add_preload_styles'], 10, 4);
        add_filter('wp_get_attachment_image_attributes', [$this, 'add_lazy_loading_attrs'], 10, 3);
        
        // Оптимізація запитів до БД
        add_action('pre_get_posts', [$this, 'optimize_queries']);
        
        // Кешування
        add_action('init', [$this, 'init_caching']);
        add_action('save_post', [$this, 'clear_post_cache']);
        add_action('comment_post', [$this, 'clear_comment_cache']);
        
        // Очищення сміття
        add_action('wp_scheduled_delete', [$this, 'cleanup_old_data']);
        add_action('wp', [$this, 'schedule_cleanup']);
        
        // Оптимізація зображень
        add_filter('jpeg_quality', [$this, 'set_jpeg_quality']);
        add_filter('wp_editor_set_quality', [$this, 'set_image_quality']);
        
        // Відключення непотрібних функцій
        $this->disable_unnecessary_features();
    }

    /**
     * Ініціалізація оптимізацій
     */
    public function init_optimizations() 
    {
        // Увімкнення GZIP компресії
        if (!ob_get_level() && extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        }
        
        // Встановлення заголовків кешування
        $this->set_cache_headers();
        
        // Оптимізація автозавантаження
        $this->optimize_autoload();
        
        // Оптимізація запитів
        $this->optimize_database_queries();
    }

    /**
     * Додавання мета-тегів для продуктивності
     */
    public function add_performance_meta() 
    {
        echo "\n<!-- Performance Optimizations -->\n";
        echo '<meta http-equiv="Cache-Control" content="public, max-age=31536000">' . "\n";
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        
        // Preload критичних ресурсів
        $critical_css = SLAVUTSKA_THEME_URI . '/assets/css/critical.css';
        if (file_exists(SLAVUTSKA_THEME_PATH . '/assets/css/critical.css')) {
            echo '<link rel="preload" href="' . esc_url($critical_css) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        }
        
        // Resource hints
        echo '<link rel="prefetch" href="' . esc_url(home_url('/investments/')) . '">' . "\n";
        echo '<link rel="prefetch" href="' . esc_url(home_url('/land-plots/')) . '">' . "\n";
    }

    /**
     * Оптимізація скриптів та стилів
     */
    public function optimize_scripts_styles() 
    {
        if (is_admin()) {
            return;
        }
        
        // Видалення непотрібних скриптів
        wp_dequeue_script('wp-embed');
        wp_dequeue_style('wp-block-library');
        
        // Переміщення jQuery у footer
        if (!is_admin()) {
            wp_deregister_script('jquery');
            wp_register_script('jquery', 
                includes_url('/js/jquery/jquery.min.js'), 
                false, 
                null, 
                true
            );
            wp_enqueue_script('jquery');
        }
        
        // Мінімізація CSS та JS
        if ($this->minify_enabled && !WP_DEBUG) {
            add_filter('style_loader_src', [$this, 'minify_css_files'], 10, 2);
            add_filter('script_loader_src', [$this, 'minify_js_files'], 10, 2);
        }
    }

    /**
     * Додавання async/defer до скриптів
     */
    public function add_async_defer($tag, $handle, $src) 
    {
        // Скрипти для async завантаження
        $async_scripts = [
            'slavutska-investment-main',
            'google-analytics',
            'gtag'
        ];
        
        // Скрипти для defer завантаження
        $defer_scripts = [
            'slavutska-investment-landing',
            'contact-form-script'
        ];
        
        if (in_array($handle, $async_scripts)) {
            return str_replace('<script ', '<script async ', $tag);
        }
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace('<script ', '<script defer ', $tag);
        }
        
        return $tag;
    }

    /**
     * Додавання preload до критичних стилів
     */
    public function add_preload_styles($html, $handle, $href, $media) 
    {
        $critical_styles = [
            'slavutska-investment-style',
            'slavutska-investment-critical'
        ];
        
        if (in_array($handle, $critical_styles)) {
            $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        }
        
        return $html;
    }

    /**
     * Увімкнення lazy loading для зображень
     */
    public function enable_lazy_loading($default) 
    {
        return $this->lazy_loading_enabled ? true : $default;
    }

    /**
     * Додавання атрибутів lazy loading
     */
    public function add_lazy_loading_attrs($attr, $attachment, $size) 
    {
        if ($this->lazy_loading_enabled && !is_admin()) {
            $attr['loading'] = 'lazy';
            $attr['decoding'] = 'async';
        }
        
        return $attr;
    }

    /**
     * Оптимізація запитів
     */
    public function optimize_queries($query) 
    {
        if (!is_admin() && $query->is_main_query()) {
            // Оптимізація для головної сторінки
            if ($query->is_home()) {
                $query->set('posts_per_page', 6);
                $query->set('meta_query', [
                    [
                        'key' => '_is_featured',
                        'value' => '1',
                        'compare' => '='
                    ]
                ]);
            }
            
            // Відключення непотрібних мета-запитів
            $query->set('no_found_rows', true);
            $query->set('update_post_meta_cache', false);
            $query->set('update_post_term_cache', false);
        }
    }

    /**
     * Ініціалізація кешування
     */
    public function init_caching() 
    {
        if (!$this->cache_enabled) {
            return;
        }
        
        // Кешування об'єктів
        if (!wp_using_ext_object_cache()) {
            wp_cache_init();
        }
        
        // Кешування запитів
        add_action('pre_get_posts', [$this, 'cache_queries']);
        add_filter('posts_pre_query', [$this, 'get_cached_posts'], 10, 2);
    }

    /**
     * Кешування запитів
     */
    public function cache_queries($query) 
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $cache_key = 'slavutska_query_' . md5(serialize($query->query_vars));
        $cached_posts = wp_cache_get($cache_key, 'slavutska_posts');
        
        if (false === $cached_posts) {
            add_action('the_posts', function($posts) use ($cache_key) {
                wp_cache_set($cache_key, $posts, 'slavutska_posts', HOUR_IN_SECONDS);
                return $posts;
            });
        }
    }

    /**
     * Отримання кешованих постів
     */
    public function get_cached_posts($posts, $query) 
    {
        if (is_admin() || !$query->is_main_query()) {
            return $posts;
        }
        
        $cache_key = 'slavutska_query_' . md5(serialize($query->query_vars));
        $cached_posts = wp_cache_get($cache_key, 'slavutska_posts');
        
        if (false !== $cached_posts) {
            return $cached_posts;
        }
        
        return $posts;
    }

    /**
     * Очищення кешу постів
     */
    public function clear_post_cache($post_id) 
    {
        wp_cache_delete_group('slavutska_posts');
        wp_cache_delete("post_meta_{$post_id}", 'slavutska_meta');
    }

    /**
     * Очищення кешу коментарів
     */
    public function clear_comment_cache($comment_id) 
    {
        $comment = get_comment($comment_id);
        if ($comment) {
            wp_cache_delete("post_meta_{$comment->comment_post_ID}", 'slavutska_meta');
        }
    }

    /**
     * Встановлення заголовків кешування
     */
    private function set_cache_headers() 
    {
        if (is_admin() || is_user_logged_in()) {
            return;
        }
        
        $expires = 24 * HOUR_IN_SECONDS; // 24 години
        
        header('Cache-Control: public, max-age=' . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
        header('Vary: Accept-Encoding');
        
        // ETag для кращого кешування
        $etag = md5(serialize([
            get_the_modified_time('U'),
            get_option('active_plugins'),
            get_theme_mod('custom_css', '')
        ]));
        
        header('ETag: "' . $etag . '"');
        
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
            $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
    }

    /**
     * Оптимізація автозавантаження
     */
    private function optimize_autoload() 
    {
        // Видалення застарілих опцій з автозавантаження
        $autoload_options = wp_load_alloptions();
        
        foreach ($autoload_options as $option => $value) {
            if (strpos($option, '_transient_') === 0 || 
                strpos($option, '_site_transient_') === 0) {
                wp_cache_delete($option, 'options');
            }
        }
    }

    /**
     * Оптимізація запитів до БД
     */
    private function optimize_database_queries() 
    {
        // Відключення ревізій для певних типів постів
        add_filter('wp_revisions_to_keep', function($num, $post) {
            if (in_array($post->post_type, ['investment', 'land_plot'])) {
                return 3; // Зберігати тільки 3 ревізії
            }
            return $num;
        }, 10, 2);
        
        // Оптимізація мета-запитів
        add_filter('get_post_metadata', [$this, 'optimize_meta_queries'], 10, 4);
    }

    /**
     * Оптимізація мета-запитів
     */
    public function optimize_meta_queries($value, $object_id, $meta_key, $single) 
    {
        $cache_key = "post_meta_{$object_id}_{$meta_key}";
        $cached_value = wp_cache_get($cache_key, 'slavutska_meta');
        
        if (false !== $cached_value) {
            return $single ? $cached_value[0] : $cached_value;
        }
        
        return $value;
    }

    /**
     * Мінімізація CSS файлів
     */
    public function minify_css_files($src, $handle) 
    {
        if (strpos($src, SLAVUTSKA_THEME_URI) !== false) {
            $minified_src = str_replace('.css', '.min.css', $src);
            
            if ($this->file_exists_remote($minified_src)) {
                return $minified_src;
            }
        }
        
        return $src;
    }

    /**
     * Мінімізація JS файлів
     */
    public function minify_js_files($src, $handle) 
    {
        if (strpos($src, SLAVUTSKA_THEME_URI) !== false) {
            $minified_src = str_replace('.js', '.min.js', $src);
            
            if ($this->file_exists_remote($minified_src)) {
                return $minified_src;
            }
        }
        
        return $src;
    }

    /**
     * Перевірка існування файлу
     */
    private function file_exists_remote($url) 
    {
        $file_path = str_replace(SLAVUTSKA_THEME_URI, SLAVUTSKA_THEME_PATH, $url);
        return file_exists($file_path);
    }

    /**
     * Встановлення якості JPEG
     */
    public function set_jpeg_quality($quality) 
    {
        return 85; // Оптимальний баланс якості та розміру
    }

    /**
     * Встановлення якості зображень
     */
    public function set_image_quality($quality) 
    {
        return 85;
    }

    /**
     * Відключення непотрібних функцій
     */
    private function disable_unnecessary_features() 
    {
        // Відключення emoji
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        
        // Відключення oEmbed
        wp_deregister_script('wp-embed');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        
        // Відключення REST API для неавторизованих користувачів
        add_filter('rest_authentication_errors', function($result) {
            if (!is_user_logged_in()) {
                return new WP_Error('rest_disabled', 'REST API disabled', ['status' => 401]);
            }
            return $result;
        });
        
        // Видалення RSD та wlwmanifest
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        
        // Видалення shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');
        
        // Відключення WordPress Heartbeat на frontend
        add_action('init', function() {
            if (!is_admin()) {
                wp_deregister_script('heartbeat');
            }
        });
    }

    /**
     * Планування очищення
     */
    public function schedule_cleanup() 
    {
        if (!wp_next_scheduled('slavutska_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'slavutska_daily_cleanup');
        }
        
        add_action('slavutska_daily_cleanup', [$this, 'cleanup_old_data']);
    }

    /**
     * Очищення старих даних
     */
    public function cleanup_old_data() 
    {
        global $wpdb;
        
        // Очищення старих ревізій (старше 30 днів)
        $wpdb->query("
            DELETE FROM {$wpdb->posts} 
            WHERE post_type = 'revision' 
            AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        
        // Очищення старих transient
        $wpdb->query("
            DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_%' 
            AND option_name NOT LIKE '_transient_timeout_%'
            AND NOT EXISTS (
                SELECT 1 FROM {$wpdb->options} t2 
                WHERE t2.option_name = CONCAT('_transient_timeout_', SUBSTRING({$wpdb->options}.option_name, 12))
                AND t2.option_value > UNIX_TIMESTAMP()
            )
        ");
        
        // Очищення сирітських meta-записів
        $wpdb->query("
            DELETE pm FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE p.ID IS NULL
        ");
        
        // Оптимізація таблиць
        $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
        foreach ($tables as $table) {
            $wpdb->query("OPTIMIZE TABLE {$table[0]}");
        }
        
        // Очищення кешу
        wp_cache_flush();
        
        // Логування очищення
        error_log('Slavutska Performance: Daily cleanup completed at ' . date('Y-m-d H:i:s'));
    }

    /**
     * Додавання скриптів продуктивності у footer
     */
    public function add_performance_scripts() 
    {
        if (is_admin()) {
            return;
        }
        
        echo "\n<!-- Performance Scripts -->\n";
        echo '<script>';
        echo 'if("serviceWorker" in navigator){navigator.serviceWorker.register("/sw.js");}';
        echo 'if("requestIdleCallback" in window){requestIdleCallback(function(){';
        echo 'var links=document.querySelectorAll("a[href]");';
        echo 'for(var i=0;i<links.length;i++){';
        echo 'if(links[i].hostname===window.location.hostname){';
        echo 'links[i].addEventListener("mouseenter",function(){';
        echo 'var link=document.createElement("link");';
        echo 'link.rel="prefetch";link.href=this.href;';
        echo 'document.head.appendChild(link);';
        echo '});}}});}';
        echo '</script>' . "\n";
    }

    /**
     * Отримання метрик продуктивності
     */
    public function get_performance_metrics() 
    {
        return [
            'cache_hit_ratio' => $this->get_cache_hit_ratio(),
            'page_load_time' => $this->get_page_load_time(),
            'database_queries' => get_num_queries(),
            'memory_usage' => $this->format_bytes(memory_get_peak_usage(true)),
            'cache_size' => $this->get_cache_size()
        ];
    }

    /**
     * Отримання коефіцієнта попадань у кеш
     */
    private function get_cache_hit_ratio() 
    {
        $cache_stats = wp_cache_get_stats();
        
        if (isset($cache_stats['cache_hits']) && isset($cache_stats['cache_misses'])) {
            $total = $cache_stats['cache_hits'] + $cache_stats['cache_misses'];
            return $total > 0 ? ($cache_stats['cache_hits'] / $total) * 100 : 0;
        }
        
        return 0;
    }

    /**
     * Отримання часу завантаження сторінки
     */
    private function get_page_load_time() 
    {
        return timer_stop(0, 3);
    }

    /**
     * Отримання розміру кешу
     */
    private function get_cache_size() 
    {
        $cache_dir = WP_CONTENT_DIR . '/cache/slavutska/';
        
        if (is_dir($cache_dir)) {
            return $this->format_bytes($this->get_directory_size($cache_dir));
        }
        
        return '0 B';
    }

    /**
     * Отримання розміру директорії
     */
    private function get_directory_size($directory) 
    {
        $size = 0;
        
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        
        return $size;
    }

    /**
     * Форматування байтів
     */
    private function format_bytes($bytes, $precision = 2) 
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Ініціалізація модуля продуктивності
new SlavutskaPerformance();