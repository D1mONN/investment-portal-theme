<?php
/**
 * Контактна форма та її обробка
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Клас для обробки контактної форми
 */
class SlavutskaContactForm 
{
    public function __construct() 
    {
        add_action('wp_ajax_slavutska_contact_form', [$this, 'handle_contact_form']);
        add_action('wp_ajax_nopriv_slavutska_contact_form', [$this, 'handle_contact_form']);
        add_action('init', [$this, 'register_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_form_scripts']);
    }

    /**
     * Реєстрація шорткоду
     */
    public function register_shortcode() 
    {
        add_shortcode('slavutska_contact_form', [$this, 'render_contact_form']);
    }

    /**
     * Підключення скриптів форми
     */
    public function enqueue_form_scripts() 
    {
        if (!is_admin()) {
            wp_enqueue_script(
                'slavutska-contact-form',
                SLAVUTSKA_THEME_URI . '/assets/js/contact-form.js',
                ['jquery'],
                SLAVUTSKA_THEME_VERSION,
                true
            );
            
            wp_localize_script('slavutska-contact-form', 'contactFormAjax', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('slavutska_contact_form_nonce'),
                'strings' => [
                    'sending' => __('Відправляємо...', 'slavutska-investment'),
                    'success' => __('Повідомлення відправлено успішно!', 'slavutska-investment'),
                    'error' => __('Сталася помилка. Спробуйте пізніше.', 'slavutska-investment'),
                    'validation_error' => __('Будь ласка, заповніть всі обов\'язкові поля.', 'slavutska-investment')
                ]
            ]);
        }
    }

    /**
     * Рендеринг контактної форми
     */
    public function render_contact_form($atts = []) 
    {
        $atts = shortcode_atts([
            'title' => __('Зв\'яжіться з нами', 'slavutska-investment'),
            'subtitle' => __('Ми готові відповісти на ваші запитання', 'slavutska-investment'),
            'show_title' => 'true',
            'form_id' => 'contact-form'
        ], $atts);

        ob_start();
        ?>
        <div class="contact-form-container">
            <?php if ($atts['show_title'] === 'true'): ?>
                <div class="contact-form-header">
                    <h3 class="contact-form-title"><?php echo esc_html($atts['title']); ?></h3>
                    <?php if ($atts['subtitle']): ?>
                        <p class="contact-form-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form id="<?php echo esc_attr($atts['form_id']); ?>" class="slavutska-contact-form" novalidate>
                <?php wp_nonce_field('slavutska_contact_form_nonce', 'contact_form_nonce'); ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact-name" class="form-label">
                            <?php _e('Ім\'я', 'slavutska-investment'); ?>
                            <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="contact-name" 
                            name="name" 
                            class="form-control" 
                            required 
                            autocomplete="name"
                            placeholder="<?php esc_attr_e('Введіть ваше ім\'я', 'slavutska-investment'); ?>"
                        >
                        <div class="form-error" id="name-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="contact-phone" class="form-label">
                            <?php _e('Телефон', 'slavutska-investment'); ?>
                        </label>
                        <input 
                            type="tel" 
                            id="contact-phone" 
                            name="phone" 
                            class="form-control" 
                            autocomplete="tel"
                            placeholder="<?php esc_attr_e('+380 XX XXX XX XX', 'slavutska-investment'); ?>"
                        >
                        <div class="form-error" id="phone-error"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact-email" class="form-label">
                        <?php _e('Email', 'slavutska-investment'); ?>
                        <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="contact-email" 
                        name="email" 
                        class="form-control" 
                        required 
                        autocomplete="email"
                        placeholder="<?php esc_attr_e('your@email.com', 'slavutska-investment'); ?>"
                    >
                    <div class="form-error" id="email-error"></div>
                </div>

                <div class="form-group">
                    <label for="contact-subject" class="form-label">
                        <?php _e('Тема', 'slavutska-investment'); ?>
                        <span class="required">*</span>
                    </label>
                    <select id="contact-subject" name="subject" class="form-control" required>
                        <option value=""><?php _e('Оберіть тему', 'slavutska-investment'); ?></option>
                        <option value="investment"><?php _e('Інвестиційні можливості', 'slavutska-investment'); ?></option>
                        <option value="land_plot"><?php _e('Земельні ділянки', 'slavutska-investment'); ?></option>
                        <option value="partnership"><?php _e('Партнерство', 'slavutska-investment'); ?></option>
                        <option value="consultation"><?php _e('Консультація', 'slavutska-investment'); ?></option>
                        <option value="other"><?php _e('Інше', 'slavutska-investment'); ?></option>
                    </select>
                    <div class="form-error" id="subject-error"></div>
                </div>

                <div class="form-group">
                    <label for="contact-message" class="form-label">
                        <?php _e('Повідомлення', 'slavutska-investment'); ?>
                        <span class="required">*</span>
                    </label>
                    <textarea 
                        id="contact-message" 
                        name="message" 
                        class="form-control" 
                        rows="6" 
                        required
                        placeholder="<?php esc_attr_e('Опишіть ваш запит детальніше...', 'slavutska-investment'); ?>"
                    ></textarea>
                    <div class="form-error" id="message-error"></div>
                </div>

                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="privacy_consent" id="privacy-consent" required>
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">
                            <?php printf(
                                __('Я погоджуюся з %s та даю згоду на обробку персональних даних', 'slavutska-investment'),
                                '<a href="' . esc_url(home_url('/privacy-policy/')) . '" target="_blank">' . __('політикою конфіденційності', 'slavutska-investment') . '</a>'
                            ); ?>
                            <span class="required">*</span>
                        </span>
                    </label>
                    <div class="form-error" id="privacy-error"></div>
                </div>

                <!-- Honeypot для захисту від спаму -->
                <input type="text" name="website" style="display:none !important" tabindex="-1" autocomplete="off">

                <div class="form-actions">
                    <button type="submit" class="btn btn--primary btn--large form-submit">
                        <span class="btn-text"><?php _e('Відправити повідомлення', 'slavutska-investment'); ?></span>
                        <span class="btn-loader" style="display: none;">
                            <i class="icon-spinner" aria-hidden="true"></i>
                            <?php _e('Відправляємо...', 'slavutska-investment'); ?>
                        </span>
                    </button>
                </div>

                <div class="form-messages">
                    <div class="form-success" id="form-success" style="display: none;">
                        <i class="icon-check-circle" aria-hidden="true"></i>
                        <span></span>
                    </div>
                    <div class="form-error-general" id="form-error" style="display: none;">
                        <i class="icon-alert-circle" aria-hidden="true"></i>
                        <span></span>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Обробка форми через AJAX
     */
    public function handle_contact_form() 
    {
        // Перевірка nonce
        if (!wp_verify_nonce($_POST['contact_form_nonce'], 'slavutska_contact_form_nonce')) {
            wp_send_json_error([
                'message' => __('Помилка безпеки. Оновіть сторінку та спробуйте знову.', 'slavutska-investment')
            ]);
        }

        // Перевірка honeypot
        if (!empty($_POST['website'])) {
            wp_send_json_error([
                'message' => __('Спам детектовано.', 'slavutska-investment')
            ]);
        }

        // Отримання та валідація даних
        $name = SlavutskaSecurity::sanitize_input($_POST['name']);
        $email = SlavutskaSecurity::sanitize_input($_POST['email'], 'email');
        $phone = SlavutskaSecurity::sanitize_input($_POST['phone']);
        $subject = SlavutskaSecurity::sanitize_input($_POST['subject']);
        $message = SlavutskaSecurity::sanitize_input($_POST['message'], 'textarea');
        $privacy_consent = isset($_POST['privacy_consent']) ? 1 : 0;

        // Валідація
        $errors = $this->validate_form_data([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'privacy_consent' => $privacy_consent
        ]);

        if (!empty($errors)) {
            wp_send_json_error([
                'message' => __('Будь ласка, виправте помилки у формі.', 'slavutska-investment'),
                'errors' => $errors
            ]);
        }

        // Збереження у базу даних
        $contact_id = $this->save_contact_submission([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $this->get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => current_time('mysql')
        ]);

        // Відправка email
        $email_sent = $this->send_notification_email([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'contact_id' => $contact_id
        ]);

        if ($email_sent) {
            // Відправка автовідповіді
            $this->send_auto_reply($email, $name);

            wp_send_json_success([
                'message' => __('Дякуємо за ваше повідомлення! Ми зв\'яжемося з вами найближчим часом.', 'slavutska-investment')
            ]);
        } else {
            wp_send_json_error([
                'message' => __('Повідомлення збережено, але виникла проблема з відправкою email. Ми зв\'яжемося з вами найближчим часом.', 'slavutska-investment')
            ]);
        }
    }

    /**
     * Валідація даних форми
     */
    private function validate_form_data($data) 
    {
        $errors = [];

        // Валідація імені
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors['name'] = __('Ім\'я повинно містити принаймні 2 символи.', 'slavutska-investment');
        }

        // Валідація email
        if (empty($data['email']) || !is_email($data['email'])) {
            $errors['email'] = __('Введіть правильну email адресу.', 'slavutska-investment');
        }

        // Валідація телефону (якщо заповнений)
        if (!empty($data['phone']) && !preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $data['phone'])) {
            $errors['phone'] = __('Введіть правильний номер телефону.', 'slavutska-investment');
        }

        // Валідація теми
        if (empty($data['subject'])) {
            $errors['subject'] = __('Оберіть тему повідомлення.', 'slavutska-investment');
        }

        // Валідація повідомлення
        if (empty($data['message']) || strlen($data['message']) < 10) {
            $errors['message'] = __('Повідомлення повинно містити принаймні 10 символів.', 'slavutska-investment');
        }

        // Валідація згоди на обробку даних
        if (!$data['privacy_consent']) {
            $errors['privacy_consent'] = __('Необхідно надати згоду на обробку персональних даних.', 'slavutska-investment');
        }

        return $errors;
    }

    /**
     * Збереження повідомлення у базу даних
     */
    private function save_contact_submission($data) 
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'slavutska_contacts';

        // Створення таблиці, якщо не існує
        $this->create_contacts_table();

        $result = $wpdb->insert(
            $table_name,
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'ip_address' => $data['ip_address'],
                'user_agent' => $data['user_agent'],
                'status' => 'new',
                'created_at' => $data['created_at']
            ],
            [
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
            ]
        );

        return $result !== false ? $wpdb->insert_id : false;
    }

    /**
     * Створення таблиці для контактів
     */
    private function create_contacts_table() 
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'slavutska_contacts';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            subject varchar(100) NOT NULL,
            message text NOT NULL,
            ip_address varchar(45),
            user_agent text,
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Відправка сповіщення адміністратору
     */
    private function send_notification_email($data) 
    {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');

        $subject_mapping = [
            'investment' => __('Інвестиційні можливості', 'slavutska-investment'),
            'land_plot' => __('Земельні ділянки', 'slavutska-investment'),
            'partnership' => __('Партнерство', 'slavutska-investment'),
            'consultation' => __('Консультація', 'slavutska-investment'),
            'other' => __('Інше', 'slavutska-investment')
        ];

        $subject_text = isset($subject_mapping[$data['subject']]) 
            ? $subject_mapping[$data['subject']] 
            : $data['subject'];

        $email_subject = sprintf(
            __('[%s] Нове повідомлення: %s', 'slavutska-investment'),
            $site_name,
            $subject_text
        );

        $email_body = $this->get_notification_email_template($data, $subject_text);

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <noreply@' . wp_parse_url(home_url(), PHP_URL_HOST) . '>',
            'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>'
        ];

        return wp_mail($admin_email, $email_subject, $email_body, $headers);
    }

    /**
     * Шаблон email для адміністратора
     */
    private function get_notification_email_template($data, $subject_text) 
    {
        $template = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .email-header { background-color: #1e40af; color: white; padding: 20px; text-align: center; }
                .email-content { background-color: #f8f9fa; padding: 20px; }
                .contact-info { background-color: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
                .label { font-weight: bold; color: #1e40af; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h2>' . __('Нове повідомлення з сайту', 'slavutska-investment') . '</h2>
                    <p>' . get_bloginfo('name') . '</p>
                </div>
                
                <div class="email-content">
                    <div class="contact-info">
                        <p><span class="label">' . __('Ім\'я:', 'slavutska-investment') . '</span> ' . esc_html($data['name']) . '</p>
                        <p><span class="label">' . __('Email:', 'slavutska-investment') . '</span> ' . esc_html($data['email']) . '</p>';
        
        if ($data['phone']) {
            $template .= '<p><span class="label">' . __('Телефон:', 'slavutska-investment') . '</span> ' . esc_html($data['phone']) . '</p>';
        }
        
        $template .= '
                        <p><span class="label">' . __('Тема:', 'slavutska-investment') . '</span> ' . esc_html($subject_text) . '</p>
                        <p><span class="label">' . __('Повідомлення:', 'slavutska-investment') . '</span></p>
                        <div style="background-color: #e9ecef; padding: 15px; border-radius: 5px;">
                            ' . nl2br(esc_html($data['message'])) . '
                        </div>
                    </div>
                    
                    <div class="contact-info">
                        <p><span class="label">' . __('ID повідомлення:', 'slavutska-investment') . '</span> #' . $data['contact_id'] . '</p>
                        <p><span class="label">' . __('Дата отримання:', 'slavutska-investment') . '</span> ' . current_time('d.m.Y H:i') . '</p>
                        <p><span class="label">' . __('IP адреса:', 'slavutska-investment') . '</span> ' . $this->get_user_ip() . '</p>
                    </div>
                </div>
                
                <div class="footer">
                    <p>' . sprintf(__('Це повідомлення було відправлено з сайту %s', 'slavutska-investment'), '<a href="' . home_url() . '">' . get_bloginfo('name') . '</a>') . '</p>
                </div>
            </div>
        </body>
        </html>';

        return $template;
    }

    /**
     * Відправка автовідповіді користувачу
     */
    private function send_auto_reply($user_email, $user_name) 
    {
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(
            __('Дякуємо за звернення - %s', 'slavutska-investment'),
            $site_name
        );

        $message = $this->get_auto_reply_template($user_name);

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <noreply@' . wp_parse_url(home_url(), PHP_URL_HOST) . '>'
        ];

        return wp_mail($user_email, $subject, $message, $headers);
    }

    /**
     * Шаблон автовідповіді
     */
    private function get_auto_reply_template($user_name) 
    {
        $site_name = get_bloginfo('name');
        $contact_phone = slavutska_get_option('contact_phone', '+380 123 456 789');
        $contact_email = slavutska_get_option('contact_email', 'info@slavutska.gov.ua');

        return '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .email-header { background-color: #1e40af; color: white; padding: 20px; text-align: center; }
                .email-content { background-color: #f8f9fa; padding: 20px; }
                .contact-info { background-color: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h2>' . sprintf(__('Дякуємо, %s!', 'slavutska-investment'), esc_html($user_name)) . '</h2>
                    <p>' . $site_name . '</p>
                </div>
                
                <div class="email-content">
                    <p>' . __('Ваше повідомлення успішно отримано. Ми розглянемо його та зв\'яжемося з вами найближчим часом.', 'slavutska-investment') . '</p>
                    
                    <div class="contact-info">
                        <h3>' . __('Наші контакти:', 'slavutska-investment') . '</h3>
                        <p><strong>' . __('Телефон:', 'slavutska-investment') . '</strong> ' . esc_html($contact_phone) . '</p>
                        <p><strong>' . __('Email:', 'slavutska-investment') . '</strong> ' . esc_html($contact_email) . '</p>
                        <p><strong>' . __('Сайт:', 'slavutska-investment') . '</strong> <a href="' . home_url() . '">' . home_url() . '</a></p>
                    </div>
                    
                    <p>' . __('З повагою,<br>Команда Славутської громади', 'slavutska-investment') . '</p>
                </div>
                
                <div class="footer">
                    <p>' . __('Це автоматичне повідомлення. Будь ласка, не відповідайте на нього.', 'slavutska-investment') . '</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Отримання IP адреси користувача
     */
    private function get_user_ip() 
    {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
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
}

// Ініціалізація обробника контактної форми
new SlavutskaContactForm();