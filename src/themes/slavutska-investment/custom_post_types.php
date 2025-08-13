<?php
/**
 * Кастомні типи постів для інвестиційного порталу
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Клас для управління кастомними типами постів
 */
class SlavutskaCustomPostTypes 
{
    public function __construct() 
    {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_taxonomies']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
        
        // Фільтри для адміністрування
        add_filter('manage_investment_posts_columns', [$this, 'investment_columns']);
        add_action('manage_investment_posts_custom_column', [$this, 'investment_column_content'], 10, 2);
        add_filter('manage_land_plot_posts_columns', [$this, 'land_plot_columns']);
        add_action('manage_land_plot_posts_custom_column', [$this, 'land_plot_column_content'], 10, 2);
    }

    /**
     * Реєстрація кастомних типів постів
     */
    public function register_post_types() 
    {
        // Інвестиційні пропозиції
        register_post_type('investment', [
            'labels' => [
                'name'               => __('Інвестиційні пропозиції', 'slavutska-investment'),
                'singular_name'      => __('Інвестиційна пропозиція', 'slavutska-investment'),
                'add_new'            => __('Додати нову', 'slavutska-investment'),
                'add_new_item'       => __('Додати нову пропозицію', 'slavutska-investment'),
                'edit_item'          => __('Редагувати пропозицію', 'slavutska-investment'),
                'new_item'           => __('Нова пропозиція', 'slavutska-investment'),
                'view_item'          => __('Переглянути пропозицію', 'slavutska-investment'),
                'search_items'       => __('Шукати пропозиції', 'slavutska-investment'),
                'not_found'          => __('Пропозицій не знайдено', 'slavutska-investment'),
                'not_found_in_trash' => __('У кошику пропозицій не знайдено', 'slavutska-investment'),
                'menu_name'          => __('Інвестиції', 'slavutska-investment'),
            ],
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => ['slug' => 'investments'],
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-chart-line',
            'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'        => true,
        ]);

        // Земельні ділянки
        register_post_type('land_plot', [
            'labels' => [
                'name'               => __('Земельні ділянки', 'slavutska-investment'),
                'singular_name'      => __('Земельна ділянка', 'slavutska-investment'),
                'add_new'            => __('Додати нову', 'slavutska-investment'),
                'add_new_item'       => __('Додати нову ділянку', 'slavutska-investment'),
                'edit_item'          => __('Редагувати ділянку', 'slavutska-investment'),
                'new_item'           => __('Нова ділянка', 'slavutska-investment'),
                'view_item'          => __('Переглянути ділянку', 'slavutska-investment'),
                'search_items'       => __('Шукати ділянки', 'slavutska-investment'),
                'not_found'          => __('Ділянок не знайдено', 'slavutska-investment'),
                'not_found_in_trash' => __('У кошику ділянок не знайдено', 'slavutska-investment'),
                'menu_name'          => __('Земельні ділянки', 'slavutska-investment'),
            ],
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => ['slug' => 'land-plots'],
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 21,
            'menu_icon'           => 'dashicons-location-alt',
            'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
            'show_in_rest'        => true,
        ]);
    }

    /**
     * Реєстрація таксономій
     */
    public function register_taxonomies() 
    {
        // Категорії інвестицій
        register_taxonomy('investment_category', 'investment', [
            'labels' => [
                'name'              => __('Категорії інвестицій', 'slavutska-investment'),
                'singular_name'     => __('Категорія інвестицій', 'slavutska-investment'),
                'search_items'      => __('Шукати категорії', 'slavutska-investment'),
                'all_items'         => __('Всі категорії', 'slavutska-investment'),
                'edit_item'         => __('Редагувати категорію', 'slavutska-investment'),
                'update_item'       => __('Оновити категорію', 'slavutska-investment'),
                'add_new_item'      => __('Додати нову категорію', 'slavutska-investment'),
                'new_item_name'     => __('Назва нової категорії', 'slavutska-investment'),
                'menu_name'         => __('Категорії', 'slavutska-investment'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'investment-category'],
        ]);

        // Типи земель
        register_taxonomy('land_type', 'land_plot', [
            'labels' => [
                'name'              => __('Типи земель', 'slavutska-investment'),
                'singular_name'     => __('Тип землі', 'slavutska-investment'),
                'search_items'      => __('Шукати типи', 'slavutska-investment'),
                'all_items'         => __('Всі типи', 'slavutska-investment'),
                'edit_item'         => __('Редагувати тип', 'slavutska-investment'),
                'update_item'       => __('Оновити тип', 'slavutska-investment'),
                'add_new_item'      => __('Додати новий тип', 'slavutska-investment'),
                'new_item_name'     => __('Назва нового типу', 'slavutska-investment'),
                'menu_name'         => __('Типи земель', 'slavutska-investment'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'land-type'],
        ]);
    }

    /**
     * Додавання мета-боксів
     */
    public function add_meta_boxes() 
    {
        // Мета-бокс для інвестицій
        add_meta_box(
            'investment_details',
            __('Деталі інвестиційної пропозиції', 'slavutska-investment'),
            [$this, 'investment_meta_box_callback'],
            'investment',
            'normal',
            'high'
        );

        // Мета-бокс для земельних ділянок
        add_meta_box(
            'land_plot_details',
            __('Деталі земельної ділянки', 'slavutska-investment'),
            [$this, 'land_plot_meta_box_callback'],
            'land_plot',
            'normal',
            'high'
        );
    }

    /**
     * Callback для мета-боксу інвестицій
     */
    public function investment_meta_box_callback($post) 
    {
        wp_nonce_field('investment_meta_box', 'investment_meta_box_nonce');

        $investment_amount = get_post_meta($post->ID, '_investment_amount', true);
        $investment_period = get_post_meta($post->ID, '_investment_period', true);
        $expected_return = get_post_meta($post->ID, '_expected_return', true);
        $contact_person = get_post_meta($post->ID, '_contact_person', true);
        $contact_phone = get_post_meta($post->ID, '_contact_phone', true);
        $contact_email = get_post_meta($post->ID, '_contact_email', true);
        $location = get_post_meta($post->ID, '_location', true);
        $is_featured = get_post_meta($post->ID, '_is_featured', true);

        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="investment_amount">' . __('Сума інвестицій (грн)', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="number" id="investment_amount" name="investment_amount" value="' . esc_attr($investment_amount) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="investment_period">' . __('Термін реалізації (місяців)', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="number" id="investment_period" name="investment_period" value="' . esc_attr($investment_period) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="expected_return">' . __('Очікувана прибутковість (%)', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="number" step="0.1" id="expected_return" name="expected_return" value="' . esc_attr($expected_return) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="contact_person">' . __('Контактна особа', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="contact_person" name="contact_person" value="' . esc_attr($contact_person) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="contact_phone">' . __('Телефон', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="contact_phone" name="contact_phone" value="' . esc_attr($contact_phone) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="contact_email">' . __('Email', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="email" id="contact_email" name="contact_email" value="' . esc_attr($contact_email) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="location">' . __('Місцезнаходження', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="location" name="location" value="' . esc_attr($location) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="is_featured">' . __('Рекомендована пропозиція', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="checkbox" id="is_featured" name="is_featured" value="1" ' . checked($is_featured, '1', false) . ' /></td>';
        echo '</tr>';
        echo '</table>';
    }

    /**
     * Callback для мета-боксу земельних ділянок
     */
    public function land_plot_meta_box_callback($post) 
    {
        wp_nonce_field('land_plot_meta_box', 'land_plot_meta_box_nonce');

        $area = get_post_meta($post->ID, '_area', true);
        $price_per_hectare = get_post_meta($post->ID, '_price_per_hectare', true);
        $cadastral_number = get_post_meta($post->ID, '_cadastral_number', true);
        $purpose = get_post_meta($post->ID, '_purpose', true);
        $infrastructure = get_post_meta($post->ID, '_infrastructure', true);
        $latitude = get_post_meta($post->ID, '_latitude', true);
        $longitude = get_post_meta($post->ID, '_longitude', true);
        $documents = get_post_meta($post->ID, '_documents', true);

        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="area">' . __('Площа (га)', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="number" step="0.01" id="area" name="area" value="' . esc_attr($area) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="price_per_hectare">' . __('Ціна за гектар (грн)', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="number" id="price_per_hectare" name="price_per_hectare" value="' . esc_attr($price_per_hectare) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="cadastral_number">' . __('Кадастровий номер', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="cadastral_number" name="cadastral_number" value="' . esc_attr($cadastral_number) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="purpose">' . __('Цільове призначення', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="purpose" name="purpose" value="' . esc_attr($purpose) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="infrastructure">' . __('Інфраструктура', 'slavutska-investment') . '</label></th>';
        echo '<td><textarea id="infrastructure" name="infrastructure" rows="3" style="width: 100%;">' . esc_textarea($infrastructure) . '</textarea></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="latitude">' . __('Широта', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="latitude" name="latitude" value="' . esc_attr($latitude) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="longitude">' . __('Довгота', 'slavutska-investment') . '</label></th>';
        echo '<td><input type="text" id="longitude" name="longitude" value="' . esc_attr($longitude) . '" style="width: 100%;" /></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<th><label for="documents">' . __('Документи', 'slavutska-investment') . '</label></th>';
        echo '<td><textarea id="documents" name="documents" rows="3" style="width: 100%;">' . esc_textarea($documents) . '</textarea></td>';
        echo '</tr>';
        echo '</table>';
    }

    /**
     * Збереження мета-даних
     */
    public function save_meta_boxes($post_id, $post) 
    {
        // Перевірка автозбереження
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Перевірка прав доступу
        if (!current_user_can('edit_post', $post_id)) return;

        // Збереження даних інвестицій
        if ($post->post_type === 'investment') {
            if (!isset($_POST['investment_meta_box_nonce']) || 
                !wp_verify_nonce($_POST['investment_meta_box_nonce'], 'investment_meta_box')) {
                return;
            }

            $fields = [
                'investment_amount', 'investment_period', 'expected_return',
                'contact_person', 'contact_phone', 'contact_email', 'location'
            ];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }

            $is_featured = isset($_POST['is_featured']) ? '1' : '0';
            update_post_meta($post_id, '_is_featured', $is_featured);
        }

        // Збереження даних земельних ділянок
        if ($post->post_type === 'land_plot') {
            if (!isset($_POST['land_plot_meta_box_nonce']) || 
                !wp_verify_nonce($_POST['land_plot_meta_box_nonce'], 'land_plot_meta_box')) {
                return;
            }

            $fields = [
                'area', 'price_per_hectare', 'cadastral_number', 
                'purpose', 'latitude', 'longitude'
            ];

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }

            $text_fields = ['infrastructure', 'documents'];
            foreach ($text_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_textarea_field($_POST[$field]));
                }
            }
        }
    }

    /**
     * Колонки для інвестицій в адміністрації
     */
    public function investment_columns($columns) 
    {
        $new_columns = [];
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['investment_amount'] = __('Сума інвестицій', 'slavutska-investment');
        $new_columns['expected_return'] = __('Прибутковість', 'slavutska-investment');
        $new_columns['is_featured'] = __('Рекомендована', 'slavutska-investment');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Контент колонок для інвестицій
     */
    public function investment_column_content($column, $post_id) 
    {
        switch ($column) {
            case 'investment_amount':
                $amount = get_post_meta($post_id, '_investment_amount', true);
                echo $amount ? number_format($amount, 0, ',', ' ') . ' грн' : '—';
                break;
            case 'expected_return':
                $return = get_post_meta($post_id, '_expected_return', true);
                echo $return ? $return . '%' : '—';
                break;
            case 'is_featured':
                $featured = get_post_meta($post_id, '_is_featured', true);
                echo $featured ? '★' : '—';
                break;
        }
    }

    /**
     * Колонки для земельних ділянок в адміністрації
     */
    public function land_plot_columns($columns) 
    {
        $new_columns = [];
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['area'] = __('Площа', 'slavutska-investment');
        $new_columns['price_per_hectare'] = __('Ціна за га', 'slavutska-investment');
        $new_columns['cadastral_number'] = __('Кадастровий номер', 'slavutska-investment');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Контент колонок для земельних ділянок
     */
    public function land_plot_column_content($column, $post_id) 
    {
        switch ($column) {
            case 'area':
                $area = get_post_meta($post_id, '_area', true);
                echo $area ? $area . ' га' : '—';
                break;
            case 'price_per_hectare':
                $price = get_post_meta($post_id, '_price_per_hectare', true);
                echo $price ? number_format($price, 0, ',', ' ') . ' грн' : '—';
                break;
            case 'cadastral_number':
                $number = get_post_meta($post_id, '_cadastral_number', true);
                echo $number ? esc_html($number) : '—';
                break;
        }
    }
}

// Ініціалізація класу
new SlavutskaCustomPostTypes();