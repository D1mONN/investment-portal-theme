<?php
/**
 * Реєстрація полів ACF через PHP
 * 
 * @package SlavutaInvest
 */

// FILE: inc/acf-fields.php

// Захист від прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Реєстрація груп полів ACF
 */
function slavuta_register_acf_fields() {
    
    // Перевіряємо чи існує функція ACF
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    /**
     * Група полів для Інвестиційних проєктів
     */
    acf_add_local_field_group(array(
        'key' => 'group_invest_project',
        'title' => 'Деталі інвестиційного проєкту',
        'fields' => array(
            // Короткий опис
            array(
                'key' => 'field_short_description',
                'label' => 'Короткий опис',
                'name' => 'short_description',
                'type' => 'textarea',
                'instructions' => 'Введіть короткий опис проєкту (до 300 символів)',
                'required' => 1,
                'default_value' => '',
                'placeholder' => 'Опис проєкту...',
                'maxlength' => 300,
                'rows' => 3,
                'new_lines' => 'br',
            ),
            // Головне зображення
            array(
                'key' => 'field_main_image',
                'label' => 'Головне зображення',
                'name' => 'main_image',
                'type' => 'image',
                'instructions' => 'Оберіть головне зображення проєкту (рекомендований розмір: 1200x800px)',
                'required' => 1,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => 800,
                'min_height' => 600,
                'max_width' => 2400,
                'max_height' => 1600,
                'max_size' => '5MB',
                'mime_types' => 'jpg,jpeg,png,webp',
            ),
            // Галерея зображень
            array(
                'key' => 'field_image_gallery',
                'label' => 'Галерея зображень',
                'name' => 'image_gallery',
                'type' => 'gallery',
                'instructions' => 'Додайте додаткові зображення проєкту',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'insert' => 'append',
                'library' => 'all',
                'min' => 0,
                'max' => 20,
                'min_width' => 800,
                'min_height' => 600,
                'max_size' => '5MB',
                'mime_types' => 'jpg,jpeg,png,webp',
            ),
            // Сума інвестицій
            array(
                'key' => 'field_investment_amount',
                'label' => 'Сума інвестицій (грн)',
                'name' => 'investment_amount',
                'type' => 'number',
                'instructions' => 'Введіть необхідну суму інвестицій в гривнях',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '1000000',
                'prepend' => '₴',
                'append' => 'грн',
                'min' => 0,
                'max' => '',
                'step' => 1000,
            ),
            // Статус проєкту
            array(
                'key' => 'field_project_status',
                'label' => 'Статус проєкту',
                'name' => 'project_status',
                'type' => 'select',
                'instructions' => 'Оберіть поточний статус проєкту',
                'required' => 1,
                'choices' => array(
                    'idea' => 'Ідея',
                    'development' => 'В розробці',
                    'implemented' => 'Реалізовано',
                ),
                'default_value' => 'idea',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'ajax' => 0,
                'return_format' => 'label',
            ),
            // PDF презентація
            array(
                'key' => 'field_pdf_presentation',
                'label' => 'PDF презентація',
                'name' => 'pdf_presentation',
                'type' => 'file',
                'instructions' => 'Завантажте презентацію проєкту в форматі PDF',
                'required' => 0,
                'return_format' => 'array',
                'library' => 'all',
                'min_size' => 0,
                'max_size' => '20MB',
                'mime_types' => 'pdf',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'invest_project',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Поля для інвестиційних проєктів',
        'show_in_rest' => 0,
    ));
    
    /**
     * Група полів для Земельних ділянок
     */
    acf_add_local_field_group(array(
        'key' => 'group_land_plot',
        'title' => 'Деталі земельної ділянки',
        'fields' => array(
            // Площа в гектарах
            array(
                'key' => 'field_area_hectares',
                'label' => 'Площа (га)',
                'name' => 'area_hectares',
                'type' => 'number',
                'instructions' => 'Введіть площу ділянки в гектарах',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '1.5',
                'prepend' => '',
                'append' => 'га',
                'min' => 0,
                'max' => 10000,
                'step' => 0.01,
            ),
            // Кадастровий номер
            array(
                'key' => 'field_cadastral_number',
                'label' => 'Кадастровий номер',
                'name' => 'cadastral_number',
                'type' => 'text',
                'instructions' => 'Введіть кадастровий номер ділянки',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '6810700000:01:001:0001',
                'prepend' => '',
                'append' => '',
                'maxlength' => 30,
            ),
            // Цільове призначення
            array(
                'key' => 'field_land_purpose',
                'label' => 'Цільове призначення',
                'name' => 'land_purpose',
                'type' => 'textarea',
                'instructions' => 'Опишіть цільове призначення земельної ділянки',
                'required' => 1,
                'default_value' => '',
                'placeholder' => 'Для будівництва та обслуговування...',
                'maxlength' => 500,
                'rows' => 4,
                'new_lines' => 'br',
            ),
            // Комунікації (Repeater)
            array(
                'key' => 'field_communications',
                'label' => 'Комунікації',
                'name' => 'communications',
                'type' => 'repeater',
                'instructions' => 'Додайте інформацію про наявні комунікації',
                'required' => 0,
                'collapsed' => 'field_comm_name',
                'min' => 0,
                'max' => 10,
                'layout' => 'table',
                'button_label' => 'Додати комунікацію',
                'sub_fields' => array(
                    // Назва комунікації
                    array(
                        'key' => 'field_comm_name',
                        'label' => 'Назва',
                        'name' => 'name',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'default_value' => '',
                        'placeholder' => 'Електроенергія',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 100,
                    ),
                    // Статус комунікації
                    array(
                        'key' => 'field_comm_status',
                        'label' => 'Статус',
                        'name' => 'status',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'default_value' => '',
                        'placeholder' => 'Підведено до межі ділянки',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 200,
                    ),
                ),
            ),
            // Схема ділянки
            array(
                'key' => 'field_main_scheme_image',
                'label' => 'Схема ділянки',
                'name' => 'main_scheme_image',
                'type' => 'image',
                'instructions' => 'Завантажте схему або план земельної ділянки',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => 600,
                'min_height' => 400,
                'max_width' => 3000,
                'max_height' => 3000,
                'max_size' => '10MB',
                'mime_types' => 'jpg,jpeg,png,webp,pdf',
            ),
            // Карта розташування
            array(
                'key' => 'field_location_map',
                'label' => 'Розташування на карті',
                'name' => 'location_map',
                'type' => 'google_map',
                'instructions' => 'Вкажіть розташування ділянки на карті',
                'required' => 1,
                'center_lat' => '50.2228',
                'center_lng' => '26.6731',
                'zoom' => 12,
                'height' => 400,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'land_plot',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Поля для земельних ділянок',
        'show_in_rest' => 0,
    ));
    
    /**
     * Створення сторінки опцій теми
     */
    if (function_exists('acf_add_options_page')) {
        
        // Головна сторінка опцій
        $option_page = acf_add_options_page(array(
            'page_title'    => 'Налаштування теми',
            'menu_title'    => 'Опції теми',
            'menu_slug'     => 'theme-options',
            'capability'    => 'edit_posts',
            'redirect'      => true,
            'icon_url'      => 'dashicons-admin-customizer',
            'position'      => 30,
        ));
        
        // Підсторінка контактів
        acf_add_options_sub_page(array(
            'page_title'    => 'Контактна інформація',
            'menu_title'    => 'Контакти',
            'parent_slug'   => 'theme-options',
            'capability'    => 'edit_posts',
        ));
        
        // Підсторінка Hero блоку
        acf_add_options_sub_page(array(
            'page_title'    => 'Налаштування Hero блоку',
            'menu_title'    => 'Hero блок',
            'parent_slug'   => 'theme-options',
            'capability'    => 'edit_posts',
        ));
    }
    
    /**
     * Група полів для контактної інформації
     */
    acf_add_local_field_group(array(
        'key' => 'group_theme_contacts',
        'title' => 'Контактна інформація',
        'fields' => array(
            // Телефон у футері
            array(
                'key' => 'field_footer_phone',
                'label' => 'Телефон',
                'name' => 'footer_phone',
                'type' => 'text',
                'instructions' => 'Введіть номер телефону для відображення у футері',
                'required' => 1,
                'default_value' => '+380 (00) 000-00-00',
                'placeholder' => '+380 (00) 000-00-00',
                'prepend' => '',
                'append' => '',
                'maxlength' => 30,
            ),
            // Email у футері
            array(
                'key' => 'field_footer_email',
                'label' => 'Email',
                'name' => 'footer_email',
                'type' => 'email',
                'instructions' => 'Введіть email для відображення у футері',
                'required' => 1,
                'default_value' => '',
                'placeholder' => 'info@slavuta-rada.gov.ua',
                'prepend' => '',
                'append' => '',
            ),
            // Адреса
            array(
                'key' => 'field_footer_address',
                'label' => 'Адреса',
                'name' => 'footer_address',
                'type' => 'textarea',
                'instructions' => 'Введіть фізичну адресу',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'вул. Назва, 1, м. Славута, Хмельницька обл., 30000',
                'maxlength' => 200,
                'rows' => 2,
                'new_lines' => 'br',
            ),
            // Соціальні мережі
            array(
                'key' => 'field_social_networks',
                'label' => 'Соціальні мережі',
                'name' => 'social_networks',
                'type' => 'repeater',
                'instructions' => 'Додайте посилання на соціальні мережі',
                'required' => 0,
                'collapsed' => 'field_social_name',
                'min' => 0,
                'max' => 6,
                'layout' => 'table',
                'button_label' => 'Додати соцмережу',
                'sub_fields' => array(
                    array(
                        'key' => 'field_social_name',
                        'label' => 'Назва',
                        'name' => 'name',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 1,
                        'choices' => array(
                            'facebook' => 'Facebook',
                            'instagram' => 'Instagram',
                            'youtube' => 'YouTube',
                            'telegram' => 'Telegram',
                            'linkedin' => 'LinkedIn',
                            'twitter' => 'Twitter',
                        ),
                        'default_value' => 'facebook',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'ajax' => 0,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_social_url',
                        'label' => 'Посилання',
                        'name' => 'url',
                        'type' => 'url',
                        'instructions' => '',
                        'required' => 1,
                        'default_value' => '',
                        'placeholder' => 'https://facebook.com/...',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-kontakty',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Контактна інформація для відображення на сайті',
        'show_in_rest' => 0,
    ));
    
    /**
     * Група полів для Hero блоку
     */
    acf_add_local_field_group(array(
        'key' => 'group_hero_block',
        'title' => 'Налаштування Hero блоку',
        'fields' => array(
            // Заголовок
            array(
                'key' => 'field_hero_title',
                'label' => 'Заголовок',
                'name' => 'hero_title',
                'type' => 'text',
                'instructions' => 'Головний заголовок на головній сторінці',
                'required' => 1,
                'default_value' => 'Інвестуйте в майбутнє Славутської громади',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => 100,
            ),
            // Підзаголовок
            array(
                'key' => 'field_hero_subtitle',
                'label' => 'Підзаголовок',
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'instructions' => 'Текст під головним заголовком',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'Відкрийте нові можливості для вашого бізнесу',
                'maxlength' => 200,
                'rows' => 2,
                'new_lines' => 'br',
            ),
            // Фонове зображення
            array(
                'key' => 'field_hero_background',
                'label' => 'Фонове зображення',
                'name' => 'hero_background',
                'type' => 'image',
                'instructions' => 'Оберіть фонове зображення для Hero блоку (рекомендований розмір: 1920x1080px)',
                'required' => 1,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => 1200,
                'min_height' => 600,
                'max_width' => 3840,
                'max_height' => 2160,
                'max_size' => '5MB',
                'mime_types' => 'jpg,jpeg,png,webp',
            ),
            // Кнопка CTA
            array(
                'key' => 'field_hero_cta_text',
                'label' => 'Текст кнопки',
                'name' => 'hero_cta_text',
                'type' => 'text',
                'instructions' => 'Текст кнопки заклику до дії',
                'required' => 0,
                'default_value' => 'Переглянути проєкти',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => 50,
            ),
            array(
                'key' => 'field_hero_cta_link',
                'label' => 'Посилання кнопки',
                'name' => 'hero_cta_link',
                'type' => 'link',
                'instructions' => 'Оберіть сторінку або введіть посилання',
                'required' => 0,
                'return_format' => 'array',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'acf-options-hero-blok',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Налаштування Hero блоку на головній сторінці',
        'show_in_rest' => 0,
    ));
}
add_action('acf/init', 'slavuta_register_acf_fields');

/**
 * Налаштування Google Maps API для ACF
 */
function slavuta_acf_google_map_api($api) {
    // Вставте ваш Google Maps API ключ
    $api['key'] = 'YOUR_GOOGLE_MAPS_API_KEY';
    return $api;
}
add_filter('acf/fields/google_map/api', 'slavuta_acf_google_map_api');

/**
 * Додавання стилів для ACF в адмін-панелі
 */
function slavuta_acf_admin_styles() {
    ?>
    <style>
        /* Покращення відображення полів ACF */
        .acf-field-group {
            border: 1px solid #e5e5e5;
            background: #f9f9f9;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .acf-field-repeater .acf-table {
            background: #fff;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .acf-field-google-map {
            margin: 20px 0;
        }
        
        .acf-field-google-map .canvas {
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    <?php
}
add_action('admin_head', 'slavuta_acf_admin_styles');