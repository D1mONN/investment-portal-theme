<?php
/**
 * Шаблон блоку-слайдера інвестиційних проєктів
 *
 * @package SlavutaInvest
 */

$projects_query = new WP_Query(array(
    'post_type' => 'invest_project',
    'posts_per_page' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
));

if ($projects_query->have_posts()) :
?>
<section id="projects" class="projects-slider-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Інвестиційні проєкти', 'slavuta-invest'); ?></h2>
            <p class="section-subtitle"><?php esc_html_e('Перспективні напрямки для вкладення капіталу в громаді', 'slavuta-invest'); ?></p>
        </div>
    </div>
    
    <div class="projects-slider-wrapper">
        <div class="swiper projects-slider">
            <div class="swiper-wrapper">
                <?php while ($projects_query->have_posts()) : $projects_query->the_post();
                    get_template_part('template-parts/content', 'project-card');
                endwhile; ?>
            </div>
        </div>
        
        <div class="swiper-pagination"></div>
        
        <button class="swiper-button-prev">
            <svg width="24" height="24" viewBox="0 0 24 24"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"/></svg>
        </button>
        <button class="swiper-button-next">
            <svg width="24" height="24" viewBox="0 0 24 24"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"/></svg>
        </button>
    </div>
</section>
<?php
endif;
wp_reset_postdata();