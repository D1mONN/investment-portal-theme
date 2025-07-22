<?php
/**
 * Шаблон результатів пошуку
 *
 * @package SlavutaInvest
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                /* translators: %s: search query. */
                printf( esc_html__( 'Результати пошуку для: %s', 'slavuta-invest' ), '<span>' . get_search_query() . '</span>' );
                ?>
            </h1>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="search-results-list">
                <?php while ( have_posts() ) : the_post(); ?>
                     <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php slavuta_pagination(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'На жаль, за вашим запитом нічого не знайдено. Спробуйте інші ключові слова.', 'slavuta-invest' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();