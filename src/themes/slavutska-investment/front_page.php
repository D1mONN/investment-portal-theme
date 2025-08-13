<?php
/**
 * Головна сторінка - лендинг інвестиційного порталу
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="landing-page">
    <!-- Hero секція -->
    <section class="hero-section" id="hero">
        <div class="hero-background">
            <?php
            $hero_image = slavutska_get_option('hero_background_image');
            if ($hero_image):
                echo slavutska_get_image($hero_image, 'hero-image', ['class' => 'hero-bg-image', 'loading' => 'eager']);
            endif;
            ?>
            <div class="hero-overlay"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        <?php echo slavutska_safe_text(slavutska_get_option('hero_title', 
                            'Інвестиційні можливості <span class="highlight">Славутської громади</span>'
                        )); ?>
                    </h1>
                    
                    <p class="hero-subtitle">
                        <?php echo esc_html(slavutska_get_option('hero_subtitle', 
                            'Відкрийте для себе унікальні можливості для інвестування в розвиток нашої громади. Сприятливий бізнес-клімат, якісна інфраструктура та професійна підтримка.'
                        )); ?>
                    </p>
                    
                    <div class="hero-actions">
                        <a href="#investments" 
                           class="btn btn--primary btn--large hero-cta" 
                           data-scroll-to="investments">
                            <?php _e('Переглянути пропозиції', 'slavutska-investment'); ?>
                        </a>
                        
                        <a href="#contact" 
                           class="btn btn--secondary btn--large hero-cta-secondary" 
                           data-scroll-to="contact">
                            <?php _e('Зв\'язатися з нами', 'slavutska-investment'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="hero-stats">
                    <?php
                    $stats = [
                        [
                            'number' => slavutska_get_option('stat_1_number', '50+'),
                            'label' => slavutska_get_option('stat_1_label', 'Реалізованих проектів'),
                            'icon' => 'icon-chart'
                        ],
                        [
                            'number' => slavutska_get_option('stat_2_number', '2000+'),
                            'label' => slavutska_get_option('stat_2_label', 'Гектарів землі'),
                            'icon' => 'icon-location'
                        ],
                        [
                            'number' => slavutska_get_option('stat_3_number', '25'),
                            'label' => slavutska_get_option('stat_3_label', 'Років досвіду'),
                            'icon' => 'icon-award'
                        ]
                    ];
                    
                    foreach ($stats as $stat):
                    ?>
                        <div class="hero-stat">
                            <div class="hero-stat-icon">
                                <i class="<?php echo esc_attr($stat['icon']); ?>" aria-hidden="true"></i>
                            </div>
                            <div class="hero-stat-content">
                                <span class="hero-stat-number"><?php echo esc_html($stat['number']); ?></span>
                                <span class="hero-stat-label"><?php echo esc_html($stat['label']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div class="hero-scroll-indicator">
            <a href="#about" data-scroll-to="about" aria-label="<?php _e('Прокрутити вниз', 'slavutska-investment'); ?>">
                <i class="icon-arrow-down" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <!-- Про громаду -->
    <section class="about-section section" id="about">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <?php echo esc_html(slavutska_get_option('about_title', 'Про Славутську громаду')); ?>
                </h2>
                <p class="section-subtitle">
                    <?php echo esc_html(slavutska_get_option('about_subtitle', 
                        'Сучасна, динамічна громада з багатими традиціями та великими амбіціями'
                    )); ?>
                </p>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <div class="about-description">
                        <?php echo slavutska_safe_text(slavutska_get_option('about_description', 
                            '<p>Славутська міська територіальна громада розташована в Хмельницькій області і є одним з найперспективніших регіонів для інвестування.</p>
                            <p>Наша громада пропонує унікальне поєднання сприятливого географічного розташування, розвиненої інфраструктури та професійної команди, готової підтримати ваші бізнес-ініціативи.</p>'
                        )); ?>
                    </div>
                    
                    <div class="about-features">
                        <?php
                        $features = [
                            [
                                'icon' => 'icon-shield',
                                'title' => 'Правова підтримка',
                                'description' => 'Повний супровід інвестиційних проектів'
                            ],
                            [
                                'icon' => 'icon-truck',
                                'title' => 'Логістика',
                                'description' => 'Вигідне транспортне сполучення'
                            ],
                            [
                                'icon' => 'icon-users',
                                'title' => 'Кваліфіковані кадри',
                                'description' => 'Освічена робоча сила'
                            ],
                            [
                                'icon' => 'icon-lightbulb',
                                'title' => 'Інновації',
                                'description' => 'Підтримка високотехнологічних проектів'
                            ]
                        ];
                        
                        foreach ($features as $feature):
                        ?>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="<?php echo esc_attr($feature['icon']); ?>" aria-hidden="true"></i>
                                </div>
                                <div class="feature-content">
                                    <h3 class="feature-title"><?php echo esc_html($feature['title']); ?></h3>
                                    <p class="feature-description"><?php echo esc_html($feature['description']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="about-visual">
                    <?php
                    $about_image = slavutska_get_option('about_image');
                    if ($about_image):
                        echo slavutska_get_image($about_image, 'large', [
                            'class' => 'about-image',
                            'alt' => __('Славутська громада', 'slavutska-investment')
                        ]);
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Інвестиційні пропозиції -->
    <section class="investments-section section section--alt" id="investments">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <?php _e('Інвестиційні пропозиції', 'slavutska-investment'); ?>
                </h2>
                <p class="section-subtitle">
                    <?php _e('Обирайте з переліку актуальних та перспективних проектів', 'slavutska-investment'); ?>
                </p>
            </div>
            
            <div class="investments-grid">
                <?php
                // Отримання рекомендованих інвестиційних пропозицій
                $featured_investments = new WP_Query([
                    'post_type' => 'investment',
                    'posts_per_page' => 6,
                    'meta_key' => '_is_featured',
                    'meta_value' => '1',
                    'post_status' => 'publish'
                ]);
                
                if ($featured_investments->have_posts()):
                    while ($featured_investments->have_posts()): $featured_investments->the_post();
                        $investment_amount = get_post_meta(get_the_ID(), '_investment_amount', true);
                        $expected_return = get_post_meta(get_the_ID(), '_expected_return', true);
                        $investment_period = get_post_meta(get_the_ID(), '_investment_period', true);
                ?>
                    <article class="investment-card">
                        <div class="investment-card-image">
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('investment-thumbnail', [
                                        'class' => 'investment-thumbnail',
                                        'alt' => get_the_title()
                                    ]); ?>
                                </a>
                            <?php endif; ?>
                            <div class="investment-card-badge">
                                <?php _e('Рекомендовано', 'slavutska-investment'); ?>
                            </div>
                        </div>
                        
                        <div class="investment-card-content">
                            <h3 class="investment-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="investment-card-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                            </div>
                            
                            <div class="investment-card-meta">
                                <?php if ($investment_amount): ?>
                                    <div class="investment-meta-item">
                                        <span class="meta-label"><?php _e('Сума:', 'slavutska-investment'); ?></span>
                                        <span class="meta-value"><?php echo number_format($investment_amount, 0, ',', ' '); ?> грн</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($expected_return): ?>
                                    <div class="investment-meta-item">
                                        <span class="meta-label"><?php _e('Прибутковість:', 'slavutska-investment'); ?></span>
                                        <span class="meta-value meta-value--highlight"><?php echo esc_html($expected_return); ?>%</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($investment_period): ?>
                                    <div class="investment-meta-item">
                                        <span class="meta-label"><?php _e('Термін:', 'slavutska-investment'); ?></span>
                                        <span class="meta-value"><?php echo esc_html($investment_period); ?> міс.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="investment-card-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--small">
                                    <?php _e('Детальніше', 'slavutska-investment'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                    <div class="no-investments">
                        <p><?php _e('Наразі немає доступних інвестиційних пропозицій.', 'slavutska-investment'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section-actions">
                <a href="<?php echo esc_url(home_url('/investments/')); ?>" class="btn btn--outline btn--large">
                    <?php _e('Переглянути всі пропозиції', 'slavutska-investment'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Земельні ділянки -->
    <section class="land-plots-section section" id="land-plots">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <?php _e('Доступні земельні ділянки', 'slavutska-investment'); ?>
                </h2>
                <p class="section-subtitle">
                    <?php _e('Якісні земельні ресурси для різних типів бізнесу', 'slavutska-investment'); ?>
                </p>
            </div>
            
            <div class="land-plots-grid">
                <?php
                // Отримання земельних ділянок
                $land_plots = new WP_Query([
                    'post_type' => 'land_plot',
                    'posts_per_page' => 4,
                    'post_status' => 'publish'
                ]);
                
                if ($land_plots->have_posts()):
                    while ($land_plots->have_posts()): $land_plots->the_post();
                        $area = get_post_meta(get_the_ID(), '_area', true);
                        $price_per_hectare = get_post_meta(get_the_ID(), '_price_per_hectare', true);
                        $purpose = get_post_meta(get_the_ID(), '_purpose', true);
                ?>
                    <article class="land-plot-card">
                        <div class="land-plot-card-image">
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('land-plot-image', [
                                        'class' => 'land-plot-thumbnail',
                                        'alt' => get_the_title()
                                    ]); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="land-plot-card-content">
                            <h3 class="land-plot-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="land-plot-card-meta">
                                <?php if ($area): ?>
                                    <div class="land-plot-meta-item">
                                        <i class="icon-area" aria-hidden="true"></i>
                                        <span><?php echo esc_html($area); ?> га</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($price_per_hectare): ?>
                                    <div class="land-plot-meta-item">
                                        <i class="icon-money" aria-hidden="true"></i>
                                        <span><?php echo number_format($price_per_hectare, 0, ',', ' '); ?> грн/га</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($purpose): ?>
                                    <div class="land-plot-meta-item">
                                        <i class="icon-tag" aria-hidden="true"></i>
                                        <span><?php echo esc_html($purpose); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="land-plot-card-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--small">
                                    <?php _e('Детальніше', 'slavutska-investment'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            
            <div class="section-actions">
                <a href="<?php echo esc_url(home_url('/land-plots/')); ?>" class="btn btn--outline btn--large">
                    <?php _e('Переглянути всі ділянки', 'slavutska-investment'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Переваги співпраці -->
    <section class="advantages-section section section--alt" id="advantages">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <?php _e('Чому обирають нас?', 'slavutska-investment'); ?>
                </h2>
                <p class="section-subtitle">
                    <?php _e('Ключові переваги співпраці з Славутською громадою', 'slavutska-investment'); ?>
                </p>
            </div>
            
            <div class="advantages-grid">
                <?php
                $advantages = [
                    [
                        'icon' => 'icon-handshake',
                        'title' => 'Партнерський підхід',
                        'description' => 'Індивідуальний підхід до кожного інвестора та довгострокова підтримка проектів'
                    ],
                    [
                        'icon' => 'icon-clock',
                        'title' => 'Швидке оформлення',
                        'description' => 'Мінімальна бюрократія та оперативне вирішення документальних питань'
                    ],
                    [
                        'icon' => 'icon-chart-growth',
                        'title' => 'Високий потенціал',
                        'description' => 'Стабільне економічне зростання та сприятливий інвестиційний клімат'
                    ],
                    [
                        'icon' => 'icon-shield-check',
                        'title' => 'Правові гарантії',
                        'description' => 'Повний правовий захист інвестицій та прозорість усіх процедур'
                    ],
                    [
                        'icon' => 'icon-network',
                        'title' => 'Розвинена інфраструктура',
                        'description' => 'Якісні дороги, комунікації та логістичні можливості'
                    ],
                    [
                        'icon' => 'icon-leaf',
                        'title' => 'Екологічність',
                        'description' => 'Підтримка екологічно чистих та соціально відповідальних проектів'
                    ]
                ];
                
                foreach ($advantages as $advantage):
                ?>
                    <div class="advantage-item">
                        <div class="advantage-icon">
                            <i class="<?php echo esc_attr($advantage['icon']); ?>" aria-hidden="true"></i>
                        </div>
                        <div class="advantage-content">
                            <h3 class="advantage-title"><?php echo esc_html($advantage['title']); ?></h3>
                            <p class="advantage-description"><?php echo esc_html($advantage['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Контактна форма -->
    <section class="contact-section section" id="contact">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <?php _e('Зв\'яжіться з нами', 'slavutska-investment'); ?>
                </h2>
                <p class="section-subtitle">
                    <?php _e('Готові обговорити ваші інвестиційні плани', 'slavutska-investment'); ?>
                </p>
            </div>
            
            <div class="contact-content">
                <div class="contact-form-wrapper">
                    <?php echo do_shortcode('[slavutska_contact_form]'); ?>
                </div>
                
                <div class="contact-info-wrapper">
                    <div class="contact-info-card">
                        <h3 class="contact-info-title"><?php _e('Контактна інформація', 'slavutska-investment'); ?></h3>
                        
                        <div class="contact-info-items">
                            <?php
                            $contacts = [
                                [
                                    'icon' => 'icon-location',
                                    'label' => __('Адреса', 'slavutska-investment'),
                                    'value' => slavutska_get_option('office_address', 'вул. Центральна, 1, м. Славута'),
                                    'link' => false
                                ],
                                [
                                    'icon' => 'icon-phone',
                                    'label' => __('Телефон', 'slavutska-investment'),
                                    'value' => slavutska_get_option('contact_phone', '+380 123 456 789'),
                                    'link' => 'tel:' . str_replace([' ', '-', '(', ')'], '', slavutska_get_option('contact_phone', '+380123456789'))
                                ],
                                [
                                    'icon' => 'icon-email',
                                    'label' => __('Email', 'slavutska-investment'),
                                    'value' => slavutska_get_option('contact_email', 'info@slavutska.gov.ua'),
                                    'link' => 'mailto:' . slavutska_get_option('contact_email', 'info@slavutska.gov.ua')
                                ],
                                [
                                    'icon' => 'icon-clock',
                                    'label' => __('Години роботи', 'slavutska-investment'),
                                    'value' => slavutska_get_option('work_hours', 'Пн-Пт: 8:00-17:00'),
                                    'link' => false
                                ]
                            ];
                            
                            foreach ($contacts as $contact):
                            ?>
                                <div class="contact-info-item">
                                    <div class="contact-info-icon">
                                        <i class="<?php echo esc_attr($contact['icon']); ?>" aria-hidden="true"></i>
                                    </div>
                                    <div class="contact-info-content">
                                        <span class="contact-info-label"><?php echo esc_html($contact['label']); ?>:</span>
                                        <?php if ($contact['link']): ?>
                                            <a href="<?php echo esc_attr($contact['link']); ?>" class="contact-info-value contact-info-link">
                                                <?php echo esc_html($contact['value']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="contact-info-value"><?php echo esc_html($contact['value']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>