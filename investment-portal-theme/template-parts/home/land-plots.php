<?php
/**
 * Шаблон блоку земельних ділянок
 * 
 * @package SlavutaInvest
 */

// FILE: template-parts/home/land-plots.php

// Запит для отримання земельних ділянок
$lands_query = new WP_Query(array(
    'post_type' => 'land_plot',
    'posts_per_page' => 9,
    'orderby' => 'date',
    'order' => 'DESC',
));

if ($lands_query->have_posts()) :
?>

<section class="land-plots-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Земельні ділянки', 'slavuta-invest'); ?></h2>
            <p class="section-subtitle">
                <?php esc_html_e('Вільні земельні ділянки для реалізації інвестиційних проєктів', 'slavuta-invest'); ?>
            </p>
        </div>
        
        <div class="land-plots-grid" data-show-more-container>
            <?php 
            $counter = 0;
            while ($lands_query->have_posts()) : $lands_query->the_post(); 
                $counter++;
                $area_hectares = get_field('area_hectares');
                $cadastral_number = get_field('cadastral_number');
                $land_purpose = get_field('land_purpose');
                $main_scheme_image = get_field('main_scheme_image');
                $plot_types = get_the_terms(get_the_ID(), 'plot_type');
                
                // Визначаємо чи елемент буде прихований спочатку
                $is_hidden = $counter > 6;
            ?>
                
                <article class="land-plot-card<?php echo $is_hidden ? ' hidden-item' : ''; ?>" 
                         <?php echo $is_hidden ? 'data-show-more-item' : ''; ?>>
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
                                        <span class="plot-type-badge">
                                            <?php echo esc_html($type->name); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="land-plot-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            
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
                            </div>
                            
                            <?php if ($land_purpose) : ?>
                                <p class="land-plot-purpose">
                                    <?php echo wp_trim_words($land_purpose, 12); ?>
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
        
        <?php if ($lands_query->found_posts > 6) : ?>
            <div class="show-more-wrapper">
                <button class="btn btn-outline btn-lg show-more-button" 
                        data-show-more-button
                        data-show-text="<?php esc_attr_e('Показати більше', 'slavuta-invest'); ?>"
                        data-hide-text="<?php esc_attr_e('Приховати', 'slavuta-invest'); ?>">
                    <span class="button-text"><?php esc_html_e('Показати більше', 'slavuta-invest'); ?></span>
                    <svg class="button-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
        
        <div class="section-footer">
            <a href="<?php echo esc_url(get_post_type_archive_link('land_plot')); ?>" 
               class="btn btn-primary">
                <?php esc_html_e('Всі земельні ділянки', 'slavuta-invest'); ?>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<?php
endif;
wp_reset_postdata();