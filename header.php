<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Перейти до вмісту', 'slavuta-invest' ); ?></a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="site-branding">
                    <?php if ( has_custom_logo() ) :
                        the_custom_logo();
                    else : ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-title"><?php bloginfo( 'name' ); ?></a>
                    <?php endif; ?>
                </div>

                <nav id="site-navigation" class="main-navigation">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-toggle-icon"><span></span><span></span><span></span></span>
                    </button>
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'header',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                    ) );
                    ?>
                </nav>

                <div class="header-tools">
                    <button class="search-toggle" aria-label="<?php esc_attr_e( 'Пошук', 'slavuta-invest' ); ?>">
                        <svg class="icon-search" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M17.5 17.5L13.87 13.87M15.83 8.33C15.83 12.35 12.52 15.67 8.5 15.67C4.48 15.67 1.17 12.35 1.17 8.33C1.17 4.31 4.48 1 8.5 1C12.52 1 15.83 4.31 15.83 8.33Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                    </div>
            </div>
        </div>
        <div class="header-search">
            <div class="container">
                <?php get_search_form(); ?>
            </div>
        </div>
    </header>