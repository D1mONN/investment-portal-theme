<?php
/**
 * Шаблон архіву земельних ділянок
 * 
 * @package SlavutaInvest
 */

// FILE: archive-land_plot.php

get_header();
?>

<main id="main" class="site-main">
    <section class="archive-section">
        <div class="container">
            <!-- Заголовок сторінки -->
            <header class="archive-header">
                <h1 class="archive-title"><?php esc_html_e('Земельні ділянки', 'slavuta-invest'); ?></h1>
                <p class="archive-description">
                    <?php esc_html_e('Вільні земельні ділянки для реалізації інвестиційних проєктів на території Славутської громади', 'slavuta-invest'); ?>
                </p>
            </header>
            
            <!-- Фільтри -->
            <div class="archive-filters">
                <form class="filters-form" method="get" action="<?php echo esc_url(get_post_type_archive_link('land_plot')); ?>">
                    <div class="filters-row">
                        <!-- Фільтр за типом ділянки -->
                        <div class="filter-item">
                            <label for="plot_type" class="filter-label">
                                <?php esc_html_e('Тип ділянки', 'slavuta-invest'); ?>
                            </label>
                            <?php
                            $current_type = isset($_GET['plot_type']) ? sanitize_text_field($_GET['plot_type']) : '';
                            $types = get_terms(array(
                                'taxonomy' => 'plot_type',
                                'hide_empty' => true,
                            ));
                            
                            if ($types && !is_wp_error($types)) :
                            ?>
                                <select name="plot_type" id="plot_type" class="filter-select">
                                    <option value=""><?php esc_html_e('Всі типи', 'slavuta-invest'); ?></option>
                                    <?php foreach ($types as $type) : ?>
                                        <option value="<?php echo esc_attr($type->slug); ?>" 
                                                <?php selected($current_type, $type->slug); ?>>
                                            <?php echo esc_html($type->name); ?> (<?php echo $type->count; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Фільтр за площею -->
                        <div class="filter-item">
                            <label for="area_range" class="filter-label">
                                <?php esc_html_e('Площа ділянки', 'slavuta-invest'); ?>
                            </label>
                            <?php
                            $current_area = isset($_GET['area_range']) ? sanitize_text_field($_GET['area_range']) : '';
                            ?>
                            <select name="area_range" id="area_range" class="filter-select">
                                <option value=""><?php esc_html_e('Будь-яка площа', 'slavuta-invest'); ?></option>
                                <option value="0-1" <?php selected($current_area, '0-1'); ?>>
                                    <?php esc_html_e('До 1 га', 'slavuta-invest'); ?>
                                </option>
                                <option value="1-5" <?php selected($current_area, '1-5'); ?>>
                                    <?php esc_html_e('1-5 га', 'slavuta-invest'); ?>
                                </option>
                                <option value="5-10" <?php selected($current_area, '5-10'); ?>>
                                    <?php esc_html_e('5-10 га', 'slavuta-invest'); ?>
                                </option>
                                <option value="10+" <?php selected($current_area, '10+'); ?>>
                                    <?php esc_html_e('Більше 10 га', 'slavuta-invest'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Сортування -->
                        <div class="filter-item">
                            <label for="orderby" class="filter-label">
                                <?php esc_html_e('Сортування', 'slavuta-invest'); ?>
                            </label>
                            <?php
                            $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
                            ?>
                            <select name="orderby" id="orderby" class="filter-select">
                                <option value="date" <?php selected($current_orderby, 'date'); ?>>
                                    <?php esc_html_e('За датою', 'slavuta-invest'); ?>
                                </option>
                                <option value="title" <?php selected($current_orderby, 'title'); ?>>
                                    <?php esc_html_e('За назвою', 'slavuta-invest'); ?>
                                </option>
                                <option value="area" <?php selected($current_orderby, 'area'); ?>>
                                    <?php esc_html_e('За площею', 'slavuta-invest'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <?php esc_html_e('Застосувати', 'slavuta-invest'); ?>
                            </button>
                            <a href="<?php echo esc_url(get_post_type_archive_link('land_plot')); ?>" 
                               class="btn btn-outline">
                                <?php esc_html_e('Скинути', 'slavuta-invest'); ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if (have_posts()) : ?>
                
                <!-- Результати -->
                <div class="archive-results">
                    <p class="results-count">
                        <?php
                        global $wp_query;
                        printf(
                            esc_html(_n(
                                'Знайдено %s ділянку',
                                'Знайдено %s ділянок',
                                $wp_query->found_posts,
                                'slavuta-invest'
                            )),
                            number_format_i18n($wp_query->found_posts)
                        );
                        ?>
                    </p>
                </div>
                
                <!-- Сітка ділянок -->
                <div class="land-plots-grid archive-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        $area_hectares = get_field('area_hectares');
                        $cadastral_number = get_field('cadastral_number');
                        $land_purpose = get_field('land_purpose');
                        $main_scheme_image = get_field('main_scheme_image');
                        $plot_types = get_the_terms(get_the_ID(), 'plot_type');
                        $communications = get_field('communications');
                        ?>
                        
                        <article id="post-<?php the_ID(); ?>" <?php post_class('land-plot-card'); ?>>
                            <div class="land-plot-card-inner">
                                <?php if ($main_scheme_image) : ?>
                                    <div class="land-plot-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo esc_url($main_scheme_image['sizes']['land-thumb']); ?>" 
                                                 alt="<?php echo esc_attr($main_scheme_image['alt'] ?: get_the_title()); ?>"
                                                 loading="lazy">
                                        </a>
                                    </div>
                                <?php else : ?>
                                    <div class="land-plot-image land-plot-image-placeholder">
                                        <a href="<?php the_permalink(); ?>">
                                            <svg width="100" height="100" viewBox="0 0 100 100" fill="none">
                                                <rect width="100" height="100" fill="#F5F5F5"/>
                                                <path d="M30 70L50 50L70 70M40 60L55 45L70 60" stroke="#CCCCCC" stroke-width="2"/>
                                                <circle cx="60" cy="40" r="5" stroke="#CCCCCC" stroke-width="2"/>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="land-plot-content">
                                    <?php if ($plot_types && !is_wp_error($plot_types)) : ?>
                                        <div class="land-plot-types">
                                            <?php foreach ($plot_types as $type) : ?>
                                                <a href="<?php echo esc_url(add_query_arg('plot_type', $type->slug, get_post_type_archive_link('land_plot'))); ?>" 
                                                   class="plot-type-badge">
                                                    <?php echo esc_html($type->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <h2 class="land-plot-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                    
                                    <div class="land-plot-info">
                                        <?php if ($area_hectares) : ?>
                                            <div class="info-item">
                                                <svg class="info-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <rect x="2" y="2" width="12" height="12" stroke="currentColor" stroke-width="1.5"/>
                                                    <path d="M2 2L14 14M14 2L2 14" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                                <span><?php echo number_format($area_hectares, 2, ',', ' '); ?> га</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($cadastral_number) : ?>
                                            <div class="info-item">
                                                <svg class="info-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M2 4H14M4 8H12M6 12H10" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                                <span><?php echo esc_html($cadastral_number); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($communications && count($communications) > 0) : ?>
                                            <div class="info-item">
                                                <svg class="info-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/>
                                                    <path d="M8 4V8L11 11" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                                <span>
                                                    <?php 
                                                    printf(
                                                        esc_html(_n(
                                                            '%s комунікація',
                                                            '%s комунікацій',
                                                            count($communications),
                                                            'slavuta-invest'
                                                        )),
                                                        count($communications)
                                                    );
                                                    ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($land_purpose) : ?>
                                        <p class="land-plot-purpose">
                                            <?php echo wp_trim_words($land_purpose, 15); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <a href="<?php the_permalink(); ?>" class="land-plot-link">
                                        <?php esc_html_e('Детальніше', 'slavuta-invest'); ?>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                        
                    <?php endwhile; ?>
                </div>
                
                <!-- Пагінація -->
                <?php slavuta_pagination(); ?>
                
            <?php else : ?>
                
                <!-- Повідомлення про відсутність результатів -->
                <div class="no-results">
                    <svg class="no-results-icon" width="80" height="80" viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="39" stroke="#E0E0E0" stroke-width="2"/>
                        <path d="M30 30L50 50M50 30L30 50" stroke="#E0E0E0" stroke-width="2"/>
                    </svg>
                    <h2 class="no-results-title">
                        <?php esc_html_e('Земельні ділянки не знайдено', 'slavuta-invest'); ?>
                    </h2>
                    <p class="no-results-text">
                        <?php esc_html_e('Спробуйте змінити параметри фільтрації або скинути фільтри.', 'slavuta-invest'); ?>
                    </p>
                    <a href="<?php echo esc_url(get_post_type_archive_link('land_plot')); ?>" 
                       class="btn btn-primary">
                        <?php esc_html_e('Скинути фільтри', 'slavuta-invest'); ?>
                    </a>
                </div>
                
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();

// Додавання логіки фільтрації через pre_get_posts
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('land_plot')) {
        
        // Фільтр за типом ділянки
        if (isset($_GET['plot_type']) && $_GET['plot_type']) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'plot_type',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['plot_type']),
                ),
            ));
        }
        
        // Фільтр за площею
        if (isset($_GET['area_range']) && $_GET['area_range']) {
            $area_range = sanitize_text_field($_GET['area_range']);
            $meta_query = array();
            
            switch ($area_range) {
                case '0-1':
                    $meta_query[] = array(
                        'key' => 'area_hectares',
                        'value' => 1,
                        'compare' => '<=',
                        'type' => 'NUMERIC',
                    );
                    break;
                case '1-5':
                    $meta_query[] = array(
                        'relation' => 'AND',
                        array(
                            'key' => 'area_hectares',
                            'value' => 1,
                            'compare' => '>',
                            'type' => 'NUMERIC',
                        ),
                        array(
                            'key' => 'area_hectares',
                            'value' => 5,
                            'compare' => '<=',
                            'type' => 'NUMERIC',
                        ),
                    );
                    break;
                case '5-10':
                    $meta_query[] = array(
                        'relation' => 'AND',
                        array(
                            'key' => 'area_hectares',
                            'value' => 5,
                            'compare' => '>',
                            'type' => 'NUMERIC',
                        ),
                        array(
                            'key' => 'area_hectares',
                            'value' => 10,
                            'compare' => '<=',
                            'type' => 'NUMERIC',
                        ),
                    );
                    break;
                case '10+':
                    $meta_query[] = array(
                        'key' => 'area_hectares',
                        'value' => 10,
                        'compare' => '>',
                        'type' => 'NUMERIC',
                    );
                    break;
            }
            
            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }
        }
        
        // Сортування
        if (isset($_GET['orderby']) && $_GET['orderby']) {
            $orderby = sanitize_text_field($_GET['orderby']);
            
            switch ($orderby) {
                case 'title':
                    $query->set('orderby', 'title');
                    $query->set('order', 'ASC');
                    break;
                case 'area':
                    $query->set('meta_key', 'area_hectares');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        }
    }
});