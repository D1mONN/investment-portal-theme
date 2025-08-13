<?php
/**
 * Модуль безпеки для інвестиційного порталу
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Клас для управління безпекою сайту
 */
class SlavutskaSecurity 
{
    public function __construct() 
    {
        add_action('init', [$this, 'init_security']);
        add_action('wp_login', [$this, 'log_successful_login'], 10, 2);
        add_action('wp_login_failed', [$this, 'log_failed_login']);
        add_action('admin_init', [$this, 'admin_security']);
        add_action('wp_head', [$this, 'add_security_headers']);
        
        // Фільтри безпеки
        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('wp_headers', [$this, 'add_security_headers_filter']);
        add_filter('login_errors', [$this, 'generic_login_error']);
        add_filter('authenticate', [$this, 'limit_login_attempts'], 30, 3);
        
        // Приховування інформації про WordPress
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', '__return_empty_string');
        
        // Захист від directory traversal
        add_action('init', [$this, 'prevent_directory_traversal']);
        
        // Захист файлів
        add_action('init', [$this, 'protect_sensitive_files']);
    }

    /**
     * Ініціалізація основних налаштувань безпеки
     */
    public function init_security() 
    {
        // Відключення file editor
        if (!defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }
        
        // Відключення file mods
        if (!defined('DISALLOW_FILE_MODS')) {
            define('DISALLOW_FILE_MODS', true);
        }
        
        // Примусове використання SSL для admin
        if (!defined('FORCE_SSL_ADMIN')) {
            define('FORCE_SSL_ADMIN', true);
        }
        
        // Встановлення строгих прав доступу до файлів
        $this->set_file_permissions();
        
        // Приховування помилок PHP у production
        if (!WP_DEBUG) {
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
        }
    }

    /**
     * Встановлення правильних прав доступу до файлів
     */
    private function set_file_permissions() 
    {
        // Права доступу до wp-config.php
        $wp_config = ABSPATH . 'wp-config.php';
        if (file_exists($wp_config)) {
            chmod($wp_config, 0600);
        }
        
        // Права доступу до .htaccess
        $htaccess = ABSPATH . '.htaccess';
        if (file_exists($htaccess)) {
            chmod($htaccess, 0644);
        }
    }

    /**
     * Додавання заголовків безпеки
     */
    public function add_security_headers() 
    {
        // Тільки для frontend
        if (is_admin()) {
            return;
        }
        
        echo "\n<!-- Security Headers -->\n";
        echo '<meta http-equiv="X-Content-Type-Options" content="nosniff">' . "\n";
        echo '<meta http-equiv="X-Frame-Options" content="SAMEORIGIN">' . "\n";
        echo '<meta http-equiv="X-XSS-Protection" content="1; mode=block">' . "\n";
        echo '<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">' . "\n";
        echo '<meta http-equiv="Permissions-Policy" content="geolocation=(), microphone=(), camera=()">' . "\n";
    }

    /**
     * Додавання заголовків безпеки через фільтр
     */
    public function add_security_headers_filter($headers) 
    {
        if (!is_admin()) {
            $headers['X-Content-Type-Options'] = 'nosniff';
            $headers['X-Frame-Options'] = 'SAMEORIGIN';
            $headers['X-XSS-Protection'] = '1; mode=block';
            $headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
            $headers['Content-Security-Policy'] = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https:;";
        }
        
        return $headers;
    }

    /**
     * Безпека адміністративної частини
     */
    public function admin_security() 
    {
        // Приховування версії WordPress у admin
        add_filter('update_footer', [$this, 'remove_wordpress_version'], 11);
        
        // Обмеження доступу до admin для не авторизованих користувачів
        if (!is_user_logged_in() && is_admin() && !wp_doing_ajax()) {
            wp_redirect(home_url());
            exit;
        }
        
        // Логування адміністративних дій
        $this->log_admin_actions();
    }

    /**
     * Видалення версії WordPress з footer
     */
    public function remove_wordpress_version() 
    {
        return '';
    }

    /**
     * Логування успішних входів
     */
    public function log_successful_login($user_login, $user) 
    {
        $log_data = [
            'action' => 'successful_login',
            'user_id' => $user->ID,
            'username' => $user_login,
            'ip_address' => $this->get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        ];
        
        $this->write_security_log($log_data);
    }

    /**
     * Логування невдалих спроб входу
     */
    public function log_failed_login($username) 
    {
        $log_data = [
            'action' => 'failed_login',
            'username' => sanitize_user($username),
            'ip_address' => $this->get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        ];
        
        $this->write_security_log($log_data);
        
        // Збільшення лічильника невдалих спроб
        $this->increment_login_attempts();
    }

    /**
     * Обмеження кількості спроб входу
     */
    public function limit_login_attempts($user, $username, $password) 
    {
        if (empty($username) || empty($password)) {
            return $user;
        }
        
        $ip = $this->get_user_ip();
        $attempts_key = 'login_attempts_' . md5($ip);
        $lockout_key = 'login_lockout_' . md5($ip);
        
        // Перевірка, чи IP заблокований
        if (get_transient($lockout_key)) {
            return new WP_Error('too_many_attempts', 
                __('Забагато спроб входу. Спробуйте пізніше.', 'slavutska-investment')
            );
        }
        
        return $user;
    }

    /**
     * Збільшення лічильника спроб входу
     */
    private function increment_login_attempts() 
    {
        $ip = $this->get_user_ip();
        $attempts_key = 'login_attempts_' . md5($ip);
        $lockout_key = 'login_lockout_' . md5($ip);
        
        $attempts = get_transient($attempts_key) ?: 0;
        $attempts++;
        
        // Встановлення лічильника на 1 годину
        set_transient($attempts_key, $attempts, HOUR_IN_SECONDS);
        
        // Блокування після 5 невдалих спроб
        if ($attempts >= 5) {
            set_transient($lockout_key, true, HOUR_IN_SECONDS);
            
            // Логування блокування
            $log_data = [
                'action' => 'ip_lockout',
                'ip_address' => $ip,
                'attempts' => $attempts,
                'timestamp' => current_time('mysql')
            ];
            
            $this->write_security_log($log_data);
        }
    }

    /**
     * Узагальнення повідомлень про помилки входу
     */
    public function generic_login_error($error) 
    {
        return __('Неправильний логін або пароль.', 'slavutska-investment');
    }

    /**
     * Отримання IP адреси користувача
     */
    private function get_user_ip() 
    {
        // Перевірка різних заголовків для отримання реального IP
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key])[0];
                $ip = trim($ip);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, 
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Запис у лог безпеки
     */
    private function write_security_log($data) 
    {
        $log_file = WP_CONTENT_DIR . '/security.log';
        $log_entry = date('Y-m-d H:i:s') . ' - ' . json_encode($data) . PHP_EOL;
        
        // Обмеження розміру лог файлу (5MB)
        if (file_exists($log_file) && filesize($log_file) > 5 * 1024 * 1024) {
            $this->rotate_log_file($log_file);
        }
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Ротація лог файлу
     */
    private function rotate_log_file($log_file) 
    {
        $backup_file = $log_file . '.old';
        
        if (file_exists($backup_file)) {
            unlink($backup_file);
        }
        
        rename($log_file, $backup_file);
    }

    /**
     * Логування адміністративних дій
     */
    private function log_admin_actions() 
    {
        // Логування видалення постів
        add_action('delete_post', function($post_id) {
            if (!wp_is_post_revision($post_id)) {
                $log_data = [
                    'action' => 'post_deleted',
                    'post_id' => $post_id,
                    'user_id' => get_current_user_id(),
                    'ip_address' => $this->get_user_ip(),
                    'timestamp' => current_time('mysql')
                ];
                
                $this->write_security_log($log_data);
            }
        });
        
        // Логування встановлення плагінів
        add_action('activated_plugin', function($plugin) {
            $log_data = [
                'action' => 'plugin_activated',
                'plugin' => $plugin,
                'user_id' => get_current_user_id(),
                'ip_address' => $this->get_user_ip(),
                'timestamp' => current_time('mysql')
            ];
            
            $this->write_security_log($log_data);
        });
    }

    /**
     * Захист від directory traversal атак
     */
    public function prevent_directory_traversal() 
    {
        $suspicious_patterns = [
            '../', '..\\', './',
            '%2e%2e%2f', '%2e%2e\\',
            '0x2e', '0x2f', '0x5c'
        ];
        
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        
        foreach ($suspicious_patterns as $pattern) {
            if (stripos($request_uri, $pattern) !== false || 
                stripos($query_string, $pattern) !== false) {
                
                $this->block_request('Directory traversal attempt');
            }
        }
    }

    /**
     * Захист чутливих файлів
     */
    public function protect_sensitive_files() 
    {
        $protected_files = [
            'wp-config.php', 'wp-config-sample.php',
            '.htaccess', '.htpasswd',
            'error_log', 'debug.log',
            'readme.html', 'readme.txt',
            'wp-admin/install.php',
            'wp-admin/upgrade.php'
        ];
        
        $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $request_file = basename($request_uri);
        
        if (in_array($request_file, $protected_files)) {
            $this->block_request('Access to sensitive file: ' . $request_file);
        }
    }

    /**
     * Блокування підозрілих запитів
     */
    private function block_request($reason) 
    {
        $log_data = [
            'action' => 'request_blocked',
            'reason' => $reason,
            'ip_address' => $this->get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'timestamp' => current_time('mysql')
        ];
        
        $this->write_security_log($log_data);
        
        // Відправка 403 заборонено
        status_header(403);
        die('Access Forbidden');
    }

    /**
     * Очищення та валідація введених даних
     */
    public static function sanitize_input($input, $type = 'text') 
    {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitize_input($item, $type);
            }, $input);
        }
        
        switch ($type) {
            case 'email':
                return sanitize_email($input);
            case 'url':
                return esc_url_raw($input);
            case 'int':
                return intval($input);
            case 'float':
                return floatval($input);
            case 'html':
                return wp_kses_post($input);
            case 'textarea':
                return sanitize_textarea_field($input);
            default:
                return sanitize_text_field($input);
        }
    }

    /**
     * Генерація безпечного nonce
     */
    public static function create_nonce($action = 'slavutska_action') 
    {
        return wp_create_nonce($action);
    }

    /**
     * Перевірка nonce
     */
    public static function verify_nonce($nonce, $action = 'slavutska_action') 
    {
        return wp_verify_nonce($nonce, $action);
    }

    /**
     * Перевірка CSRF токена для AJAX запитів
     */
    public static function verify_ajax_request() 
    {
        if (!wp_doing_ajax()) {
            return false;
        }
        
        $nonce = $_POST['nonce'] ?? $_GET['nonce'] ?? '';
        
        if (!self::verify_nonce($nonce, 'slavutska_ajax')) {
            wp_send_json_error([
                'message' => __('Помилка безпеки. Оновіть сторінку та спробуйте знову.', 'slavutska-investment')
            ]);
        }
        
        return true;
    }

    /**
     * Шифрування чутливих даних
     */
    public static function encrypt_data($data) 
    {
        if (!function_exists('openssl_encrypt')) {
            return base64_encode($data);
        }
        
        $key = wp_salt('SECURE_AUTH_KEY');
        $iv = openssl_random_pseudo_bytes(16);
        
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    /**
     * Розшифрування даних
     */
    public static function decrypt_data($encrypted_data) 
    {
        if (!function_exists('openssl_decrypt')) {
            return base64_decode($encrypted_data);
        }
        
        $data = base64_decode($encrypted_data);
        $key = wp_salt('SECURE_AUTH_KEY');
        
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}

// Ініціалізація модуля безпеки
new SlavutskaSecurity();