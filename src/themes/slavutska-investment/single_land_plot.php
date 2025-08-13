<?php
/**
 * Шаблон для окремої сторінки земельної ділянки
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
    $area = get_post_meta(get_the_ID(), '_area', true);
    $price_per_hectare = get_post_meta(get_the_ID(), '_price_per_hectare', true);
    $cadastral_number = get_post_meta(get_the_ID(), '_cadastral_number', true);
    $purpose = get_post_meta(get_the_ID(), '_purpose', true);
    $infrastructure = get_post_meta(get_the_ID(), '_infrastructure', true);
    $latitude = get_post_meta(get_the_ID(), '_latitude', true);
    $longitude = get_post_meta(get_the_ID(), '_longitude', true);
    $documents = get_post_meta(get_the_ID(), '_documents', true);
    
    // Розрахунок загальної вартості
    $total_price = $area && $price_per_hectare ? $area * $price_per_hectare : null;
    
    // Отримання типів землі
    $land_types = get_the_terms(get_the_ID(), 'land_type');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('land-plot-single'); ?>>
    <!-- Hero секція -->
    <section class="land-plot-hero">
        <div class="container">
            <div class="land-plot-hero-content">
                <div class="land-plot-meta-bar">
                    <div class="land-plot-types">
                        <?php if ($land_types && !is_wp_error($land_types)): ?>
                            <?php foreach ($land_types as $type): ?>
                                <span class="land-type-badge">
                                    <i class="icon-tag" aria-hidden="true"></i>
                                    <?php echo esc_html($type->name); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="land-plot-actions">
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
                        
                        <?php if ($latitude && $longitude): ?>
                            <button class="btn btn--outline btn--small map-button" 
                                    data-lat="<?php echo esc_attr($latitude); ?>"
                                    data-lng="<?php echo esc_attr($longitude); ?>"
                                    data-title="<?php echo esc_attr(get_the_title()); ?>">
                                <i class="icon-map" aria-hidden="true"></i>
                                <?php _e('На карті', 'slavutska-investment'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <header class="land-plot-header">
                    <h1 class="land-plot-title"><?php the_title(); ?></h1>
                    
                    <?php if ($cadastral_number): ?>
                        <div class="cadastral-number">
                            <i class="icon-hash" aria-hidden="true"></i>
                            <span><?php _e('Кадастровий номер:', 'slavutska-investment'); ?> <?php echo esc_html($cadastral_number); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (has_excerpt()): ?>
                        <div class="land-plot-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="land-plot-key-metrics">
                        <?php if ($area): ?>
                            <div class="key-metric key-metric--primary">
                                <div class="metric-icon">
                                    <i class="icon-maximize" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Площа', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo esc_html($area); ?> га</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($price_per_hectare): ?>
                            <div class="key-metric key-metric--primary">
                                <div class="metric-icon">
                                    <i class="icon-dollar-sign" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Ціна за гектар', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo number_format($price_per_hectare, 0, ',', ' '); ?> грн</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($total_price): ?>
                            <div class="key-metric key-metric--highlight">
                                <div class="metric-icon">
                                    <i class="icon-calculator" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Загальна вартість', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo number_format($total_price, 0, ',', ' '); ?> грн</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($purpose): ?>
                            <div class="key-metric">
                                <div class="metric-icon">
                                    <i class="icon-target" aria-hidden="true"></i>
                                </div>
                                <div class="metric-content">
                                    <span class="metric-label"><?php _e('Цільове призначення', 'slavutska-investment'); ?></span>
                                    <span class="metric-value"><?php echo esc_html($purpose); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>
            </div>
        </div>
    </section>

    <!-- Основний контент -->
    <section class="land-plot-content">
        <div class="container">
            <div class="land-plot-layout">
                <div class="land-plot-main">
                    <!-- Галерея зображень -->
                    <?php if (has_post_thumbnail()): ?>
                        <div class="land-plot-gallery">
                            <div class="main-image">
                                <?php the_post_thumbnail('large', [
                                    'class' => 'land-plot-image',
                                    'alt' => get_the_title()
                                ]); ?>
                            </div>
                            
                            <?php
                            // Додаткові зображення з галереї
                            $gallery_images = get_post_meta(get_the_ID(), '_land_plot_gallery', true);
                            if ($gallery_images):
                                $image_ids = explode(',', $gallery_images);
                            ?>
                                <div class="gallery-thumbnails">
                                    <?php foreach (array_slice($image_ids, 0, 5) as $image_id): ?>
                                        <div class="thumbnail-item">
                                            <a href="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'full')); ?>" 
                                               class="thumbnail-link"
                                               data-lightbox="land-plot-gallery">
                                                <?php echo wp_get_attachment_image($image_id, 'thumbnail', false, [
                                                    'class' => 'thumbnail-image',
                                                    'loading' => 'lazy'
                                                ]); ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Опис ділянки -->
                    <div class="land-plot-description">
                        <h2 class="section-title"><?php _e('Опис земельної ділянки', 'slavutska-investment'); ?></h2>
                        <div class="land-plot-text">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    
                    <!-- Інфраструктура -->
                    <?php if ($infrastructure): ?>
                        <div class="land-plot-section">
                            <h3 class="section-title"><?php _e('Інфраструктура', 'slavutska-investment'); ?></h3>
                            <div class="infrastructure-content">
                                <?php echo wp_kses_post(nl2br($infrastructure)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Карта -->
                    <?php if ($latitude && $longitude): ?>
                        <div class="land-plot-section">
                            <h3 class="section-title"><?php _e('Розташування на карті', 'slavutska-investment'); ?></h3>
                            <div class="land-plot-map">
                                <div id="land-plot-map" 
                                     class="map-container"
                                     data-lat="<?php echo esc_attr($latitude); ?>"
                                     data-lng="<?php echo esc_attr($longitude); ?>"
                                     data-title="<?php echo esc_attr(get_the_title()); ?>"
                                     data-address="<?php echo esc_attr($purpose); ?>">
                                    <!-- Карта буде завантажена через JavaScript -->
                                    <div class="map-placeholder">
                                        <i class="icon-map-pin" aria-hidden="true"></i>
                                        <p><?php _e('Завантаження карти...', 'slavutska-investment'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="map-controls">
                                    <button class="btn btn--outline btn--small" id="zoom-in">
                                        <i class="icon-plus" aria-hidden="true"></i>
                                        <?php _e('Збільшити', 'slavutska-investment'); ?>
                                    </button>
                                    <button class="btn btn--outline btn--small" id="zoom-out">
                                        <i class="icon-minus" aria-hidden="true"></i>
                                        <?php _e('Зменшити', 'slavutska-investment'); ?>
                                    </button>
                                    <button class="btn btn--outline btn--small" id="fullscreen-map">
                                        <i class="icon-maximize-2" aria-hidden="true"></i>
                                        <?php _e('На весь екран', 'slavutska-investment'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Калькулятор вартості -->
                    <?php if ($price_per_hectare): ?>
                        <div class="land-plot-section">
                            <h3 class="section-title"><?php _e('Калькулятор вартості', 'slavutska-investment'); ?></h3>
                            <div class="price-calculator">
                                <div class="calculator-inputs">
                                    <div class="input-group">
                                        <label for="calc-area"><?php _e('Площа (га):', 'slavutska-investment'); ?></label>
                                        <input type="number" 
                                               id="calc-area" 
                                               class="form-control" 
                                               value="<?php echo esc_attr($area); ?>"
                                               min="0.01" 
                                               step="0.01"
                                               max="<?php echo esc_attr($area); ?>">
                                    </div>
                                    
                                    <div class="input-group">
                                        <label><?php _e('Ціна за гектар:', 'slavutska-investment'); ?></label>
                                        <div class="price-display">
                                            <?php echo number_format($price_per_hectare, 0, ',', ' '); ?> грн
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="calculator-result">
                                    <div class="result-label"><?php _e('Загальна вартість:', 'slavutska-investment'); ?></div>
                                    <div class="result-value" 
                                         id="calc-result"
                                         data-price="<?php echo esc_attr($price_per_hectare); ?>">
                                        <?php echo number_format($total_price, 0, ',', ' '); ?> грн
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Документи -->
                    <?php if ($documents): ?>
                        <div class="land-plot-section">
                            <h3 class="section-title"><?php _e('Документи', 'slavutska-investment'); ?></h3>
                            <div class="documents-content">
                                <?php echo wp_kses_post(nl2br($documents)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Правові аспекти -->
                    <div class="land-plot-section">
                        <h3 class="section-title"><?php _e('Правові аспекти', 'slavutska-investment'); ?></h3>
                        <div class="legal-info">
                            <div class="legal-item">
                                <h4><?php _e('Форма власності', 'slavutska-investment'); ?></h4>
                                <p><?php _e('Комунальна власність територіальної громади', 'slavutska-investment'); ?></p>
                            </div>
                            
                            <div class="legal-item">
                                <h4><?php _e('Спосіб надання', 'slavutska-investment'); ?></h4>
                                <p><?php _e('Продаж прав оренди або права власності згідно з чинним законодавством України', 'slavutska-investment'); ?></p>
                            </div>
                            
                            <div class="legal-item">
                                <h4><?php _e('Необхідні документи', 'slavutska-investment'); ?></h4>
                                <ul>
                                    <li><?php _e('Заява на участь у конкурсі/аукціоні', 'slavutska-investment'); ?></li>
                                    <li><?php _e('Копія документа, що посвідчує особу', 'slavutska-investment'); ?></li>
                                    <li><?php _e('Довідка про відсутність заборгованості з податків', 'slavutska-investment'); ?></li>
                                    <li><?php _e('Документи, що підтверджують фінансову спроможність', 'slavutska-investment'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Бічна панель -->
                <aside class="land-plot-sidebar">
                    <!-- Основна інформація -->
                    <div class="sidebar-widget info-widget">
                        <h3 class="widget-title"><?php _e('Основна інформація', 'slavutska-investment'); ?></h3>
                        
                        <div class="info-card">
                            <div class="info-grid">
                                <?php if ($area): ?>
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Площа:', 'slavutska-investment'); ?></span>
                                        <span class="info-value"><?php echo esc_html($area); ?> га</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($price_per_hectare): ?>
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Ціна за га:', 'slavutska-investment'); ?></span>
                                        <span class="info-value"><?php echo number_format($price_per_hectare, 0, ',', ' '); ?> грн</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($total_price): ?>
                                    <div class="info-item info-item--highlight">
                                        <span class="info-label"><?php _e('Загальна вартість:', 'slavutska-investment'); ?></span>
                                        <span class="info-value"><?php echo number_format($total_price, 0, ',', ' '); ?> грн</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($cadastral_number): ?>
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Кадастровий №:', 'slavutska-investment'); ?></span>
                                        <span class="info-value"><?php echo esc_html($cadastral_number); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($purpose): ?>
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Призначення:', 'slavutska-investment'); ?></span>
                                        <span class="info-value"><?php echo esc_html($purpose); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="info-actions">
                                <a href="#contact-form" 
                                   class="btn btn--primary btn--block"
                                   data-scroll-to="contact-form">
                                    <i class="icon-message-circle" aria-hidden="true"></i>
                                    <?php _e('Запитати про ділянку', 'slavutska-investment'); ?>
                                </a>
                                
                                <button class="btn btn--outline btn--block request-viewing">
                                    <i class="icon-eye" aria-hidden="true"></i>
                                    <?php _e('Запросити огляд', 'slavutska-investment'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Контактна інформація -->
                    <div class="sidebar-widget contact-widget">
                        <h3 class="widget-title"><?php _e('Контактна інформація', 'slavutska-investment'); ?></h3>
                        
                        <div class="contact-card">
                            <?php
                            $contact_person = slavutska_get_option('land_contact_person', 'Відділ земельних відносин');
                            $contact_phone = slavutska_get_option('land_contact_phone', slavutska_get_option('contact_phone'));
                            $contact_email = slavutska_get_option('land_contact_email', slavutska_get_option('contact_email'));
                            ?>
                            
                            <div class="contact-item">
                                <i class="icon-user" aria-hidden="true"></i>
                                <div class="contact-content">
                                    <span class="contact-label"><?php _e('Відповідальний:', 'slavutska-investment'); ?></span>
                                    <span class="contact-value"><?php echo esc_html($contact_person); ?></span>
                                </div>
                            </div>
                            
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
                        </div>
                    </div>
                    
                    <!-- Швидкі факти -->
                    <div class="sidebar-widget quick-facts-widget">
                        <h3 class="widget-title"><?php _e('Додаткова інформація', 'slavutska-investment'); ?></h3>
                        
                        <div class="quick-facts">
                            <div class="fact-item">
                                <span class="fact-label"><?php _e('Дата публікації:', 'slavutska-investment'); ?></span>
                                <span class="fact-value"><?php echo get_the_date('d.m.Y'); ?></span>
                            </div>
                            
                            <div class="fact-item">
                                <span class="fact-label"><?php _e('Останнє оновлення:', 'slavutska-investment'); ?></span>
                                <span class="fact-value"><?php echo get_the_modified_date('d.m.Y'); ?></span>
                            </div>
                            
                            <?php if ($land_types && !is_wp_error($land_types)): ?>
                                <div class="fact-item">
                                    <span class="fact-label"><?php _e('Тип землі:', 'slavutska-investment'); ?></span>
                                    <div class="fact-value">
                                        <?php foreach ($land_types as $index => $type): ?>
                                            <a href="<?php echo esc_url(get_term_link($type)); ?>" 
                                               class="type-link">
                                                <?php echo esc_html($type->name); ?>
                                            </a>
                                            <?php if ($index < count($land_types) - 1) echo ', '; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="fact-item">
                                <span class="fact-label"><?php _e('ID ділянки:', 'slavutska-investment'); ?></span>
                                <span class="fact-value">#<?php echo get_the_ID(); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Схожі ділянки -->
                    <?php
                    $related_plots = new WP_Query([
                        'post_type' => 'land_plot',
                        'posts_per_page' => 3,
                        'post__not_in' => [get_the_ID()],
                        'orderby' => 'rand'
                    ]);
                    
                    if ($related_plots->have_posts()):
                    ?>
                        <div class="sidebar-widget related-plots-widget">
                            <h3 class="widget-title"><?php _e('Інші ділянки', 'slavutska-investment'); ?></h3>
                            
                            <div class="related-plots">
                                <?php while ($related_plots->have_posts()): $related_plots->the_post(); ?>
                                    <article class="related-plot">
                                        <div class="related-plot-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if (has_post_thumbnail()): ?>
                                                    <?php the_post_thumbnail('thumbnail', [
                                                        'class' => 'related-thumbnail',
                                                        'alt' => get_the_title()
                                                    ]); ?>
                                                <?php else: ?>
                                                    <div class="placeholder-image">
                                                        <i class="icon-map" aria-hidden="true"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        
                                        <div class="related-plot-content">
                                            <h4 class="related-plot-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            
                                            <?php
                                            $related_area = get_post_meta(get_the_ID(), '_area', true);
                                            $related_price = get_post_meta(get_the_ID(), '_price_per_hectare', true);
                                            ?>
                                            
                                            <div class="related-plot-meta">
                                                <?php if ($related_area): ?>
                                                    <span class="meta-item">
                                                        <i class="icon-maximize" aria-hidden="true"></i>
                                                        <?php echo esc_html($related_area); ?> га
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if ($related_price): ?>
                                                    <span class="meta-item">
                                                        <i class="icon-dollar-sign" aria-hidden="true"></i>
                                                        <?php echo number_format($related_price, 0, ',', ' '); ?> грн/га
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
    <section class="land-plot-contact-section" id="contact-form">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Зацікавились земельною ділянкою?', 'slavutska-investment'); ?></h2>
                <p class="section-subtitle"><?php _e('Залиште заявку і ми надамо детальну консультацію', 'slavutska-investment'); ?></p>
            </div>
            
            <div class="contact-form-wrapper">
                <?php echo do_shortcode('[slavutska_contact_form show_title="false"]'); ?>
            </div>
        </div>
    </section>
</article>

<script>
// Калькулятор вартості
document.addEventListener('DOMContentLoaded', function() {
    const areaInput = document.getElementById('calc-area');
    const resultElement = document.getElementById('calc-result');
    
    if (areaInput && resultElement) {
        const pricePerHectare = parseFloat(resultElement.dataset.price);
        
        areaInput.addEventListener('input', function() {
            const area = parseFloat(this.value) || 0;
            const totalPrice = area * pricePerHectare;
            resultElement.textContent = new Intl.NumberFormat('uk-UA').format(totalPrice) + ' грн';
        });
    }
});
</script>

<?php endwhile;

get_footer();