<?php
/**
 * Шаблон Hero блоку для головної сторінки
 * 
 * @package SlavutaInvest
 */

// FILE: template-parts/home/hero.php

// Отримуємо дані з ACF
$hero_title = get_field('hero_title', 'option');
$hero_subtitle = get_field('hero_subtitle', 'option');
$hero_background = get_field('hero_background', 'option');
$hero_cta_text = get_field('hero_cta_text', 'option');
$hero_cta_link = get_field('hero_cta_link', 'option');
?>

<section class="hero-section">
    <?php if ($hero_background) : ?>
        <div class="hero-background">
            <picture>
                <?php if (isset($hero_background['sizes'])) : ?>
                    <source media="(max-width: 768px)" 
                            srcset="<?php echo esc_url($hero_background['sizes']['large']); ?>">
                    <source media="(max-width: 1200px)" 
                            srcset="<?php echo esc_url($hero_background['sizes']['1536x1536']); ?>">
                <?php endif; ?>
                <img src="<?php echo esc_url($hero_background['url']); ?>" 
                     alt="<?php echo esc_attr($hero_background['alt']); ?>"
                     loading="eager">
            </picture>
            <div class="hero-overlay"></div>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <div class="hero-content">
            <?php if ($hero_title) : ?>
                <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
            <?php endif; ?>
            
            <?php if ($hero_subtitle) : ?>
                <p class="hero-subtitle"><?php echo wp_kses_post($hero_subtitle); ?></p>
            <?php endif; ?>
            
            <?php if ($hero_cta_text && $hero_cta_link) : ?>
                <div class="hero-actions">
                    <a href="<?php echo esc_url($hero_cta_link['url']); ?>" 
                       class="btn btn-primary btn-lg"
                       <?php echo $hero_cta_link['target'] ? 'target="_blank" rel="noopener"' : ''; ?>>
                        <?php echo esc_html($hero_cta_text); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- Статистика -->
            <div class="hero-stats">
                <?php
                // Підрахунок кількості проєктів та ділянок
                $projects_count = wp_count_posts('invest_project')->publish;
                $lands_count = wp_count_posts('land_plot')->publish;
                
                // Підрахунок загальної суми інвестицій
                $total_investment = 0;
                $investment_query = new WP_Query(array(
                    'post_type' => 'invest_project',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                ));
                
                if ($investment_query->have_posts()) {
                    foreach ($investment_query->posts as $project_id) {
                        $amount = get_field('investment_amount', $project_id);
                        if ($amount) {
                            $total_investment += $amount;
                        }
                    }
                }
                wp_reset_postdata();
                ?>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($projects_count); ?></span>
                        <span class="stat-label"><?php esc_html_e('Інвестиційних проєктів', 'slavuta-invest'); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($lands_count); ?></span>
                        <span class="stat-label"><?php esc_html_e('Земельних ділянок', 'slavuta-invest'); ?></span>
                    </div>
                    <?php if ($total_investment > 0) : ?>
                        <div class="stat-item">
                            <span class="stat-number">
                                <?php echo number_format($total_investment / 1000000, 1, ',', ' '); ?> млн
                            </span>
                            <span class="stat-label"><?php esc_html_e('Грн інвестицій', 'slavuta-invest'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Анімована стрілка вниз -->
    <a href="#projects" class="hero-scroll-down" aria-label="<?php esc_attr_e('Прокрутити вниз', 'slavuta-invest'); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </a>
</section>