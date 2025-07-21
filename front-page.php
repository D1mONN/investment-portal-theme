<?php
/**
 * Шаблон головної сторінки
 *
 * @package SlavutaInvest
 */

get_header();
?>

<main id="main" class="site-main">

    <?php
    // Hero блок
    get_template_part( 'template-parts/home/hero' );

    // Слайдер з інвестиційними проєктами
    get_template_part( 'template-parts/home/projects-slider' );

    // Блок з земельними ділянками
    get_template_part( 'template-parts/home/land-plots' );

    // Блок з останніми новинами
    get_template_part( 'template-parts/home/latest-news' );
    ?>

</main>

<?php
get_footer();