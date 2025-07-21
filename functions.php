<?php
/**
 * Головний файл функцій теми Slavuta Invest
 *
 * @package SlavutaInvest
 */

// Захист від прямого доступу
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Визначення констант теми
define( 'SLAVUTA_THEME_VERSION', '1.0.0' );
define( 'SLAVUTA_THEME_PATH', get_template_directory() );
define( 'SLAVUTA_THEME_URI', get_template_directory_uri() );

// Підключення інтеграції з Vite.js
require_once SLAVUTA_THEME_PATH . '/inc/vite-assets.php';

// Підключення реєстрації полів ACF
require_once SLAVUTA_THEME_PATH . '/inc/acf-fields.php';

/**
 * Налаштування теми.
 */
function slavuta_theme_setup() {
    // Підтримка локалізації
    load_theme_textdomain( 'slavuta-invest', SLAVUTA_THEME_PATH . '/languages' );

    // Дозволяє WordPress керувати тегом <title>
    add_theme_support( 'title-tag' );

    // Підтримка мініатюр для постів
    add_theme_support( 'post-thumbnails' );
    
    // Підтримка кастомного логотипу
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Додавання кастомних розмірів зображень
    add_image_size( 'project-thumb', 400, 300, true );
    add_image_size( 'land-thumb', 400, 240, true );
    add_image_size( 'project-large', 1200, 800, true );

    // Реєстрація меню
    register_nav_menus( array(
        'header' => esc_html__( 'Головне меню', 'slavuta-invest' ),
        'footer' => esc_html__( 'Меню в підвалі', 'slavuta-invest' ),
    ) );

    // Підтримка HTML5 для стандартних елементів
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
}
add_action( 'after_setup_theme', 'slavuta_theme_setup' );

/**
 * Реєстрація кастомних типів записів та таксономій.
 */
function slavuta_register_post_types() {
    // CPT: Інвестиційні проєкти
    register_post_type( 'invest_project', array(
        'labels'       => array(
            'name'          => __( 'Інвестиційні проєкти', 'slavuta-invest' ),
            'singular_name' => __( 'Інвестиційний проєкт', 'slavuta-invest' ),
            'add_new'       => __( 'Додати новий', 'slavuta-invest' ),
            'add_new_item'  => __( 'Додати новий проєкт', 'slavuta-invest' ),
            'edit_item'     => __( 'Редагувати проєкт', 'slavuta-invest' ),
            'all_items'     => __( 'Всі проєкти', 'slavuta-invest' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'invest-projects' ),
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'    => 'dashicons-chart-area',
    ) );

    // CPT: Земельні ділянки
    register_post_type( 'land_plot', array(
        'labels'       => array(
            'name'          => __( 'Земельні ділянки', 'slavuta-invest' ),
            'singular_name' => __( 'Земельна ділянка', 'slavuta-invest' ),
            'add_new'       => __( 'Додати нову', 'slavuta-invest' ),
            'add_new_item'  => __( 'Додати нову ділянку', 'slavuta-invest' ),
            'edit_item'     => __( 'Редагувати ділянку', 'slavuta-invest' ),
            'all_items'     => __( 'Всі ділянки', 'slavuta-invest' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'land-plots' ),
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'    => 'dashicons-location-alt',
    ) );

    // Taxonomy: Галузь для проєктів
    register_taxonomy( 'project_category', 'invest_project', array(
        'labels'            => array(
            'name'              => __( 'Галузі', 'slavuta-invest' ),
            'singular_name'     => __( 'Галузь', 'slavuta-invest' ),
        ),
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'project-category' ),
    ) );

    // Taxonomy: Тип для ділянок
    register_taxonomy( 'plot_type', 'land_plot', array(
        'labels'            => array(
            'name'              => __( 'Типи ділянок', 'slavuta-invest' ),
            'singular_name'     => __( 'Тип ділянки', 'slavuta-invest' ),
        ),
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'plot-type' ),
    ) );
}
add_action( 'init', 'slavuta_register_post_types' );

/**
 * Кастомна пагінація.
 */
function slavuta_pagination() {
    the_posts_pagination( array(
        'mid_size'  => 2,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
    ) );
}