<?php
/**
 * Допоміжні функції для теми
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Функція breadcrumbs для навігації
 */
function slavutska_breadcrumbs() 
{
    $separator = '<i class="icon-arrow-right breadcrumbs-separator" aria-hidden="true"></i>';
    $home_title = __('Головна', 'slavutska-investment');
    
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Навігаційний шлях', 'slavutska-investment') . '">';
    echo '<ol class="breadcrumbs-list">';
    
    // Головна сторінка
    if (!is_home() && !is_front_page()) {
        echo '<li class="breadcrumbs-item">';
        echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_title) . '</a>';
        echo '</li>';
        echo '<li class="breadcrumbs-separator">' . $separator . '</li>';
    }
    
    if (is_home()) {
        echo '<li class="breadcrumbs-item current">' . esc_html($home_title) . '</li>';
    } elseif (is_category()) {
        $category = get_category(get_query_var('cat'));
        if ($category->parent != 0) {
            $parent_cats = get_category_parents($category->parent, true, '</li><li class="breadcrumbs-separator">' . $separator . '</li><li class="breadcrumbs-item">');
            echo '<li class="breadcrumbs-item">' . $parent_cats . '</li>';
            echo '<li class="breadcrumbs-separator">' . $separator . '</li>';
        }
        echo '<li class="breadcrumbs-item current">' . single_cat_title('', false) . '</li>';
    } elseif (is_single()) {
        $post_type = get_post_type();
        
        if ($post_type == 'investment') {
            echo '<li class="breadcrumbs-item">';
            echo '<a href="' . esc_url(get_post_type_archive_link('investment')) . '">';
            echo esc_html__('Інвестиційні пропозиції', 'slavutska-investment');
            echo '</a></li>';
            echo '<li class="breadcrumbs-separator">' . $separator . '</li>';
        } elseif ($post_type == 'land_plot') {
            echo '<li class="breadcrumbs-item">';
            echo '<a href="' . esc_url(get_post_type_archive_link('land_plot')) . '">';
            echo esc_html__('Земельні ділянки', 'slavutska-investment');
            echo '</a></li>';
            echo '<li class="breadcrumbs-separator">' . $separator . '</li>';
        } elseif ($post_type == 'post') {
            $category = get_the_category();
            if ($category) {
                $cat = $category[0];
                echo '<li class="breadcrumbs-item">';
                echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a>';
                echo '</li>';
                echo '<li class="breadcrumbs-separator">' . $separator . '</li>';
            }
        }
        
        echo '<li class="breadcrumbs-item current">' . get_the_title() . '</li>';
    } elseif (is_page()) {
        if ($post = get_post(get_the_ID())) {
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = array();
                
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id = $page->post_parent;
                }
                
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) {
                    echo '<li class="breadcrumbs-item">' . $crumb . '</li>';
                    echo '<li class="breadcrumbs-separator">' . $separator . '</li>';
                }
            }
        }
        echo '<li class="breadcrumbs-item current">' . get_the_title() . '</li>';
    } elseif (is_tag()) {
        echo '<li class="breadcrumbs-item current">';
        printf(__('Тег: %s', 'slavutska-investment'), single_tag_title('', false));
        echo '</li>';
    } elseif (is_author()) {
        echo '<li class="breadcrumbs-item current">';
        printf(__('Автор: %s', 'slavutska-investment'), get_the_author());
        echo '</li>';
    } elseif (is_day()) {
        echo '<li class="breadcrumbs-item current">';
        printf(__('Архів за %s', 'slavutska-investment'), get_the_date());
        echo '</li>';
    } elseif (is_month()) {
        echo '<li class="breadcrumbs-item current">';
        printf(__('Архів за %s', 'slavutska-investment'), get_the_date('F Y'));
        echo '</li>';
    } elseif (is_year()) {
        echo '<li class="breadcrumbs-item current">';
        printf(__('Архів за %s', 'slavutska-investment'), get_the_date('Y'));
        echo '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumbs-item current">';
        printf(__('Результати пошуку: %s', 'slavutska-investment'), get_search_query());
        echo '</li>';
    } elseif (is_404()) {
        echo '<li class="breadcrumbs-item current">' . esc_html__('Сторінка не знайдена', 'slavutska-investment') . '</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}

/**
 * Fallback меню якщо основне меню не встановлено
 */
function slavutska_fallback_menu() 
{
    echo '<ul class="primary-menu fallback-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Головна', 'slavutska-investment') . '</a></li>';
    
    if (post_type_exists('investment')) {
        echo '<li><a href="' . esc_url(get_post_type_archive_link('investment')) . '">';
        echo esc_html__('Інвестиції', 'slavutska-investment') . '</a></li>';
    }
    
    if (post_type_exists('land_plot')) {
        echo '<li><a href="' . esc_url(get_post_type_archive_link('land_plot')) . '">';
        echo esc_html__('Земельні ділянки', 'slavutska-investment') . '</a></li>';
    }
    
    echo '<li><a href="#contact">' . esc_html__('Контакти', 'slavutska-investment') . '</a></li>';
    echo '</ul>';
}

/**
 * Walker для кастомізації виводу меню
 */
class Slavutska_Walker_Nav_Menu extends Walker_Nav_Menu 
{
    public function start_lvl(&$output, $depth = 0, $args = array()) 
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu level-$depth\">\n";
    }
    
    public function end_lvl(&$output, $depth = 0, $args = array()) 
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) 
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Додавання класу для поточної сторінки
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'active';
        }
        
        // Додавання класу для батьківського елемента
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'has-dropdown';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target) ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn) ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url) ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        // Додавання іконки для випадаючого меню
        $item_output = $args->before ?? '';
        $item_output .= '<a' . $attributes .'>';
        $item_output .= ($args->link_before ?? '') . apply_filters('the_title', $item->title, $item->ID) . ($args->link_after ?? '');
        
        if (in_array('menu-item-has-children', $classes)) {
            $item_output .= ' <i class="icon-chevron-down dropdown-icon" aria-hidden="true"></i>';
        }
        
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    public function end_el(&$output, $item, $depth = 0, $args = array()) 
    {
        $output .= "</li>\n";
    }
}

/**
 * Отримання пов'язаних постів
 */
function slavutska_get_related_posts($post_id, $limit = 3) 
{
    $post = get_post($post_id);
    $related_posts = array();
    
    if (!$post) {
        return $related_posts;
    }
    
    // Спочатку шукаємо пости з тими ж тегами
    if ($post->post_type === 'post') {
        $tags = wp_get_post_tags($post_id);
        if ($tags) {
            $tag_ids = array();
            foreach ($tags as $tag) {
                $tag_ids[] = $tag->term_id;
            }
            
            $related_posts = get_posts(array(
                'tag__in' => $tag_ids,
                'post__not_in' => array($post_id),
                'posts_per_page' => $limit,
                'post_status' => 'publish'
            ));
        }
    }
    
    // Якщо не знайшли достатньо постів, додаємо з тої ж категорії
    if (count($related_posts) < $limit) {
        $remaining = $limit - count($related_posts);
        $exclude_ids = array_merge(array($post_id), wp_list_pluck($related_posts, 'ID'));
        
        if ($post->post_type === 'investment') {
            $terms = get_the_terms($post_id, 'investment_category');
        } elseif ($post->post_type === 'land_plot') {
            $terms = get_the_terms($post_id, 'land_type');
        } else {
            $terms = get_the_category($post_id);
        }
        
        if ($terms && !is_wp_error($terms)) {
            $term_ids = wp_list_pluck($terms, 'term_id');
            
            $args = array(
                'post_type' => $post->post_type,
                'post__not_in' => $exclude_ids,
                'posts_per_page' => $remaining,
                'post_status' => 'publish'
            );
            
            if ($post->post_type === 'investment') {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'investment_category',
                        'field' => 'term_id',
                        'terms' => $term_ids
                    )
                );
            } elseif ($post->post_type === 'land_plot') {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'land_type',
                        'field' => 'term_id',
                        'terms' => $term_ids
                    )
                );
            } else {
                $args['category__in'] = $term_ids;
            }
            
            $category_posts = get_posts($args);
            $related_posts = array_merge($related_posts, $category_posts);
        }
    }
    
    // Якщо все ще недостатньо, додаємо випадкові пости того ж типу
    if (count($related_posts) < $limit) {
        $remaining = $limit - count($related_posts);
        $exclude_ids = array_merge(array($post_id), wp_list_pluck($related_posts, 'ID'));
        
        $random_posts = get_posts(array(
            'post_type' => $post->post_type,
            'post__not_in' => $exclude_ids,
            'posts_per_page' => $remaining,
            'orderby' => 'rand',
            'post_status' => 'publish'
        ));
        
        $related_posts = array_merge($related_posts, $random_posts);
    }
    
    return array_slice($related_posts, 0, $limit);
}

/**
 * Форматування числа для читабельності
 */
function slavutska_format_number($number, $decimals = 0) 
{
    if (!is_numeric($number)) {
        return $number;
    }
    
    return number_format($number, $decimals, ',', ' ');
}

/**
 * Отримання читаємого розміру файлу
 */
function slavutska_format_file_size($bytes) 
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' ГБ';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' МБ';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' КБ';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' байт';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' байт';
    } else {
        $bytes = '0 байт';
    }
    
    return $bytes;
}

/**
 * Генерація excerpt з HTML контенту
 */
function slavutska_get_excerpt($content, $word_count = 55, $more = '...') 
{
    $content = strip_shortcodes($content);
    $content = strip_tags($content);
    $content = str_replace(array("\r", "\n", "\t"), ' ', $content);
    $content = trim(preg_replace('/\s+/', ' ', $content));
    
    $words = explode(' ', $content);
    
    if (count($words) > $word_count) {
        $words = array_slice($words, 0, $word_count);
        $content = implode(' ', $words) . $more;
    }
    
    return $content;
}

/**
 * Перевірка чи є поточна сторінка архівною для кастомних типів постів
 */
function slavutska_is_custom_post_type_archive($post_types = array()) 
{
    if (empty($post_types)) {
        $post_types = array('investment', 'land_plot');
    }
    
    foreach ($post_types as $post_type) {
        if (is_post_type_archive($post_type)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Отримання URL першого зображення з контенту поста
 */
function slavutska_get_first_image_url($post_id = null) 
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field('post_content', $post_id);
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
    
    if (!empty($matches[1])) {
        return $matches[1][0];
    }
    
    return false;
}

/**
 * Додавання схеми для структурованих даних
 */
function slavutska_get_schema_markup($type, $data = array()) 
{
    $schema = array(
        '@context' => 'https://schema.org'
    );
    
    switch ($type) {
        case 'organization':
            $schema['@type'] = 'GovernmentOrganization';
            $schema = array_merge($schema, $data);
            break;
            
        case 'investment':
            $schema['@type'] = 'Product';
            $schema = array_merge($schema, $data);
            break;
            
        case 'land_plot':
            $schema['@type'] = 'RealEstateListing';
            $schema = array_merge($schema, $data);
            break;
            
        default:
            $schema['@type'] = 'WebPage';
            $schema = array_merge($schema, $data);
    }
    
    return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * Логування помилок теми
 */
function slavutska_log_error($message, $data = array()) 
{
    if (WP_DEBUG_LOG) {
        $log_message = '[Slavutska Theme] ' . $message;
        
        if (!empty($data)) {
            $log_message .= ' | Data: ' . print_r($data, true);
        }
        
        error_log($log_message);
    }
}