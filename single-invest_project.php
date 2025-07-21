<?php
/**
 * Шаблон одиночного інвестиційного проєкту
 * 
 * @package SlavutaInvest
 */

// FILE: single-invest_project.php

get_header();

while (have_posts()) : the_post();
    // Отримуємо дані з ACF
    $short_description = get_field('short_description');
    $main_image = get_field('main_image');
    $image_gallery = get_field('image_gallery');
    $investment_amount = get_field('investment_amount');
    $project_status = get_field('project_status');
    $pdf_presentation = get_field('pdf_presentation');
    $categories = get_the_terms(get_the_ID(), 'project_category');
?>

<main id="main" class="site-main">
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-project'); ?>>
        
        <!-- Хлібні крихти -->
        <div class="breadcrumbs-section">
            <div class="container">
                <nav class="breadcrumbs" aria-label="<?php esc_attr_e('Навігація', 'slavuta-invest'); ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php esc_html_e('Головна', 'slavuta-invest'); ?>
                    </a>
                    <span class="separator">/</span>
                    <a href="<?php echo esc_url(get_post_type_archive_link('invest_project')); ?>">
                        <?php esc_html_e('Інвестиційні проєкти', 'slavuta-invest'); ?>
                    </a>
                    <span class="separator">/</span>
                    <span class="current"><?php the_title(); ?></span>
                </nav>
            </div>
        </div>
        
        <!-- Заголовок проєкту -->
        <header class="project-header">
            <div class="container">
                <div class="project-header-content">
                    <div class="project-header-main">
                        <h1 class="project-title"><?php the_title(); ?></h1>
                        
                        <?php if ($short_description) : ?>
                            <p class="project-description"><?php echo esc_html($short_description); ?></p>
                        <?php endif; ?>
                        
                        <div class="project-meta">
                            <?php if ($categories && !is_wp_error($categories)) : ?>
                                <div class="project-categories">
                                    <span class="meta-label"><?php esc_html_e('Галузь:', 'slavuta-invest'); ?></span>
                                    <?php foreach ($categories as $category) : ?>
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>" 
                                           class="category-link">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($project_status) : ?>
                                <div class="project-status-wrapper">
                                    <span class="meta-label"><?php esc_html_e('Статус:', 'slavuta-invest'); ?></span>
                                    <span class="project-status status-<?php echo sanitize_title($project_status); ?>">
                                        <?php echo esc_html($project_status); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($investment_amount) : ?>
                        <div class="project-header-sidebar">
                            <div class="investment-box">
                                <div class="investment-label">
                                    <?php esc_html_e('Необхідні інвестиції', 'slavuta-invest'); ?>
                                </div>
                                <div class="investment-amount">
                                    <?php echo number_format($investment_amount, 0, ',', ' '); ?> грн
                                </div>
                                <?php if ($pdf_presentation) : ?>
                                    <a href="<?php echo esc_url($pdf_presentation['url']); ?>" 
                                       class="btn btn-primary btn-block"
                                       download>
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M10 14L14 10M10 14L6 10M10 14V3" stroke="currentColor" stroke-width="2"/>
                                            <path d="M17 14V17H3V14" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        <?php esc_html_e('Завантажити презентацію', 'slavuta-invest'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        
        <!-- Галерея зображень -->
        <?php if ($main_image || $image_gallery) : ?>
            <section class="project-gallery-section">
                <div class="container">
                    <div class="project-gallery-wrapper">
                        <!-- Основний слайдер -->
                        <div class="swiper gallery-main">
                            <div class="swiper-wrapper">
                                <?php if ($main_image) : ?>
                                    <div class="swiper-slide">
                                        <img src="<?php echo esc_url($main_image['sizes']['project-large']); ?>" 
                                             alt="<?php echo esc_attr($main_image['alt'] ?: get_the_title()); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($image_gallery) : ?>
                                    <?php foreach ($image_gallery as $image) : ?>
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($image['sizes']['project-large']); ?>" 
                                                 alt="<?php echo esc_attr($image['alt'] ?: get_the_title()); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Навігація -->
                            <button class="swiper-button-prev">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                            <button class="swiper-button-next">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Превью слайдер -->
                        <?php if ($image_gallery && count($image_gallery) > 0) : ?>
                            <div class="swiper gallery-thumbs">
                                <div class="swiper-wrapper">
                                    <?php if ($main_image) : ?>
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($main_image['sizes']['thumbnail']); ?>" 
                                                 alt="<?php echo esc_attr($main_image['alt']); ?>">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($image_gallery as $image) : ?>
                                        <div class="swiper-slide">
                                            <img src="<?php echo esc_url($image['sizes']['thumbnail']); ?>" 
                                                 alt="<?php echo esc_attr($image['alt']); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        
        <!-- Основний контент -->
        <section class="project-content-section">
            <div class="container">
                <div class="project-content-wrapper">
                    <div class="project-content">
                        <h2 class="content-title"><?php esc_html_e('Про проєкт', 'slavuta-invest'); ?></h2>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    
                    <!-- Сайдбар з додатковою інформацією -->
                    <aside class="project-sidebar">
                        <!-- Контактна форма -->
                        <div class="sidebar-widget contact-widget">
                            <h3 class="widget-title">
                                <?php esc_html_e('Зацікавлені в проєкті?', 'slavuta-invest'); ?>
                            </h3>
                            <p class="widget-description">
                                <?php esc_html_e('Залиште свої контактні дані і ми зв\'яжемося з вами', 'slavuta-invest'); ?>
                            </p>
                            
                            <?php
                            // Fluent Forms integration
                            if (function_exists('fluentform_render_form')) {
                                // Замініть 2 на ID вашої контактної форми
                                echo fluentform_render_form(array(
                                    'id' => 2,
                                    'show_title' => false,
                                    'show_description' => false
                                ));
                            } else {
                                // Fallback форма
                                ?>
                                <form class="contact-form" action="#" method="post">
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
                                        <textarea name="message" 
                                                  placeholder="<?php esc_attr_e('Повідомлення', 'slavuta-invest'); ?>" 
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
                        
                        <!-- Поділитися -->
                        <div class="sidebar-widget share-widget">
                            <h3 class="widget-title"><?php esc_html_e('Поділитися', 'slavuta-invest'); ?></h3>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                                   class="share-button share-facebook"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.656 9.128 8.438 9.879V12.89h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.989C16.344 19.129 20 14.99 20 10z" fill="currentColor"/>
                                    </svg>
                                </a>
                                <a href="https://telegram.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                                   class="share-button share-telegram"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.89 6.82l-1.64 7.747c-.123.554-.447.688-.905.428l-2.5-1.842-1.206 1.16c-.133.133-.245.245-.503.245l.18-2.548 4.628-4.181c.201-.18-.044-.279-.312-.099L7.21 11.15 4.744 10.4c-.536-.168-.546-.536.112-.792l9.632-3.713c.447-.16.838.109.693.775z" fill="currentColor"/>
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" 
                                   class="share-button share-linkedin"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M18.52 0H1.477C.66 0 0 .645 0 1.441v17.114C0 19.352.66 20 1.477 20H18.52c.816 0 1.48-.648 1.48-1.445V1.441C20 .645 19.336 0 18.52 0zM5.932 17.043H2.968V7.496h2.964v9.547zM4.45 6.195a1.72 1.72 0 110-3.44 1.72 1.72 0 010 3.44zm12.593 10.848h-2.963v-4.64c0-1.106-.02-2.53-1.54-2.53-1.544 0-1.78 1.204-1.78 2.449v4.72H7.797V7.497h2.844v1.305h.04c.396-.75 1.363-1.54 2.804-1.54 3 0 3.556 1.975 3.556 4.546v5.235z" fill="currentColor"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
        
        <!-- Схожі проєкти -->
        <?php
        $related_args = array(
            'post_type' => 'invest_project',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'orderby' => 'rand',
        );
        
        // Якщо є категорії, шукаємо схожі по категорії
        if ($categories && !is_wp_error($categories)) {
            $category_ids = wp_list_pluck($categories, 'term_id');
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'project_category',
                    'field' => 'term_id',
                    'terms' => $category_ids,
                ),
            );
        }
        
        $related_query = new WP_Query($related_args);
        
        if ($related_query->have_posts()) :
        ?>
            <section class="related-projects-section">
                <div class="container">
                    <h2 class="section-title"><?php esc_html_e('Схожі проєкти', 'slavuta-invest'); ?></h2>
                    
                    <div class="projects-grid">
                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                            <?php get_template_part('template-parts/content', 'project-card'); ?>
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