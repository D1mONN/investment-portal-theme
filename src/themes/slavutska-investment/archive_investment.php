<?php
/**
 * Архівна сторінка інвестиційних пропозицій
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

<div class="archive-page archive-investments">
    <!-- Hero секція архівної сторінки -->
    <section class="archive-hero">
        <div class="container">
            <div class="archive-hero-content">
                <header class="archive-header">
                    <h1 class="archive-title">
                        <?php _e('Інвестиційні пропозиції', 'slavutska-investment'); ?>
                    </h1>
                    <p class="archive-description">
                        <?php _e('Огляд актуальних можливостей для інвестування в розвиток Славутської громади', 'slavutska-investment'); ?>
                    </p>
                </header>
                
                <div class="archive-stats">
                    <?php
                    $total_investments = wp_count_posts('investment')->publish;
                    $featured_investments = get_posts([
                        'post_type' => 'investment',
                        'meta_key' => '_is_featured',
                        'meta_value' => '1',
                        'post_status' => 'publish',
                        'fields' => 'ids'
                    ]);
                    $featured_count = count($featured_investments);
                    
                    // Розрахунок загальної суми інвестицій
                    $total_amount = 0;
                    $investments = get_posts([
                        'post_type' => 'investment',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'fields' => 'ids'
                    ]);
                    
                    foreach ($investments as $investment_id) {
                        $amount = get_post_meta($investment_id, '_investment_amount', true);
                        if ($amount) {
                            $total_amount += floatval($amount);
                        }
                    }
                    ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($total_investments); ?></span>
                        <span class="stat-label"><?php _e('Активних пропозицій', 'slavutska-investment'); ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($featured_count); ?></span>
                        <span class="stat-label"><?php _e('Рекомендованих', 'slavutska-investment'); ?></span>
                    </div>
                    
                    <?php if ($total_amount > 0): ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo number_format($total_amount / 1000000, 1); ?>М</span>
                            <span class="stat-label"><?php _e('Загальний обсяг грн', 'slavutska-investment'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Фільтри та сортування -->
    <section class="archive-filters">
        <div class="container">
            <div class="filters-wrapper">
                <div class="filters-row">
                    <!-- Категорії -->
                    <div class="filter-group">
                        <label for="category-filter" class="filter-label">
                            <?php _e('Категорія:', 'slavutska-investment'); ?>
                        </label>
                        <select id="category-filter" class="filter-select">
                            <option value=""><?php _e('Всі категорії', 'slavutska-investment'); ?></option>
                            <?php
                            $categories = get_terms([
                                'taxonomy' => 'investment_category',
                                'hide_empty' => true
                            ]);
                            
                            if ($categories && !is_wp_error($categories)):
                                foreach ($categories as $category):
                            ?>
                                <option value="<?php echo esc_attr($category->slug); ?>" 
                                        <?php selected(get_query_var('investment_category'), $category->slug); ?>>
                                    <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <!-- Діапазон суми -->
                    <div class="filter-group">
                        <label class="filter-label"><?php _e('Сума інвестицій:', 'slavutska-investment'); ?></label>
                        <div class="range-filter">
                            <input type="number" 
                                   id="amount-min" 
                                   class="filter-input" 
                                   placeholder="<?php esc_attr_e('Від', 'slavutska-investment'); ?>">
                            <span class="range-separator">—</span>
                            <input type="number" 
                                   id="amount-max" 
                                   class="filter-input" 
                                   placeholder="<?php esc_attr_e('До', 'slavutska-investment'); ?>">
                        </div>
                    </div>
                    
                    <!-- Прибутковість -->
                    <div class="filter-group">
                        <label for="return-filter" class="filter-label">
                            <?php _e('Прибутковість:', 'slavutska-investment'); ?>
                        </label>
                        <select id="return-filter" class="filter-select">
                            <option value=""><?php _e('Будь-яка', 'slavutska-investment'); ?></option>
                            <option value="0-5"><?php _e('До 5%', 'slavutska-investment'); ?></option>
                            <option value="5-10"><?php _e('5-10%', 'slavutska-investment'); ?></option>
                            <option value="10-15"><?php _e('10-15%', 'slavutska-investment'); ?></option>
                            <option value="15+"><?php _e('Понад 15%', 'slavutska-investment'); ?></option>
                        </select>
                    </div>
                    
                    <!-- Тільки рекомендовані -->
                    <div class="filter-group filter-group--checkbox">
                        <label class="checkbox-filter">
                            <input type="checkbox" id="featured-only" class="filter-checkbox">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text"><?php _e('Тільки рекомендовані', 'slavutska-investment'); ?></span>
                        </label>
                    </div>
                </div>
                
                <div class="filters-actions">
                    <div class="sort-group">
                        <label for="sort-select" class="filter-label">
                            <?php _e('Сортувати:', 'slavutska-investment'); ?>
                        </label>
                        <select id="sort-select" class="filter-select">
                            <option value="date-desc"><?php _e('За датою (новіші)', 'slavutska-investment'); ?></option>
                            <option value="date-asc"><?php _e('За датою (старіші)', 'slavutska-investment'); ?></option>
                            <option value="amount-desc"><?php _e('За сумою (більші)', 'slavutska-investment'); ?></option>
                            <option value="amount-asc"><?php _e('За сумою (менші)', 'slavutska-investment'); ?></option>
                            <option value="return-desc"><?php _e('За прибутковістю (вища)', 'slavutska-investment'); ?></option>
                            <option value="return-asc"><?php _e('За прибутковістю (нижча)', 'slavutska-investment'); ?></option>
                            <option value="title-asc"><?php _e('За назвою (А-Я)', 'slavutska-investment'); ?></option>
                        </select>
                    </div>
                    
                    <button id="reset-filters" class="btn btn--outline btn--small">
                        <i class="icon-refresh" aria-hidden="true"></i>
                        <?php _e('Скинути фільтри', 'slavutska-investment'); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Результати -->
    <section class="archive-results">
        <div class="container">
            <div class="results-header">
                <div class="results-count">
                    <span class="count-text">
                        <?php
                        global $wp_query;
                        $found_posts = $wp_query->found_posts;
                        printf(
                            _n(
                                'Знайдено %s пропозицію',
                                'Знайдено %s пропозицій',
                                $found_posts,
                                'slavutska-investment'
                            ),
                            '<strong>' . number_format_i18n($found_posts) . '</strong>'
                        );
                        ?>
                    </span>
                </div>
                
                <div class="view-toggle">
                    <button class="view-btn view-btn--grid active" data-view="grid" aria-label="<?php esc_attr_e('Сітка', 'slavutska-investment'); ?>">
                        <i class="icon-grid" aria-hidden="true"></i>
                    </button>
                    <button class="view-btn view-btn--list" data-view="list" aria-label="<?php esc_attr_e('Список', 'slavutska-investment'); ?>">
                        <i class="icon-list" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            
            <div class="investments-container">
                <?php if (have_posts()): ?>
                    <div class="investments-grid" id="investments-grid">
                        <?php while (have_posts()): the_post(); ?>
                            <?php
                            $investment_amount = get_post_meta(get_the_ID(), '_investment_amount', true);
                            $investment_period = get_post_meta(get_the_ID(), '_investment_period', true);
                            $expected_return = get_post_meta(get_the_ID(), '_expected_return', true);
                            $location = get_post_meta(get_the_ID(), '_location', true);
                            $is_featured = get_post_meta(get_the_ID(), '_is_featured', true);
                            $categories = get_the_terms(get_the_ID(), 'investment_category');
                            ?>
                            
                            <article class="investment-card" 
                                     data-amount="<?php echo esc_attr($investment_amount ?: 0); ?>"
                                     data-return="<?php echo esc_attr($expected_return ?: 0); ?>"
                                     data-featured="<?php echo esc_attr($is_featured ? '1' : '0'); ?>"
                                     data-categories="<?php echo esc_attr($categories && !is_wp_error($categories) ? implode(',', wp_list_pluck($categories, 'slug')) : ''); ?>">
                                
                                <div class="investment-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('investment-thumbnail', [
                                                'class' => 'investment-thumbnail',
                                                'alt' => get_the_title(),
                                                'loading' => 'lazy'
                                            ]); ?>
                                        <?php else: ?>
                                            <div class="placeholder-image">
                                                <i class="icon-briefcase" aria-hidden="true"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    
                                    <?php if ($is_featured): ?>
                                        <div class="investment-card-badge">
                                            <i class="icon-star" aria-hidden="true"></i>
                                            <?php _e('Рекомендовано', 'slavutska-investment'); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="investment-card-overlay">
                                        <a href="<?php the_permalink(); ?>" class="card-overlay-link">
                                            <i class="icon-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="investment-card-content">
                                    <header class="investment-card-header">
                                        <?php if ($categories && !is_wp_error($categories)): ?>
                                            <div class="investment-categories">
                                                <?php foreach (array_slice($categories, 0, 2) as $category): ?>
                                                    <span class="category-tag">
                                                        <?php echo esc_html($category->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <h2 class="investment-card-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <?php if ($location): ?>
                                            <div class="investment-location">
                                                <i class="icon-map-pin" aria-hidden="true"></i>
                                                <span><?php echo esc_html($location); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </header>
                                    
                                    <div class="investment-card-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                                    </div>
                                    
                                    <div class="investment-card-meta">
                                        <?php if ($investment_amount): ?>
                                            <div class="meta-item">
                                                <span class="meta-label"><?php _e('Сума:', 'slavutska-investment'); ?></span>
                                                <span class="meta-value"><?php echo number_format($investment_amount, 0, ',', ' '); ?> грн</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($expected_return): ?>
                                            <div class="meta-item">
                                                <span class="meta-label"><?php _e('Прибутковість:', 'slavutska-investment'); ?></span>
                                                <span class="meta-value meta-value--highlight"><?php echo esc_html($expected_return); ?>%</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($investment_period): ?>
                                            <div class="meta-item">
                                                <span class="meta-label"><?php _e('Термін:', 'slavutska-investment'); ?></span>
                                                <span class="meta-value"><?php echo esc_html($investment_period); ?> міс.</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="investment-card-footer">
                                        <div class="card-date">
                                            <i class="icon-calendar" aria-hidden="true"></i>
                                            <span><?php echo get_the_date('d.m.Y'); ?></span>
                                        </div>
                                        
                                        <a href="<?php the_permalink(); ?>" class="btn btn--primary btn--small">
                                            <?php _e('Детальніше', 'slavutska-investment'); ?>
                                            <i class="icon-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Пагінація -->
                    <div class="archive-pagination">
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
                        <h3 class="no-results-title"><?php _e('Пропозицій не знайдено', 'slavutska-investment'); ?></h3>
                        <p class="no-results-text">
                            <?php _e('За вашими критеріями пошуку інвестиційних пропозицій не знайдено. Спробуйте змінити параметри фільтрації або', 'slavutska-investment'); ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('investment')); ?>">
                                <?php _e('переглянути всі пропозиції', 'slavutska-investment'); ?>
                            </a>.
                        </p>
                        <div class="no-results-actions">
                            <button id="reset-search" class="btn btn--primary">
                                <?php _e('Скинути фільтри', 'slavutska-investment'); ?>
                            </button>
                            <a href="#contact" class="btn btn--outline" data-scroll-to="contact">
                                <?php _e('Зв\'язатися з нами', 'slavutska-investment'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Заклик до дії -->
    <section class="archive-cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title"><?php _e('Не знайшли підходящу пропозицію?', 'slavutska-investment'); ?></h2>
                <p class="cta-description">
                    <?php _e('Зв\'яжіться з нами, і ми допоможемо знайти оптимальне рішення для ваших інвестиційних цілей', 'slavutska-investment'); ?>
                </p>
                <div class="cta-actions">
                    <a href="#contact" 
                       class="btn btn--primary btn--large"
                       data-scroll-to="contact">
                        <?php _e('Зв\'язатися з нами', 'slavutska-investment'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/land-plots/')); ?>" 
                       class="btn btn--outline btn--large">
                        <?php _e('Земельні ділянки', 'slavutska-investment'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Фільтрація та сортування інвестицій
document.addEventListener('DOMContentLoaded', function() {
    const filterElements = {
        category: document.getElementById('category-filter'),
        amountMin: document.getElementById('amount-min'),
        amountMax: document.getElementById('amount-max'),
        returnFilter: document.getElementById('return-filter'),
        featuredOnly: document.getElementById('featured-only'),
        sort: document.getElementById('sort-select'),
        reset: document.getElementById('reset-filters'),
        viewToggle: document.querySelectorAll('.view-btn'),
        grid: document.getElementById('investments-grid')
    };

    // Фільтрація
    function filterInvestments() {
        const cards = filterElements.grid.querySelectorAll('.investment-card');
        const filters = {
            category: filterElements.category.value,
            amountMin: parseFloat(filterElements.amountMin.value) || 0,
            amountMax: parseFloat(filterElements.amountMax.value) || Infinity,
            returnRange: filterElements.returnFilter.value,
            featuredOnly: filterElements.featuredOnly.checked
        };

        cards.forEach(card => {
            let show = true;

            // Фільтр категорії
            if (filters.category && !card.dataset.categories.includes(filters.category)) {
                show = false;
            }

            // Фільтр суми
            const amount = parseFloat(card.dataset.amount) || 0;
            if (amount < filters.amountMin || amount > filters.amountMax) {
                show = false;
            }

            // Фільтр прибутковості
            if (filters.returnRange) {
                const returnValue = parseFloat(card.dataset.return) || 0;
                const [min, max] = filters.returnRange.split('-').map(v => 
                    v === '+' ? Infinity : parseFloat(v)
                );
                if (returnValue < (min || 0) || returnValue > (max || Infinity)) {
                    show = false;
                }
            }

            // Фільтр рекомендованих
            if (filters.featuredOnly && card.dataset.featured !== '1') {
                show = false;
            }

            card.style.display = show ? '' : 'none';
        });

        updateResultsCount();
    }

    // Сортування
    function sortInvestments() {
        const container = filterElements.grid;
        const cards = Array.from(container.querySelectorAll('.investment-card'));
        const sortType = filterElements.sort.value;

        cards.sort((a, b) => {
            switch (sortType) {
                case 'amount-desc':
                    return parseFloat(b.dataset.amount || 0) - parseFloat(a.dataset.amount || 0);
                case 'amount-asc':
                    return parseFloat(a.dataset.amount || 0) - parseFloat(b.dataset.amount || 0);
                case 'return-desc':
                    return parseFloat(b.dataset.return || 0) - parseFloat(a.dataset.return || 0);
                case 'return-asc':
                    return parseFloat(a.dataset.return || 0) - parseFloat(b.dataset.return || 0);
                case 'title-asc':
                    return a.querySelector('.investment-card-title a').textContent.localeCompare(
                        b.querySelector('.investment-card-title a').textContent
                    );
                case 'date-asc':
                    return new Date(a.querySelector('.card-date span').textContent.split('.').reverse().join('-')) - 
                           new Date(b.querySelector('.card-date span').textContent.split('.').reverse().join('-'));
                default: // date-desc
                    return new Date(b.querySelector('.card-date span').textContent.split('.').reverse().join('-')) - 
                           new Date(a.querySelector('.card-date span').textContent.split('.').reverse().join('-'));
            }
        });

        cards.forEach(card => container.appendChild(card));
    }

    // Оновлення лічильника результатів
    function updateResultsCount() {
        const visibleCards = filterElements.grid.querySelectorAll('.investment-card[style=""], .investment-card:not([style])');
        const countElement = document.querySelector('.count-text');
        const count = visibleCards.length;
        
        if (countElement) {
            countElement.innerHTML = `Знайдено <strong>${count}</strong> ${count === 1 ? 'пропозицію' : count < 5 ? 'пропозиції' : 'пропозицій'}`;
        }
    }

    // Перемикання виду
    filterElements.viewToggle.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            filterElements.viewToggle.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            filterElements.grid.className = view === 'list' ? 'investments-list' : 'investments-grid';
        });
    });

    // Обробники подій
    [filterElements.category, filterElements.returnFilter, filterElements.featuredOnly].forEach(el => {
        if (el) el.addEventListener('change', filterInvestments);
    });

    [filterElements.amountMin, filterElements.amountMax].forEach(el => {
        if (el) el.addEventListener('input', debounce(filterInvestments, 500));
    });

    if (filterElements.sort) {
        filterElements.sort.addEventListener('change', () => {
            sortInvestments();
            filterInvestments();
        });
    }

    if (filterElements.reset) {
        filterElements.reset.addEventListener('click', () => {
            filterElements.category.value = '';
            filterElements.amountMin.value = '';
            filterElements.amountMax.value = '';
            filterElements.returnFilter.value = '';
            filterElements.featuredOnly.checked = false;
            filterElements.sort.value = 'date-desc';
            
            filterInvestments();
            sortInvestments();
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>

<?php get_footer(); ?>