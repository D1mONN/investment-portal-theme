<?php
/**
 * Футер сайту
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}
?>

    </main><!-- #main -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <!-- Основний футер -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-columns">
                    <!-- Колонка 1: Інформація про громаду -->
                    <div class="footer-column footer-column--about">
                        <div class="footer-logo">
                            <?php
                            $footer_logo = slavutska_get_option('footer_logo');
                            if ($footer_logo):
                                echo slavutska_get_image($footer_logo, 'medium', ['class' => 'footer-logo-img', 'alt' => get_bloginfo('name')]);
                            else:
                            ?>
                                <h3 class="footer-site-title"><?php bloginfo('name'); ?></h3>
                            <?php endif; ?>
                        </div>
                        
                        <div class="footer-description">
                            <?php
                            $footer_description = slavutska_get_option('footer_description', 
                                'Славутська міська територіальна громада - ваш надійний партнер у сфері інвестицій та розвитку бізнесу.'
                            );
                            echo '<p>' . esc_html($footer_description) . '</p>';
                            ?>
                        </div>

                        <!-- Соціальні мережі -->
                        <div class="footer-social">
                            <h4 class="footer-social-title"><?php _e('Слідкуйте за нами:', 'slavutska-investment'); ?></h4>
                            <div class="social-links footer-social-links">
                                <?php
                                $social_links = [
                                    'facebook' => [
                                        'url' => slavutska_get_option('facebook_url'),
                                        'label' => 'Facebook'
                                    ],
                                    'telegram' => [
                                        'url' => slavutska_get_option('telegram_url'),
                                        'label' => 'Telegram'
                                    ],
                                    'youtube' => [
                                        'url' => slavutska_get_option('youtube_url'),
                                        'label' => 'YouTube'
                                    ],
                                    'instagram' => [
                                        'url' => slavutska_get_option('instagram_url'),
                                        'label' => 'Instagram'
                                    ],
                                ];
                                
                                foreach ($social_links as $platform => $data):
                                    if ($data['url']):
                                ?>
                                    <a href="<?php echo esc_url($data['url']); ?>" 
                                       class="social-link social-link--<?php echo esc_attr($platform); ?>"
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       aria-label="<?php printf(__('Відвідати нашу сторінку в %s', 'slavutska-investment'), $data['label']); ?>">
                                        <i class="icon-<?php echo esc_attr($platform); ?>" aria-hidden="true"></i>
                                        <span class="screen-reader-text"><?php echo esc_html($data['label']); ?></span>
                                    </a>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Колонка 2: Навігація -->
                    <div class="footer-column footer-column--navigation">
                        <h4 class="footer-column-title"><?php _e('Навігація', 'slavutska-investment'); ?></h4>
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ]);
                        ?>
                        
                        <!-- Додаткові посилання -->
                        <div class="footer-additional-links">
                            <a href="<?php echo esc_url(home_url('/investments/')); ?>" class="footer-link">
                                <?php _e('Інвестиційні пропозиції', 'slavutska-investment'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/land-plots/')); ?>" class="footer-link">
                                <?php _e('Земельні ділянки', 'slavutska-investment'); ?>
                            </a>
                            <a href="#contact" class="footer-link" data-scroll-to="contact">
                                <?php _e('Контакти', 'slavutska-investment'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Колонка 3: Контактна інформація -->
                    <div class="footer-column footer-column--contacts">
                        <h4 class="footer-column-title"><?php _e('Контактна інформація', 'slavutska-investment'); ?></h4>
                        
                        <div class="footer-contacts">
                            <?php
                            $address = slavutska_get_option('office_address', 'вул. Центральна, 1, м. Славута, Хмельницька область, 30000');
                            $phone = slavutska_get_option('contact_phone', '+380 123 456 789');
                            $email = slavutska_get_option('contact_email', 'info@slavutska.gov.ua');
                            $work_hours = slavutska_get_option('work_hours', 'Пн-Пт: 8:00-17:00');
                            ?>
                            
                            <?php if ($address): ?>
                                <div class="contact-item contact-item--address">
                                    <i class="icon-location" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Адреса:', 'slavutska-investment'); ?></span>
                                        <span class="contact-value"><?php echo esc_html($address); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($phone): ?>
                                <div class="contact-item contact-item--phone">
                                    <i class="icon-phone" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Телефон:', 'slavutska-investment'); ?></span>
                                        <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $phone)); ?>" 
                                           class="contact-value contact-link">
                                            <?php echo esc_html($phone); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($email): ?>
                                <div class="contact-item contact-item--email">
                                    <i class="icon-email" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Email:', 'slavutska-investment'); ?></span>
                                        <a href="mailto:<?php echo esc_attr($email); ?>" 
                                           class="contact-value contact-link">
                                            <?php echo esc_html($email); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($work_hours): ?>
                                <div class="contact-item contact-item--hours">
                                    <i class="icon-clock" aria-hidden="true"></i>
                                    <div class="contact-content">
                                        <span class="contact-label"><?php _e('Години роботи:', 'slavutska-investment'); ?></span>
                                        <span class="contact-value"><?php echo esc_html($work_hours); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Колонка 4: Віджети -->
                    <?php if (is_active_sidebar('footer-widgets')): ?>
                        <div class="footer-column footer-column--widgets">
                            <?php dynamic_sidebar('footer-widgets'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Нижній футер -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">
                        <p>
                            &copy; <?php echo date('Y'); ?> 
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                            <?php _e('Усі права захищені.', 'slavutska-investment'); ?>
                        </p>
                    </div>
                    
                    <div class="footer-legal">
                        <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="footer-legal-link">
                            <?php _e('Політика конфіденційності', 'slavutska-investment'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/terms-of-use/')); ?>" class="footer-legal-link">
                            <?php _e('Умови використання', 'slavutska-investment'); ?>
                        </a>
                    </div>
                    
                    <!-- Кнопка "Вгору" -->
                    <button class="back-to-top" 
                            aria-label="<?php _e('Повернутися вгору', 'slavutska-investment'); ?>"
                            title="<?php _e('Повернутися вгору', 'slavutska-investment'); ?>">
                        <i class="icon-arrow-up" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Структуровані дані для організації -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalGovernment",
            "name": "<?php bloginfo('name'); ?>",
            "description": "<?php bloginfo('description'); ?>",
            "url": "<?php echo esc_url(home_url('/')); ?>",
            "telephone": "<?php echo esc_attr($phone); ?>",
            "email": "<?php echo esc_attr($email); ?>",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "<?php echo esc_attr($address); ?>",
                "addressLocality": "Славута",
                "addressRegion": "Хмельницька область",
                "postalCode": "30000",
                "addressCountry": "UA"
            },
            "openingHours": "Mo-Fr 08:00-17:00",
            "areaServed": {
                "@type": "AdministrativeArea",
                "name": "Славутська територіальна громада"
            }
        }
        </script>
    </footer>

</div><!-- #page -->

<?php wp_footer(); ?>

<!-- Cookies notification (GDPR compliance) -->
<div id="cookies-notification" class="cookies-notification" style="display: none;">
    <div class="cookies-content">
        <div class="cookies-text">
            <p><?php _e('Цей сайт використовує файли cookie для покращення користувацького досвіду. Продовжуючи використовувати сайт, ви погоджуєтесь з нашою політикою конфіденційності.', 'slavutska-investment'); ?></p>
        </div>
        <div class="cookies-actions">
            <button id="accept-cookies" class="btn btn--small btn--primary">
                <?php _e('Прийняти', 'slavutska-investment'); ?>
            </button>
            <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="cookies-link">
                <?php _e('Детальніше', 'slavutska-investment'); ?>
            </a>
        </div>
    </div>
</div>

</body>
</html>