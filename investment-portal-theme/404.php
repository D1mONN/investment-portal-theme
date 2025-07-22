<?php
/**
 * Шаблон сторінки 404 (Не знайдено)
 *
 * @package SlavutaInvest
 */

get_header();
?>

<main id="main" class="site-main">
    <section class="error-404 not-found">
        <div class="container">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( '404', 'slavuta-invest' ); ?></h1>
                <h2><?php esc_html_e( 'Сторінку не знайдено', 'slavuta-invest' ); ?></h2>
            </header>

            <div class="page-content">
                <p><?php esc_html_e( 'Схоже, за цією адресою нічого немає. Спробуйте скористатися пошуком або повернутися на головну.', 'slavuta-invest' ); ?></p>
                <?php get_search_form(); ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'На головну', 'slavuta-invest' ); ?></a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();