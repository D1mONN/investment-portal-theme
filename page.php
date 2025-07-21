<?php
/**
 * Шаблон для сторінок
 *
 * @package SlavutaInvest
 */

get_header();
?>

<main id="main" class="site-main">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <div class="container">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </div>
            </header>

            <div class="entry-content">
                <div class="container">
                    <?php the_content(); ?>
                </div>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();