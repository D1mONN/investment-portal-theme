<?php
/**
 * Шаблон для окремої сторінки інвестиційної пропозиції
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) : the_post();
    // Отримання мета-даних
    $investment_amount = get_post_meta(get_the_ID(), '_investment_amount', true);
    $investment_period = get_post_meta(get_the_ID(), '_investment_period', true);
    $expected_return = get_post_meta(get_the_ID(), '_expected_return', true);
    $contact_person = get_post_meta(get_the_ID(), '_contact_person', true);
    $contact_phone = get_post_meta(get_the_ID(), '_contact_phone', true);
    $contact_email = get_post_meta(get_the_ID(), '_contact_email', true);
    $location = get_post_meta(get_the_ID(), '_location', true);
    $is_featured = get_post_meta(get_the_ID(), '_is_featured', true);
    
    // Отримання категорій
    $categories = get_the_terms(get_the_ID(), 'investment_category');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('investment-single'); ?>>
    <!-- Hero секція -->
    <section class="investment-hero">
        <div class="container">
            <div class="investment-hero-content">
                <div class="investment-meta-bar">
                    <div class="investment-categories">
                        <?php if ($categories && !is_wp_error($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <span class="investment-category-badge">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if ($is_featured): ?>
                            <span class="investment-featured-badge">
                                <i class="icon-star" aria-hidden="true"></i>
                                <?php _e('Рекомендовано', 'slavutska-investment'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="investment-actions">
                        <button class="btn btn--outline btn--small share-button" 
                                data-url="<?php echo esc_url(get_permalink()); ?>"
                                data-title="<?php echo esc_attr(get_the_title()); ?>">
                            <i class="icon-share" aria-hidden="true"></i>
                            <?php _e('Поділитися', 'slavutska-investment'); ?>
                        </button>
                        
                        <button class="btn btn--outline btn--small print-button" onclick="window.print()">
                            <i class="icon-print" aria-hidden="true"></i>
                            <?php _e('Друк', 'slavutska-investment'); ?>
                        </button>
                    </div>
                </div>
                
                <header class="investment-header">
                    <h1 class="investment-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()): ?>
                        <div class="investment-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="investment-key-metrics">
                        <?php if ($investment_amount): ?>
                            <div class="key-metric">
                                <div class="metric-icon">
                                    <i class="icon-money" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Сума інвестицій', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo number_format($investment_amount, 0, ',', ' '); ?> грн</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($expected_return): ?>
                            <div class="key-metric">
                                <div class="metric-icon">
                                    <i class="icon-trending-up" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Очікувана прибутковість', 'slavutska-investment'); ?></span>
                                    <span class="metric-value metric-value--highlight"><?php echo esc_html($expected_return); ?>%</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($investment_period): ?>
                            <div class="key-metric">
                                <div class="metric-icon">
                                    <i class="icon-calendar" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Термін реалізації', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo esc_html($investment_period); ?> <?php _e('міс.', 'slavutska-investment'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($location): ?>
                            <div class="key-metric">
                                <div class="metric-icon">
                                    <i class="icon-map-pin" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Місцезнаходження', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo esc_html($location); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>
            </div>
        </div>
    </section>

    <!-- Основний контент -->
    <section class="investment-content">
        <div class="container">
            <div class="investment-layout">
                <div class="investment-main">
                    <!-- Зображення проекту -->
                    <?php if (has_post_thumbnail()): ?>
                        <div class="investment-featured-image">
                            <?php the_post_thumbnail('large', [
                                'class' => 'investment-image',
                                'alt' => get_the_title()
                            ]); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Опис проекту -->
                    <div class="investment-description">
                        <h2 class="section-title"><?php _e('Опис проекту', 'slavutska-investment'); ?></h2>
                        <div class="investment-text">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    
                    <!-- Додаткові секції -->
                    <?php
                    $sections = [
                        'advantages' => __('Переваги проекту', 'slavutska-investment'),
                        'risks' => __('Ризики та мітигація', 'slavutska-investment'),
                        'timeline' => __('Етапи реалізації', 'slavutska-investment'),
                        'requirements' => __('Вимоги до інвестора', 'slavutska-investment')
                    ];
                    
                    foreach ($sections as $section_key => $section_title):
                        $section_content = get_post_meta(get_the_ID(), "_investment_{$section_key}", true);
                        if ($section_content):
                    ?>
                        <div class="investment-section">
                            <h3 class="section-title"><?php echo esc_html($section_title); ?></h3>
                            <div class="section-content">
                                <?php echo wp_kses_post($section_content); ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                    
                    <!-- Галерея зображень -->
                    <?php
                    $gallery_images = get_post_meta(get_the_ID(), '_investment_gallery', true);
                    if ($gallery_images):
                        $image_ids = explode(',', $gallery_images);
                    ?>
                        <div class="investment-section">
                            <h3 class="section-title"><?php _e('Галерея проекту', 'slavutska-investment'); ?></h3>
                            <div class="investment-gallery">
                                <?php foreach ($image_ids as $image_id): ?>
                                    <div class="gallery-item">
                                        <a href="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'full')); ?>" 
                                           class="gallery-link"
                                           data-lightbox="investment-gallery">
                                            <?php echo wp_get_attachment_image($image_id, 'medium', false, [
                                                'class' => 'gallery-image',
                                                'loading' => 'lazy'
                                            ]); ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Документи -->
                    <?php
                    $documents = get_post_meta(get_the_ID(), '_investment_documents', true);
                    if ($documents):
                        $document_ids = explode(',', $documents);
                    ?>
                        <div class="investment-section">
                            <h3 class="section-title"><?php _e('Супровідні документи', 'slavutska-investment'); ?></h3>
                            <div class="investment-documents">
                                <?php foreach ($document_ids as $document_id): 
                                    $file_url = wp_get_attachment_url($document_id);
                                    $file_title = get_the_title($document_id);
                                    $file_size = size_format(filesize(get_attached_file($document_id)));
                                    $file_type = wp_check_filetype($file_url)['ext'];
                                ?>
                                    <div class="document-item">
                                        <div class="document-icon">
                                            <i class="icon-file-<?php echo esc_attr($file_type); ?>" aria-hidden="true"></i>
                                        </div>
                                        <div class="document-info">
                                            <h4 class="document-title"><?php echo esc_html($file_title); ?></h4>
                                            <span class="document-meta"><?php echo esc_html(strtoupper($file_type)); ?> • <?php echo esc_html($file_size); ?></span>
                                        </div>
                                        <a href="<?php echo esc_url($file_url); ?>" 
                                           class="btn btn--outline btn--small document-download"
                                           download
                                           target="_blank">
                                            <i class="icon-download" aria-hidden="true"></i>
                                            <?php _e('Завантажити', 'slavutska-investment'); ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Бічна панель -->
                <aside class="investment-sidebar">
                    <!-- Контактна інформація -->
                    <div class="sidebar-widget contact-widget">
                        <h3 class="widget-title"><?php _e('Контактна інформація', 'slavutska-investment'); ?></h3>
                        
                        <div class="contact-card">
                            <?php if ($contact_person): ?>
                                <div class="contact-item">
                                    <i class="icon-user" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Контактна особа:', 'slavutska-investment'); ?></span>
                                        <span class="contact-value"><?php echo esc_html($contact_person); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($contact_phone): ?>
                                <div class="contact-item">
                                    <i class="icon-phone" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Телефон:', 'slavutska-investment'); ?></span>
                                        <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $contact_phone)); ?>" 
                                           class="contact-value contact-link">
                                            <?php echo esc_html($contact_phone); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($contact_email): ?>
                                <div class="contact-item">
                                    <i class="icon-email" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Email:', 'slavutska-investment'); ?></span>
                                        <a href="mailto:<?php echo esc_attr($contact_email); ?>" 
                                           class="contact-value contact-link">
                                            <?php echo esc_html($contact_email); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="contact-actions">
                                <a href="#contact-form" 
                                   class="btn btn--primary btn--block"
                                   data-scroll-to="contact-form">
                                    <i class="icon-message-circle" aria-hidden="true"></i>
                                    <?php _e('Написати повідомлення', 'slavutska-investment'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Швидкі факти -->
                    <div class="sidebar-widget quick-facts-widget">
                        <h3 class="widget-title"><?php _e('Швидкі факти', 'slavutska-investment'); ?></h3>
                        
                        <div class="quick-facts">
                            <div class="fact-item">
                                <span class="fact-label"><?php _e('Дата публікації:', 'slavutska-investment'); ?></span>
                                <span class="fact-value"><?php echo get_the_date('d.m.Y'); ?></span>
                            </div>
                            
                            <div class="fact-item">
                                <span class="fact-label"><?php _e('Останнє оновлення:', 'slavutska-investment'); ?></span>
                                <span class="fact-value"><?php echo get_the_modified_date('d.m.Y'); ?></span>
                            </div>
                            
                            <?php if ($categories && !is_wp_error($categories)): ?>
                                <div class="fact-item">
                                    <span class="fact-label"><?php _e('Категорії:', 'slavutska-investment'); ?></span>
                                    <div class="fact-value">
                                        <?php foreach ($categories as $index => $category): ?>
                                            <a href="<?php echo esc_url(get_term_link($category)); ?>" 
                                               class="category-link">
                                                <?php echo esc_html($category->name); ?>
                                            </a>
                                            <?php if ($index < count($categories) - 1) echo ', '; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="fact-item">
                                <span class="fact-label"><?php _e('ID проекту:', 'slavutska-investment'); ?></span>
                                <span class="fact-value">#<?php echo get_the_ID(); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Схожі проекти -->
                    <?php
                    $related_investments = new WP_Query([
                        'post_type' => 'investment',
                        'posts_per_page' => 3,
                        'post__not_in' => [get_the_ID()],
                        'meta_query' => [
                            [
                                'key' => '_is_featured',
                                'value' => '1',
                                'compare' => '='
                            ]
                        ]
                    ]);
                    
                    if ($related_investments->have_posts()):
                    ?>
                        <div class="sidebar-widget related-investments-widget">
                            <h3 class="widget-title"><?php _e('Схожі проекти', 'slavutska-investment'); ?></h3>
                            
                            <div class="related-investments">
                                <?php while ($related_investments->have_posts()): $related_investments->the_post(); ?>
                                    <article class="related-investment">
                                        <div class="related-investment-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if (has_post_thumbnail()): ?>
                                                    <?php the_post_thumbnail('thumbnail', [
                                                        'class' => 'related-thumbnail',
                                                        'alt' => get_the_title()
                                                    ]); ?>
                                                <?php else: ?>
                                                    <div class="placeholder-image">
                                                        <i class="icon-image" aria-hidden="true"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        
                                        <div class="related-investment-content">
                                            <h4 class="related-investment-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            
                                            <?php
                                            $related_amount = get_post_meta(get_the_ID(), '_investment_amount', true);
                                            $related_return = get_post_meta(get_the_ID(), '_expected_return', true);
                                            ?>
                                            
                                            <div class="related-investment-meta">
                                                <?php if ($related_amount): ?>
                                                    <span class="meta-item">
                                                        <?php echo number_format($related_amount, 0, ',', ' '); ?> грн
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if ($related_return): ?>
                                                    <span class="meta-item meta-item--highlight">
                                                        <?php echo esc_html($related_return); ?>%
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </section>
    
    <!-- Контактна форма -->
    <section class="investment-contact-section" id="contact-form">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Зацікавились проектом?', 'slavutska-investment'); ?></h2>
                <p class="section-subtitle"><?php _e('Залиште ваші контактні дані і ми зв\'яжемося з вами', 'slavutska-investment'); ?></p>
            </div>
            
            <div class="contact-form-wrapper">
                <?php echo do_shortcode('[slavutska_contact_form show_title="false"]'); ?>
            </div>
        </div>
    </section>
</article>

<?php endwhile;

get_footer();