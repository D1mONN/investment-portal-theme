<?php
/**
 * Головний шаблон-заглушка
 *
 * @package SlavutaInvest
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php single_post_title(); ?></h1>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="posts-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'template-parts/content', get_post_type() ); ?>
                <?php endwhile; ?>
            </div>
            <?php slavuta_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'На жаль, нічого не знайдено.', 'slavuta-invest' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();