<?php
/**
 * Шаблон блоку останніх новин
 * 
 * @package SlavutaInvest
 */

// FILE: template-parts/home/latest-news.php

// Запит для отримання останніх новин
$news_query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC',
    'ignore_sticky_posts' => true,
));

if ($news_query->have_posts()) :
?>

<section class="latest-news-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Новини громади', 'slavuta-invest'); ?></h2>
            <p class="section-subtitle">
                <?php esc_html_e('Актуальні події та важливі оголошення', 'slavuta-invest'); ?>
            </p>
        </div>
        
        <div class="news-grid">
            <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                
                <article class="news-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="news-card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('project-thumb', array(
                                    'loading' => 'lazy',
                                    'alt' => get_the_title()
                                )); ?>
                            </a>
                            <div class="news-date">
                                <span class="date-day"><?php echo get_the_date('d'); ?></span>
                                <span class="date-month"><?php echo get_the_date('M'); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="news-card-content">
                        <div class="news-meta">
                            <?php
                            $categories = get_the_category();
                            if ($categories) :
                                $category = $categories[0];
                            ?>
                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                                   class="news-category">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!has_post_thumbnail()) : ?>
                                <time class="news-date-text" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="news-card-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        
                        <p class="news-card-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </p>
                        
                        <a href="<?php the_permalink(); ?>" class="news-card-link">
                            <?php esc_html_e('Читати далі', 'slavuta-invest'); ?>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </a>
                    </div>
                </article>
                
            <?php endwhile; ?>
        </div>
        
        <div class="section-footer">
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" 
               class="btn btn-outline">
                <?php esc_html_e('Всі новини', 'slavuta-invest'); ?>
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