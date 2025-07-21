<?php
/**
 * Шаблон підвалу сайту
 * 
 * @package SlavutaInvest
 */

// FILE: footer.php
?>

    <footer id="colophon" class="site-footer">
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    
                    <!-- Інформація про громаду -->
                    <div class="footer-column footer-about">
                        <h3 class="footer-title"><?php esc_html_e('Славутська міська територіальна громада', 'slavuta-invest'); ?></h3>
                        <p class="footer-description">
                            <?php esc_html_e('Інвестиційний портал створено для залучення інвестицій та розвитку економічного потенціалу громади.', 'slavuta-invest'); ?>
                        </p>
                        
                        <?php
                        // Соціальні мережі
                        if (have_rows('social_networks', 'option')) :
                        ?>
                            <div class="social-links">
                                <?php while (have_rows('social_networks', 'option')) : the_row(); ?>
                                    <a href="<?php echo esc_url(get_sub_field('url')); ?>" 
                                       class="social-link social-<?php echo esc_attr(get_sub_field('name')); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       aria-label="<?php echo esc_attr(get_sub_field('name')); ?>">
                                        <?php echo slavuta_get_social_icon(get_sub_field('name')); ?>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Швидкі посилання -->
                    <div class="footer-column footer-links">
                        <h3 class="footer-title"><?php esc_html_e('Швидкі посилання', 'slavuta-invest'); ?></h3>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </div>
                    
                    <!-- Контактна інформація -->
                    <div class="footer-column footer-contacts">
                        <h3 class="footer-title"><?php esc_html_e('Контакти', 'slavuta-invest'); ?></h3>
                        
                        <?php if (get_field('footer_address', 'option')) : ?>
                            <div class="contact-item">
                                <svg class="contact-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M8 1C5.24 1 3 3.24 3 6C3 9.5 8 15 8 15C8 15 13 9.5 13 6C13 3.24 10.76 1 8 1ZM8 8C6.9 8 6 7.1 6 6C6 4.9 6.9 4 8 4C9.1 4 10 4.9 10 6C10 7.1 9.1 8 8 8Z" fill="currentColor"/>
                                </svg>
                                <span><?php the_field('footer_address', 'option'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (get_field('footer_phone', 'option')) : ?>
                            <div class="contact-item">
                                <svg class="contact-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M13.5 10.34C12.55 10.34 11.63 10.17 10.78 9.86C10.51 9.77 10.21 9.84 10 10.05L8.62 11.89C6.49 10.83 4.17 8.51 3.11 6.38L4.95 5C5.16 4.79 5.23 4.49 5.14 4.22C4.83 3.37 4.66 2.45 4.66 1.5C4.66 1.05 4.29 0.68 3.84 0.68H1.5C1.05 0.68 0.68 1.05 0.68 1.5C0.68 9.23 6.77 15.32 14.5 15.32C14.95 15.32 15.32 14.95 15.32 14.5V12.16C15.32 11.71 14.95 11.34 14.5 11.34C14.17 11.34 13.84 11.31 13.5 11.34V10.34Z" fill="currentColor"/>
                                </svg>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', get_field('footer_phone', 'option'))); ?>">
                                    <?php the_field('footer_phone', 'option'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (get_field('footer_email', 'option')) : ?>
                            <div class="contact-item">
                                <svg class="contact-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M14 3H2C1.45 3 1 3.45 1 4V12C1 12.55 1.45 13 2 13H14C14.55 13 15 12.55 15 12V4C15 3.45 14.55 3 14 3ZM14 5L8 8.5L2 5V4L8 7.5L14 4V5Z" fill="currentColor"/>
                                </svg>
                                <a href="mailto:<?php echo esc_attr(get_field('footer_email', 'option')); ?>">
                                    <?php the_field('footer_email', 'option'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Форма підписки -->
                    <div class="footer-column footer-newsletter">
                        <h3 class="footer-title"><?php esc_html_e('Підписка на новини', 'slavuta-invest'); ?></h3>
                        <p class="newsletter-description">
                            <?php esc_html_e('Отримуйте актуальну інформацію про інвестиційні можливості', 'slavuta-invest'); ?>
                        </p>
                        
                        <?php
                        // Fluent Forms integration
                        if (function_exists('fluentform_render_form')) {
                            // Замініть 1 на ID вашої форми підписки
                            echo fluentform_render_form(array(
                                'id' => 1,
                                'show_title' => false,
                                'show_description' => false
                            ));
                        } else {
                            // Fallback форма
                            ?>
                            <form class="newsletter-form" action="#" method="post">
                                <input type="email" 
                                       name="email" 
                                       placeholder="<?php esc_attr_e('Ваш email', 'slavuta-invest'); ?>" 
                                       required>
                                <button type="submit">
                                    <?php esc_html_e('Підписатися', 'slavuta-invest'); ?>
                                </button>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        <?php
                        printf(
                            esc_html__('© %1$s %2$s. Всі права захищені.', 'slavuta-invest'),
                            date('Y'),
                            get_bloginfo('name')
                        );
                        ?>
                    </div>
                    <div class="footer-credits">
                        <?php
                        printf(
                            esc_html__('Розроблено з %s для громади', 'slavuta-invest'),
                            '<span class="heart">♥</span>'
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

<?php
/**
 * Функція для отримання SVG іконки соціальної мережі
 */
function slavuta_get_social_icon($network) {
    $icons = array(
        'facebook' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.656 9.128 8.438 9.879V12.89h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.989C16.344 19.129 20 14.99 20 10z" fill="currentColor"/></svg>',
        'instagram' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 1.802c2.67 0 2.987.01 4.042.058 1.101.05 1.856.24 2.284.408.466.18.895.42 1.303.828.408.408.648.837.828 1.303.168.428.358 1.183.408 2.284.048 1.055.058 1.372.058 4.042s-.01 2.987-.058 4.042c-.05 1.101-.24 1.856-.408 2.284a3.487 3.487 0 01-.828 1.303 3.487 3.487 0 01-1.303.828c-.428.168-1.183.358-2.284.408-1.055.048-1.372.058-4.042.058s-2.987-.01-4.042-.058c-1.101-.05-1.856-.24-2.284-.408a3.487 3.487 0 01-1.303-.828 3.487 3.487 0 01-.828-1.303c-.168-.428-.358-1.183-.408-2.284C1.812 12.987 1.802 12.67 1.802 10s.01-2.987.058-4.042c.05-1.101.24-1.856.408-2.284.18-.466.42-.895.828-1.303A3.487 3.487 0 014.399 1.543c.428-.168 1.183-.358 2.284-.408C7.738 1.087 8.055 1.077 10.725 1.077L10 1.802zm0-1.802C7.284 0 6.944.012 5.877.06 4.813.11 4.086.278 3.45.525a5.289 5.289 0 00-1.925 1.255A5.289 5.289 0 00.27 3.705C.023 4.341-.145 5.068-.195 6.132-.243 7.199-.255 7.539-.255 10.255s.012 3.056.06 4.123c.05 1.064.218 1.791.465 2.427a5.29 5.29 0 001.255 1.925 5.29 5.29 0 001.925 1.255c.636.247 1.363.415 2.427.465 1.067.048 1.407.06 4.123.06s3.056-.012 4.123-.06c1.064-.05 1.791-.218 2.427-.465a5.29 5.29 0 001.925-1.255 5.29 5.29 0 001.255-1.925c.247-.636.415-1.363.465-2.427.048-1.067.06-1.407.06-4.123s-.012-3.056-.06-4.123c-.05-1.064-.218-1.791-.465-2.427a5.29 5.29 0 00-1.255-1.925A5.29 5.29 0 0016.58.525C15.944.278 15.217.11 14.153.06 13.086.012 12.746 0 10.03 0L10 0z" fill="currentColor"/><path d="M10 4.865a5.135 5.135 0 100 10.27 5.135 5.135 0 000-10.27zm0 8.468a3.333 3.333 0 110-6.666 3.333 3.333 0 010 6.666z" fill="currentColor"/><circle cx="15.338" cy="4.662" r="1.2" fill="currentColor"/></svg>',
        'youtube' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M19.15 5.35s-.2-1.35-.75-1.95c-.7-.75-1.5-.75-1.85-.8C13.95 2.45 10 2.45 10 2.45s-3.95 0-6.55.15c-.4.05-1.15.05-1.85.8-.55.6-.75 1.95-.75 1.95S.65 7.05.65 8.75v1.6c0 1.7.2 3.4.2 3.4s.2 1.35.75 1.95c.7.75 1.65.7 2.05.8 1.5.15 6.35.2 6.35.2s3.95 0 6.55-.2c.4-.05 1.15-.05 1.85-.8.55-.6.75-1.95.75-1.95s.2-1.7.2-3.4v-1.6c0-1.7-.2-3.4-.2-3.4zM7.95 12.45V6.55L13.2 9.5l-5.25 2.95z" fill="currentColor"/></svg>',
        'telegram' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.89 6.82l-1.64 7.747c-.123.554-.447.688-.905.428l-2.5-1.842-1.206 1.16c-.133.133-.245.245-.503.245l.18-2.548 4.628-4.181c.201-.18-.044-.279-.312-.099L7.21 11.15 4.744 10.4c-.536-.168-.546-.536.112-.792l9.632-3.713c.447-.16.838.109.693.775z" fill="currentColor"/></svg>',
        'linkedin' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18.52 0H1.477C.66 0 0 .645 0 1.441v17.114C0 19.352.66 20 1.477 20H18.52c.816 0 1.48-.648 1.48-1.445V1.441C20 .645 19.336 0 18.52 0zM5.932 17.043H2.968V7.496h2.964v9.547zM4.45 6.195a1.72 1.72 0 110-3.44 1.72 1.72 0 010 3.44zm12.593 10.848h-2.963v-4.64c0-1.106-.02-2.53-1.54-2.53-1.544 0-1.78 1.204-1.78 2.449v4.72H7.797V7.497h2.844v1.305h.04c.396-.75 1.363-1.54 2.804-1.54 3 0 3.556 1.975 3.556 4.546v5.235z" fill="currentColor"/></svg>',
        'twitter' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M17.944 4.987c.013.176.013.352.013.528 0 5.368-4.086 11.553-11.553 11.553-2.3 0-4.438-.668-6.237-1.82.327.04.642.053.983.053a8.152 8.152 0 005.046-1.735 4.078 4.078 0 01-3.805-2.822c.25.04.502.066.766.066.365 0 .73-.053 1.07-.14a4.072 4.072 0 01-3.264-3.991v-.053c.541.302 1.172.49 1.834.515a4.063 4.063 0 01-1.81-3.389c0-.755.2-1.447.553-2.047a11.564 11.564 0 008.402 4.258 4.595 4.595 0 01-.1-.932 4.07 4.07 0 014.07-4.07c1.172 0 2.23.49 2.973 1.283A8.017 8.017 0 0019.47.74a4.053 4.053 0 01-1.785 2.233 8.158 8.158 0 002.334-.628 8.751 8.751 0 01-2.046 2.105l.021.037z" fill="currentColor"/></svg>',
    );
    
    return isset($icons[$network]) ? $icons[$network] : '';
}