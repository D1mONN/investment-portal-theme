<?php
/**
 * Шаблон одиночної земельної ділянки
 * 
 * @package SlavutaInvest
 */

// FILE: single-land_plot.php

get_header();

while (have_posts()) : the_post();
    // Отримуємо дані з ACF
    $area_hectares = get_field('area_hectares');
    $cadastral_number = get_field('cadastral_number');
    $land_purpose = get_field('land_purpose');
    $communications = get_field('communications');
    $main_scheme_image = get_field('main_scheme_image');
    $location_map = get_field('location_map');
    $plot_types = get_the_terms(get_the_ID(), 'plot_type');
?>

<main id="main" class="site-main">
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-land-plot'); ?>>
        
        <!-- Хлібні крихти -->
        <div class="breadcrumbs-section">
            <div class="container">
                <nav class="breadcrumbs" aria-label="<?php esc_attr_e('Навігація', 'slavuta-invest'); ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php esc_html_e('Головна', 'slavuta-invest'); ?>
                    </a>
                    <span class="separator">/</span>
                    <a href="<?php echo esc_url(get_post_type_archive_link('land_plot')); ?>">
                        <?php esc_html_e('Земельні ділянки', 'slavuta-invest'); ?>
                    </a>
                    <span class="separator">/</span>
                    <span class="current"><?php the_title(); ?></span>
                </nav>
            </div>
        </div>
        
        <!-- Заголовок ділянки -->
        <header class="land-plot-header">
            <div class="container">
                <div class="land-plot-header-content">
                    <h1 class="land-plot-title"><?php the_title(); ?></h1>
                    
                    <div class="land-plot-key-info">
                        <?php if ($area_hectares) : ?>
                            <div class="key-info-item">
                                <svg class="info-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect x="4" y="4" width="16" height="16" stroke="currentColor" stroke-width="2"/>
                                    <path d="M4 4L20 20M20 4L4 20" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <div class="info-content">
                                    <span class="info-label"><?php esc_html_e('Площа', 'slavuta-invest'); ?></span>
                                    <span class="info-value"><?php echo number_format($area_hectares, 2, ',', ' '); ?> га</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($cadastral_number) : ?>
                            <div class="key-info-item">
                                <svg class="info-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 6H21M5 12H19M8 18H16" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <div class="info-content">
                                    <span class="info-label"><?php esc_html_e('Кадастровий номер', 'slavuta-invest'); ?></span>
                                    <span class="info-value"><?php echo esc_html($cadastral_number); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($plot_types && !is_wp_error($plot_types)) : ?>
                            <div class="key-info-item">
                                <svg class="info-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 7H21L19 3H5L3 7Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M3 7V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V7" stroke="currentColor" stroke-width="2"/>
                                    <path d="M10 11H14" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <div class="info-content">
                                    <span class="info-label"><?php esc_html_e('Тип ділянки', 'slavuta-invest'); ?></span>
                                    <span class="info-value">
                                        <?php 
                                        $type_names = wp_list_pluck($plot_types, 'name');
                                        echo esc_html(implode(', ', $type_names));
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Основний контент -->
        <div class="land-plot-content-section">
            <div class="container">
                <div class="land-plot-layout">
                    <!-- Ліва колонка -->
                    <div class="land-plot-main">
                        <!-- Цільове призначення -->
                        <?php if ($land_purpose) : ?>
                            <section class="content-block">
                                <h2 class="block-title"><?php esc_html_e('Цільове призначення', 'slavuta-invest'); ?></h2>
                                <div class="block-content">
                                    <p><?php echo wp_kses_post($land_purpose); ?></p>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Опис ділянки -->
                        <?php if (get_the_content()) : ?>
                            <section class="content-block">
                                <h2 class="block-title"><?php esc_html_e('Опис ділянки', 'slavuta-invest'); ?></h2>
                                <div class="entry-content">
                                    <?php the_content(); ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Комунікації -->
                        <?php if ($communications) : ?>
                            <section class="content-block">
                                <h2 class="block-title"><?php esc_html_e('Комунікації', 'slavuta-invest'); ?></h2>
                                <div class="communications-list">
                                    <?php foreach ($communications as $communication) : ?>
                                        <div class="communication-item">
                                            <div class="communication-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                                    <path d="M12 7V12L15 15" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="communication-content">
                                                <h3 class="communication-name">
                                                    <?php echo esc_html($communication['name']); ?>
                                                </h3>
                                                <p class="communication-status">
                                                    <?php echo esc_html($communication['status']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Схема ділянки -->
                        <?php if ($main_scheme_image) : ?>
                            <section class="content-block">
                                <h2 class="block-title"><?php esc_html_e('Схема ділянки', 'slavuta-invest'); ?></h2>
                                <div class="scheme-image">
                                    <a href="<?php echo esc_url($main_scheme_image['url']); ?>" 
                                       data-lightbox="scheme"
                                       data-title="<?php echo esc_attr(get_the_title()); ?>">
                                        <img src="<?php echo esc_url($main_scheme_image['sizes']['large']); ?>" 
                                             alt="<?php echo esc_attr($main_scheme_image['alt'] ?: get_the_title()); ?>">
                                    </a>
                                    <p class="image-caption">
                                        <?php esc_html_e('Натисніть на зображення для збільшення', 'slavuta-invest'); ?>
                                    </p>
                                </div>
                            </section>
                        <?php endif; ?>
                        
                        <!-- Карта розташування -->
                        <?php if ($location_map) : ?>
                            <section class="content-block">
                                <h2 class="block-title"><?php esc_html_e('Розташування на карті', 'slavuta-invest'); ?></h2>
                                <div class="location-map">
                                    <div class="acf-map" data-zoom="14">
                                        <div class="marker" 
                                             data-lat="<?php echo esc_attr($location_map['lat']); ?>" 
                                             data-lng="<?php echo esc_attr($location_map['lng']); ?>">
                                            <h3><?php the_title(); ?></h3>
                                            <?php if ($area_hectares) : ?>
                                                <p><?php echo number_format($area_hectares, 2, ',', ' '); ?> га</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Права колонка (сайдбар) -->
                    <aside class="land-plot-sidebar">
                        <!-- Контактна форма -->
                        <div class="sidebar-widget contact-widget sticky-widget">
                            <h3 class="widget-title">
                                <?php esc_html_e('Зацікавлені в ділянці?', 'slavuta-invest'); ?>
                            </h3>
                            <p class="widget-description">
                                <?php esc_html_e('Отримайте детальну інформацію та умови оренди або продажу', 'slavuta-invest'); ?>
                            </p>
                            
                            <?php
                            // Fluent Forms integration
                            if (function_exists('fluentform_render_form')) {
                                // Замініть 3 на ID вашої контактної форми для земельних ділянок
                                echo fluentform_render_form(array(
                                    'id' => 3,
                                    'show_title' => false,
                                    'show_description' => false
                                ));
                            } else {
                                // Fallback форма
                                ?>
                                <form class="contact-form" action="#" method="post">
                                    <input type="hidden" name="land_plot_id" value="<?php echo get_the_ID(); ?>">
                                    <input type="hidden" name="land_plot_title" value="<?php echo esc_attr(get_the_title()); ?>">
                                    
                                    <div class="form-group">
                                        <input type="text" 
                                               name="name" 
                                               placeholder="<?php esc_attr_e('Ваше ім\'я', 'slavuta-invest'); ?>" 
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <input type="tel" 
                                               name="phone" 
                                               placeholder="<?php esc_attr_e('Телефон', 'slavuta-invest'); ?>" 
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" 
                                               name="email" 
                                               placeholder="<?php esc_attr_e('Email', 'slavuta-invest'); ?>" 
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <select name="interest_type" required>
                                            <option value=""><?php esc_html_e('Тип зацікавленості', 'slavuta-invest'); ?></option>
                                            <option value="rent"><?php esc_html_e('Оренда', 'slavuta-invest'); ?></option>
                                            <option value="purchase"><?php esc_html_e('Купівля', 'slavuta-invest'); ?></option>
                                            <option value="info"><?php esc_html_e('Інформація', 'slavuta-invest'); ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <textarea name="message" 
                                                  placeholder="<?php esc_attr_e('Ваше повідомлення', 'slavuta-invest'); ?>" 
                                                  rows="4"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <?php esc_html_e('Надіслати запит', 'slavuta-invest'); ?>
                                    </button>
                                </form>
                                <?php
                            }
                            ?>
                        </div>
                        
                        <!-- Завантажити документи -->
                        <div class="sidebar-widget download-widget">
                            <h3 class="widget-title"><?php esc_html_e('Документи', 'slavuta-invest'); ?></h3>
                            <div class="download-list">
                                <?php if ($main_scheme_image) : ?>
                                    <a href="<?php echo esc_url($main_scheme_image['url']); ?>" 
                                       class="download-item"
                                       download>
                                        <svg class="download-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M10 1V13M10 13L14 9M10 13L6 9" stroke="currentColor" stroke-width="2"/>
                                            <path d="M1 13V17C1 18.1 1.9 19 3 19H17C18.1 19 19 18.1 19 17V13" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        <span><?php esc_html_e('Схема ділянки', 'slavuta-invest'); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Контактна інформація -->
                        <div class="sidebar-widget info-widget">
                            <h3 class="widget-title"><?php esc_html_e('Контактна інформація', 'slavuta-invest'); ?></h3>
                            <div class="contact-info">
                                <?php if (get_field('footer_phone', 'option')) : ?>
                                    <div class="contact-item">
                                        <svg class="contact-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M13.5 10.34C12.55 10.34 11.63 10.17 10.78 9.86C10.51 9.77 10.21 9.84 10 10.05L8.62 11.89C6.49 10.83 4.17 8.51 3.11 6.38L4.95 5C5.16 4.79 5.23 4.49 5.14 4.22C4.83 3.37 4.66 2.45 4.66 1.5C4.66 1.05 4.29 0.68 3.84 0.68H1.5C1.05 0.68 0.68 1.05 0.68 1.5C0.68 9.23 6.77 15.32 14.5 15.32C14.95 15.32 15.32 14.95 15.32 14.5V12.16C15.32 11.71 14.95 11.34 14.5 11.34C14.17 11.34 13.84 11.31 13.5 11.34V10.34Z" fill="currentColor"/>
                                        </svg>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', get_field('footer_phone', 'option'))); ?>">
                                            <?php the_field('footer_phone', 'option'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (get_field('footer_email', 'option')) : ?>
                                    <div class="contact-item">
                                        <svg class="contact-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M14 3H2C1.45 3 1 3.45 1 4V12C1 12.55 1.45 13 2 13H14C14.55 13 15 12.55 15 12V4C15 3.45 14.55 3 14 3ZM14 5L8 8.5L2 5V4L8 7.5L14 4V5Z" fill="currentColor"/>
                                        </svg>
                                        <a href="mailto:<?php echo esc_attr(get_field('footer_email', 'option')); ?>">
                                            <?php the_field('footer_email', 'option'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
        
        <!-- Схожі ділянки -->
        <?php
        $related_args = array(
            'post_type' => 'land_plot',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'orderby' => 'rand',
        );
        
        // Якщо є типи ділянок, шукаємо схожі
        if ($plot_types && !is_wp_error($plot_types)) {
            $type_ids = wp_list_pluck($plot_types, 'term_id');
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'plot_type',
                    'field' => 'term_id',
                    'terms' => $type_ids,
                ),
            );
        }
        
        $related_query = new WP_Query($related_args);
        
        if ($related_query->have_posts()) :
        ?>
            <section class="related-lands-section">
                <div class="container">
                    <h2 class="section-title"><?php esc_html_e('Інші земельні ділянки', 'slavuta-invest'); ?></h2>
                    
                    <div class="land-plots-grid">
                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                            <?php get_template_part('template-parts/content', 'land-card'); ?>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
        <?php
        endif;
        wp_reset_postdata();
        ?>
        
    </article>
</main>

<?php
endwhile;
get_footer();