<?php
/**
 * Шапка сайту
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO мета-теги -->
    <meta name="description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
    <meta name="keywords" content="<?php echo esc_attr(slavutska_get_option('seo_keywords', 'інвестиції, Славута, земельні ділянки, бізнес можливості')); ?>">
    <meta name="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    
    <!-- Open Graph теги -->
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); ?>">
    <meta property="og:description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(home_url('/')); ?>">
    <meta property="og:image" content="<?php echo esc_url(slavutska_get_option('og_image', SLAVUTSKA_THEME_URI . '/assets/images/og-image.jpg')); ?>">
    
    <!-- Безпека -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url(slavutska_get_option('favicon', SLAVUTSKA_THEME_URI . '/assets/images/favicon.ico')); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url(slavutska_get_option('apple_touch_icon', SLAVUTSKA_THEME_URI . '/assets/images/apple-touch-icon.png')); ?>">
    
    <?php wp_head(); ?>
    
    <!-- Структуровані дані -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "GovernmentOrganization",
        "name": "<?php bloginfo('name'); ?>",
        "description": "<?php bloginfo('description'); ?>",
        "url": "<?php echo esc_url(home_url('/')); ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Славута",
            "addressRegion": "Хмельницька область",
            "addressCountry": "UA"
        },
        "areaServed": "Славутська громада"
    }
    </script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip link для доступності -->
<a class="skip-link screen-reader-text" href="#main"><?php _e('Перейти до основного контенту', 'slavutska-investment'); ?></a>

<div id="page" class="site">
    <header id="masthead" class="site-header" role="banner">
        <!-- Верхня панель з контактами -->
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="contact-info">
                        <?php
                        $phone = slavutska_get_option('contact_phone', '+380 123 456 789');
                        $email = slavutska_get_option('contact_email', 'info@slavutska.gov.ua');
                        ?>
                        <?php if ($phone): ?>
                            <span class="contact-item">
                                <i class="icon-phone" aria-hidden="true"></i>
                                <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $phone)); ?>" 
                                   aria-label="<?php _e('Зателефонувати', 'slavutska-investment'); ?>">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($email): ?>
                            <span class="contact-item">
                                <i class="icon-email" aria-hidden="true"></i>
                                <a href="mailto:<?php echo esc_attr($email); ?>" 
                                   aria-label="<?php _e('Написати email', 'slavutska-investment'); ?>">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="social-links">
                        <?php
                        $social_links = [
                            'facebook' => slavutska_get_option('facebook_url'),
                            'telegram' => slavutska_get_option('telegram_url'),
                            'youtube' => slavutska_get_option('youtube_url'),
                        ];
                        
                        foreach ($social_links as $platform => $url):
                            if ($url):
                        ?>
                            <a href="<?php echo esc_url($url); ?>" 
                               class="social-link social-link--<?php echo esc_attr($platform); ?>"
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php printf(__('Відвідати нашу сторінку в %s', 'slavutska-investment'), ucfirst($platform)); ?>">
                                <i class="icon-<?php echo esc_attr($platform); ?>" aria-hidden="true"></i>
                            </a>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Основна навігація -->
        <div class="header-main">
            <div class="container">
                <div class="site-branding">
                    <?php
                    $custom_logo = slavutska_get_option('custom_logo');
                    if ($custom_logo):
                    ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link" rel="home">
                            <?php echo slavutska_get_image($custom_logo, 'full', ['class' => 'custom-logo', 'alt' => get_bloginfo('name')]); ?>
                        </a>
                    <?php else: ?>
                        <div class="site-title-wrapper">
                            <h1 class="site-title">
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                    <?php bloginfo('name'); ?>
                                </a>
                            </h1>
                            <?php
                            $description = get_bloginfo('description', 'display');
                            if ($description || is_customize_preview()):
                            ?>
                                <p class="site-description"><?php echo esc_html($description); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php _e('Головне меню', 'slavutska-investment'); ?>">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-toggle-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="menu-toggle-text"><?php _e('Меню', 'slavutska-investment'); ?></span>
                    </button>
                    
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'primary-menu',
                        'container'      => false,
                        'depth'          => 2,
                        'fallback_cb'    => 'slavutska_fallback_menu',
                        'walker'         => new Slavutska_Walker_Nav_Menu(),
                    ]);
                    ?>
                    
                    <div class="header-cta">
                        <a href="#contact" 
                           class="btn btn--primary btn--header-cta"
                           data-scroll-to="contact">
                            <?php _e('Зв\'язатися з нами', 'slavutska-investment'); ?>
                        </a>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Breadcrumbs для внутрішніх сторінок -->
        <?php if (!is_front_page()): ?>
            <div class="breadcrumbs-wrapper">
                <div class="container">
                    <?php slavutska_breadcrumbs(); ?>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <main id="main" class="site-main" role="main">
