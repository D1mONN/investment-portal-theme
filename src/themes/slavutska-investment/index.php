<?php
/**
 * Головний файл теми (fallback)
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

<div class="site-content">
    <div class="container">
        <div class="content-layout">
            <main class="main-content">
                <?php if (have_posts()): ?>
                    
                    <header class="page-header">
                        <?php if (is_home() && !is_front_page()): ?>
                            <h1 class="page-title"><?php single_post_title(); ?></h1>
                        <?php elseif (is_archive()): ?>
                            <h1 class="page-title">
                                <?php
                                if (is_category()) {
                                    single_cat_title();
                                } elseif (is_tag()) {
                                    single_tag_title();
                                } elseif (is_author()) {
                                    printf(__('Автор: %s', 'slavutska-investment'), get_the_author());
                                } elseif (is_day()) {
                                    printf(__('День: %s', 'slavutska-investment'), get_the_date());
                                } elseif (is_month()) {
                                    printf(__('Місяць: %s', 'slavutska-investment'), get_the_date('F Y'));
                                } elseif (is_year()) {
                                    printf(__('Рік: %s', 'slavutska-investment'), get_the_date('Y'));
                                } else {
                                    _e('Архіви', 'slavutska-investment');
                                }
                                ?>
                            </h1>
                            
                            <?php if (is_category() || is_tag()) : ?>
                                <div class="archive-description">
                                    <?php echo term_description(); ?>
                                </div>
                            <?php endif; ?>
                            
                        <?php elseif (is_search()): ?>
                            <h1 class="page-title">
                                <?php printf(__('Результати пошуку для: %s', 'slavutska-investment'), '<span>' . get_search_query() . '</span>'); ?>
                            </h1>
                        <?php endif; ?>
                    </header>

                    <div class="posts-container">
                        <?php while (have_posts()): the_post(); ?>
                            
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                                
                                <?php if (has_post_thumbnail()): ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', [
                                                'alt' => get_the_title(),
                                                'loading' => 'lazy'
                                            ]); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
                                    <header class="post-header">
                                        <h2 class="post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="post-meta">
                                            <span class="post-date">
                                                <i class="icon-calendar" aria-hidden="true"></i>
                                                <time datetime="<?php echo get_the_date('c'); ?>">
                                                    <?php echo get_the_date(); ?>
                                                </time>
                                            </span>
                                            
                                            <?php if (get_post_type() === 'post'): ?>
                                                <span class="post-author">
                                                    <i class="icon-user" aria-hidden="true"></i>
                                                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                        <?php the_author(); ?>
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (get_the_category_list()): ?>
                                                <span class="post-categories">
                                                    <i class="icon-folder" aria-hidden="true"></i>
                                                    <?php the_category(', '); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </header>
                                    
                                    <div class="post-excerpt">
                                        <?php
                                        if (has_excerpt()) {
                                            the_excerpt();
                                        } else {
                                            echo wp_trim_words(get_the_content(), 30, '...');
                                        }
                                        ?>
                                    </div>
                                    
                                    <footer class="post-footer">
                                        <a href="<?php the_permalink(); ?>" class="btn btn--outline btn--small">
                                            <?php _e('Читати далі', 'slavutska-investment'); ?>
                                            <i class="icon-arrow-right" aria-hidden="true"></i>
                                        </a>
                                        
                                        <?php if (get_the_tags()): ?>
                                            <div class="post-tags">
                                                <i class="icon-tag" aria-hidden="true"></i>
                                                <?php the_tags('', ', '); ?>
                                            </div>
                                        <?php endif; ?>
                                    </footer>
                                </div>
                            </article>
                            
                        <?php endwhile; ?>
                    </div>

                    <!-- Пагінація -->
                    <div class="pagination-wrapper">
                        <?php
                        $pagination = paginate_links([
                            'prev_text' => '<i class="icon-arrow-left" aria-hidden="true"></i> ' . __('Попередня', 'slavutska-investment'),
                            'next_text' => __('Наступна', 'slavutska-investment') . ' <i class="icon-arrow-right" aria-hidden="true"></i>',
                            'before_page_number' => '<span class="screen-reader-text">' . __('Сторінка', 'slavutska-investment') . ' </span>',
                            'type' => 'array'
                        ]);
                        
                        if ($pagination):
                        ?>
                            <nav class="pagination-nav" aria-label="<?php esc_attr_e('Навігація по сторінках', 'slavutska-investment'); ?>">
                                <ul class="pagination-list">
                                    <?php foreach ($pagination as $page): ?>
                                        <li class="pagination-item">
                                            <?php echo $page; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="icon-search" aria-hidden="true"></i>
                        </div>
                        
                        <h2 class="no-results-title">
                            <?php
                            if (is_search()) {
                                _e('Нічого не знайдено', 'slavutska-investment');
                            } else {
                                _e('Контент відсутній', 'slavutska-investment');
                            }
                            ?>
                        </h2>
                        
                        <p class="no-results-text">
                            <?php
                            if (is_search()) {
                                printf(__('На жаль, за запитом "%s" нічого не знайдено. Спробуйте інші ключові слова.', 'slavutska-investment'), get_search_query());
                            } else {
                                _e('Вибачте, але контент для цієї сторінки поки що відсутній.', 'slavutska-investment');
                            }
                            ?>
                        </p>
                        
                        <div class="no-results-actions">
                            <?php if (is_search()): ?>
                                <div class="search-form-wrapper">
                                    <?php get_search_form(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary">
                                <?php _e('На головну', 'slavutska-investment'); ?>
                            </a>
                        </div>
                    </div>
                    
                <?php endif; ?>
            </main>

            <?php if (is_active_sidebar('primary-sidebar')): ?>
                <aside class="sidebar">
                    <?php dynamic_sidebar('primary-sidebar'); ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer();