<?php
/**
 * Шаблон архіву інвестиційних проєктів
 * 
 * @package SlavutaInvest
 */

// FILE: archive-invest_project.php

get_header();
?>

<main id="main" class="site-main">
    <section class="archive-section">
        <div class="container">
            <!-- Заголовок сторінки -->
            <header class="archive-header">
                <h1 class="archive-title"><?php esc_html_e('Інвестиційні проєкти', 'slavuta-invest'); ?></h1>
                <p class="archive-description">
                    <?php esc_html_e('Перспективні проєкти для інвестування та розвитку Славутської громади', 'slavuta-invest'); ?>
                </p>
            </header>
            
            <!-- Фільтри -->
            <div class="archive-filters">
                <form class="filters-form" method="get" action="<?php echo esc_url(get_post_type_archive_link('invest_project')); ?>">
                    <div class="filters-row">
                        <!-- Фільтр за галуззю -->
                        <div class="filter-item">
                            <label for="project_category" class="filter-label">
                                <?php esc_html_e('Галузь', 'slavuta-invest'); ?>
                            </label>
                            <?php
                            $current_category = isset($_GET['project_category']) ? sanitize_text_field($_GET['project_category']) : '';
                            $categories = get_terms(array(
                                'taxonomy' => 'project_category',
                                'hide_empty' => true,
                            ));
                            
                            if ($categories && !is_wp_error($categories)) :
                            ?>
                                <select name="project_category" id="project_category" class="filter-select">
                                    <option value=""><?php esc_html_e('Всі галузі', 'slavuta-invest'); ?></option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?php echo esc_attr($category->slug); ?>" 
                                                <?php selected($current_category, $category->slug); ?>>
                                            <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Фільтр за статусом -->
                        <div class="filter-item">
                            <label for="project_status" class="filter-label">
                                <?php esc_html_e('Статус проєкту', 'slavuta-invest'); ?>
                            </label>
                            <?php
                            $current_status = isset($_GET['project_status']) ? sanitize_text_field($_GET['project_status']) : '';
                            ?>
                            <select name="project_status" id="project_status" class="filter-select">
                                <option value=""><?php esc_html_e('Всі статуси', 'slavuta-invest'); ?></option>
                                <option value="idea" <?php selected($current_status, 'idea'); ?>>
                                    <?php esc_html_e('Ідея', 'slavuta-invest'); ?>
                                </option>
                                <option value="development" <?php selected($current_status, 'development'); ?>>
                                    <?php esc_html_e('В розробці', 'slavuta-invest'); ?>
                                </option>
                                <option value="implemented" <?php selected($current_status, 'implemented'); ?>>
                                    <?php esc_html_e('Реалізовано', 'slavuta-invest'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Сортування -->
                        <div class="filter-item">
                            <label for="orderby" class="filter-label">
                                <?php esc_html_e('Сортування', 'slavuta-invest'); ?>
                            </label>
                            <?php
                            $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
                            ?>
                            <select name="orderby" id="orderby" class="filter-select">
                                <option value="date" <?php selected($current_orderby, 'date'); ?>>
                                    <?php esc_html_e('За датою', 'slavuta-invest'); ?>
                                </option>
                                <option value="title" <?php selected($current_orderby, 'title'); ?>>
                                    <?php esc_html_e('За назвою', 'slavuta-invest'); ?>
                                </option>
                                <option value="investment" <?php selected($current_orderby, 'investment'); ?>>
                                    <?php esc_html_e('За сумою інвестицій', 'slavuta-invest'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <?php esc_html_e('Застосувати', 'slavuta-invest'); ?>
                            </button>
                            <a href="<?php echo esc_url(get_post_type_archive_link('invest_project')); ?>" 
                               class="btn btn-outline">
                                <?php esc_html_e('Скинути', 'slavuta-invest'); ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if (have_posts()) : ?>
                
                <!-- Результати -->
                <div class="archive-results">
                    <p class="results-count">
                        <?php
                        global $wp_query;
                        printf(
                            esc_html(_n(
                                'Знайдено %s проєкт',
                                'Знайдено %s проєктів',
                                $wp_query->found_posts,
                                'slavuta-invest'
                            )),
                            number_format_i18n($wp_query->found_posts)
                        );
                        ?>
                    </p>
                </div>
                
                <!-- Сітка проєктів -->
                <div class="projects-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        $main_image = get_field('main_image');
                        $short_description = get_field('short_description');
                        $investment_amount = get_field('investment_amount');
                        $project_status = get_field('project_status');
                        $project_categories = get_the_terms(get_the_ID(), 'project_category');
                        ?>
                        
                        <article id="post-<?php the_ID(); ?>" <?php post_class('project-card'); ?>>
                            <?php if ($main_image) : ?>
                                <div class="project-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo esc_url($main_image['sizes']['project-thumb']); ?>" 
                                             alt="<?php echo esc_attr($main_image['alt'] ?: get_the_title()); ?>"
                                             loading="lazy">
                                    </a>
                                    <?php if ($project_status) : ?>
                                        <span class="project-status status-<?php echo sanitize_title($project_status); ?>">
                                            <?php echo esc_html($project_status); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="project-card-content">
                                <?php if ($project_categories && !is_wp_error($project_categories)) : ?>
                                    <div class="project-categories">
                                        <?php foreach ($project_categories as $category) : ?>
                                            <a href="<?php echo esc_url(add_query_arg('project_category', $category->slug, get_post_type_archive_link('invest_project'))); ?>" 
                                               class="project-category">
                                                <?php echo esc_html($category->name); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h2 class="project-card-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                
                                <?php if ($short_description) : ?>
                                    <p class="project-card-excerpt">
                                        <?php echo wp_trim_words($short_description, 20); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($investment_amount) : ?>
                                    <div class="project-investment">
                                        <span class="investment-label">
                                            <?php esc_html_e('Інвестиції:', 'slavuta-invest'); ?>
                                        </span>
                                        <span class="investment-amount">
                                            <?php echo number_format($investment_amount, 0, ',', ' '); ?> грн
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
                                    <?php esc_html_e('Детальніше', 'slavuta-invest'); ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                        
                    <?php endwhile; ?>
                </div>
                
                <!-- Пагінація -->
                <?php slavuta_pagination(); ?>
                
            <?php else : ?>
                
                <!-- Повідомлення про відсутність результатів -->
                <div class="no-results">
                    <svg class="no-results-icon" width="80" height="80" viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="39" stroke="#E0E0E0" stroke-width="2"/>
                        <path d="M30 30L50 50M50 30L30 50" stroke="#E0E0E0" stroke-width="2"/>
                    </svg>
                    <h2 class="no-results-title">
                        <?php esc_html_e('Проєкти не знайдено', 'slavuta-invest'); ?>
                    </h2>
                    <p class="no-results-text">
                        <?php esc_html_e('Спробуйте змінити параметри фільтрації або скинути фільтри.', 'slavuta-invest'); ?>
                    </p>
                    <a href="<?php echo esc_url(get_post_type_archive_link('invest_project')); ?>" 
                       class="btn btn-primary">
                        <?php esc_html_e('Скинути фільтри', 'slavuta-invest'); ?>
                    </a>
                </div>
                
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();