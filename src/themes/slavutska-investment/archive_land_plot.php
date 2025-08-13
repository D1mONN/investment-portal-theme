<?php
/**
 * Архівна сторінка земельних ділянок
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

<div class="archive-page archive-land-plots">
    <!-- Hero секція -->
    <section class="archive-hero">
        <div class="container">
            <div class="archive-hero-content">
                <header class="archive-header">
                    <h1 class="archive-title">
                        <?php _e('Земельні ділянки', 'slavutska-investment'); ?>
                    </h1>
                    <p class="archive-description">
                        <?php _e('Якісні земельні ресурси для бізнесу та інвестицій у Славутській громаді', 'slavutska-investment'); ?>
                    </p>
                </header>
                
                <div class="archive-stats">
                    <?php
                    $total_plots = wp_count_posts('land_plot')->publish;
                    
                    // Розрахунок загальної площі
                    $total_area = 0;
                    $average_price = 0;
                    $plots = get_posts([
                        'post_type' => 'land_plot',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'fields' => 'ids'
                    ]);
                    
                    $valid_prices = [];
                    foreach ($plots as $plot_id) {
                        $area = get_post_meta($plot_id, '_area', true);
                        $price = get_post_meta($plot_id, '_price_per_hectare', true);
                        
                        if ($area) {
                            $total_area += floatval($area);
                        }
                        
                        if ($price) {
                            $valid_prices[] = floatval($price);
                        }
                    }
                    
                    if (!empty($valid_prices)) {
                        $average_price = array_sum($valid_prices) / count($valid_prices);
                    }
                    ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($total_plots); ?></span>
                        <span class="stat-label"><?php _e('Доступних ділянок', 'slavutska-investment'); ?></span>
                    </div>
                    
                    <?php if ($total_area > 0): ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo number_format($total_area, 0); ?></span>
                            <span class="stat-label"><?php _e('Гектарів землі', 'slavutska-investment'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($average_price > 0): ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo number_format($average_price / 1000, 0); ?>К</span>
                            <span class="stat-label"><?php _e('Середня ціна/га грн', 'slavutska-investment'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Карта з ділянками -->
    <section class="archive-map">
        <div class="container">
            <div class="map-header">
                <h2 class="map-title"><?php _e('Розташування ділянок', 'slavutska-investment'); ?></h2>
                <div class="map-controls">
                    <button class="btn btn--outline btn--small" id="show-all-plots">
                        <i class="icon-map" aria-hidden="true"></i>
                        <?php _e('Показати всі', 'slavutska-investment'); ?>
                    </button>
                    <button class="btn btn--outline btn--small" id="reset-map">
                        <i class="icon-refresh" aria-hidden="true"></i>
                        <?php _e('Скинути', 'slavutska-investment'); ?>
                    </button>
                </div>
            </div>
            
            <div class="interactive-map">
                <div id="plots-map" class="plots-map-container">
                    <!-- Карта буде завантажена через JavaScript -->
                    <div class="map-placeholder">
                        <i class="icon-map-pin" aria-hidden="true"></i>
                        <p><?php _e('Завантаження карти з ділянками...', 'slavutska-investment'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Фільтри -->
    <section class="archive-filters">
        <div class="container">
            <div class="filters-wrapper">
                <div class="filters-row">
                    <!-- Тип землі -->
                    <div class="filter-group">
                        <label for="land-type-filter" class="filter-label">
                            <?php _e('Тип землі:', 'slavutska-investment'); ?>
                        </label>
                        <select id="land-type-filter" class="filter-select">
                            <option value=""><?php _e('Всі типи', 'slavutska-investment'); ?></option>
                            <?php
                            $land_types = get_terms([
                                'taxonomy' => 'land_type',
                                'hide_empty' => true
                            ]);
                            
                            if ($land_types && !is_wp_error($land_types)):
                                foreach ($land_types as $type):
                            ?>
                                <option value="<?php echo esc_attr($type->slug); ?>" 
                                        <?php selected(get_query_var('land_type'), $type->slug); ?>>
                                    <?php echo esc_html($type->name); ?> (<?php echo $type->count; ?>)
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <!-- Площа -->
                    <div class="filter-group">
                        <label class="filter-label"><?php _e('Площа (га):', 'slavutska-investment'); ?></label>
                        <div class="range-filter">
                            <input type="number" 
                                   id="area-min" 
                                   class="filter-input" 
                                   step="0.01"
                                   placeholder="<?php esc_attr_e('Від', 'slavutska-investment'); ?>">
                            <span class="range-separator">—</span>
                            <input type="number" 
                                   id="area-max" 
                                   class="filter-input" 
                                   step="0.01"
                                   placeholder="<?php esc_attr_e('До', 'slavutska-investment'); ?>">
                        </div>
                    </div>
                    
                    <!-- Ціна за гектар -->
                    <div class="filter-group">
                        <label class="filter-label"><?php _e('Ціна за га (грн):', 'slavutska-investment'); ?></label>
                        <div class="range-filter">
                            <input type="number" 
                                   id="price-min" 
                                   class="filter-input" 
                                   placeholder="<?php esc_attr_e('Від', 'slavutska-investment'); ?>">
                            <span class="range-separator">—</span>
                            <input type="number" 
                                   id="price-max" 
                                   class="filter-input" 
                                   placeholder="<?php esc_attr_e('До', 'slavutska-investment'); ?>">
                        </div>
                    </div>
                    
                    <!-- Цільове призначення -->
                    <div class="filter-group">
                        <label for="purpose-filter" class="filter-label">
                            <?php _e('Призначення:', 'slavutska-investment'); ?>
                        </label>
                        <select id="purpose-filter" class="filter-select">
                            <option value=""><?php _e('Будь-яке', 'slavutska-investment'); ?></option>
                            <option value="commercial"><?php _e('Комерційне', 'slavutska-investment'); ?></option>
                            <option value="industrial"><?php _e('Промислове', 'slavutska-investment'); ?></option>
                            <option value="agricultural"><?php _e('Сільськогосподарське', 'slavutska-investment'); ?></option>
                            <option value="residential"><?php _e('Житлове', 'slavutska-investment'); ?></option>
                            <option value="mixed"><?php _e('Змішане', 'slavutska-investment'); ?></option>
                        </select>
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
                            <option value="area-desc"><?php _e('За площею (більші)', 'slavutska-investment'); ?></option>
                            <option value="area-asc"><?php _e('За площею (менші)', 'slavutska-investment'); ?></option>
                            <option value="price-desc"><?php _e('За ціною (дорожчі)', 'slavutska-investment'); ?></option>
                            <option value="price-asc"><?php _e('За ціною (дешевші)', 'slavutska-investment'); ?></option>
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
                                'Знайдено %s ділянку',
                                'Знайдено %s ділянок',
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
                    <button class="view-btn view-btn--map" data-view="map" aria-label="<?php esc_attr_e('Карта', 'slavutska-investment'); ?>">
                        <i class="icon-map" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            
            <div class="land-plots-container">
                <?php if (have_posts()): ?>
                    <div class="land-plots-grid" id="land-plots-grid">
                        <?php while (have_posts()): the_post(); ?>
                            <?php
                            $area = get_post_meta(get_the_ID(), '_area', true);
                            $price_per_hectare = get_post_meta(get_the_ID(), '_price_per_hectare', true);
                            $cadastral_number = get_post_meta(get_the_ID(), '_cadastral_number', true);
                            $purpose = get_post_meta(get_the_ID(), '_purpose', true);
                            $latitude = get_post_meta(get_the_ID(), '_latitude', true);
                            $longitude = get_post_meta(get_the_ID(), '_longitude', true);
                            $land_types = get_the_terms(get_the_ID(), 'land_type');
                            
                            $total_price = $area && $price_per_hectare ? $area * $price_per_hectare : null;
                            ?>
                            
                            <article class="land-plot-card" 
                                     data-area="<?php echo esc_attr($area ?: 0); ?>"
                                     data-price="<?php echo esc_attr($price_per_hectare ?: 0); ?>"
                                     data-total="<?php echo esc_attr($total_price ?: 0); ?>"
                                     data-purpose="<?php echo esc_attr($purpose ?: ''); ?>"
                                     data-types="<?php echo esc_attr($land_types && !is_wp_error($land_types) ? implode(',', wp_list_pluck($land_types, 'slug')) : ''); ?>"
                                     data-lat="<?php echo esc_attr($latitude ?: ''); ?>"
                                     data-lng="<?php echo esc_attr($longitude ?: ''); ?>">
                                
                                <div class="land-plot-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('land-plot-image', [
                                                'class' => 'land-plot-thumbnail',
                                                'alt' => get_the_title(),
                                                'loading' => 'lazy'
                                            ]); ?>
                                        <?php else: ?>
                                            <div class="placeholder-image">
                                                <i class="icon-map" aria-hidden="true"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    
                                    <?php if ($land_types && !is_wp_error($land_types)): ?>
                                        <div class="land-plot-card-badge">
                                            <?php echo esc_html($land_types[0]->name); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="land-plot-card-overlay">
                                        <a href="<?php the_permalink(); ?>" class="card-overlay-link">
                                            <i class="icon-arrow-right" aria-hidden="true"></i>
                                        </a>
                                        
                                        <?php if ($latitude && $longitude): ?>
                                            <button class="card-map-btn" 
                                                    data-lat="<?php echo esc_attr($latitude); ?>"
                                                    data-lng="<?php echo esc_attr($longitude); ?>"
                                                    data-title="<?php echo esc_attr(get_the_title()); ?>"
                                                    title="<?php esc_attr_e('Показати на карті', 'slavutska-investment'); ?>">
                                                <i class="icon-map-pin" aria-hidden="true"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="land-plot-card-content">
                                    <header class="land-plot-card-header">
                                        <h2 class="land-plot-card-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <?php if ($cadastral_number): ?>
                                            <div class="cadastral-info">
                                                <i class="icon-hash" aria-hidden="true"></i>
                                                <span><?php echo esc_html($cadastral_number); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </header>
                                    
                                    <div class="land-plot-card-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                    </div>
                                    
                                    <div class="land-plot-card-meta">
                                        <div class="meta-grid">
                                            <?php if ($area): ?>
                                                <div class="meta-item">
                                                    <i class="icon-maximize" aria-hidden="true"></i>
                                                    <span class="meta-label"><?php _e('Площа:', 'slavutska-investment'); ?></span>
                                                    <span class="meta-value"><?php echo esc_html($area); ?> га</span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($price_per_hectare): ?>
                                                <div class="meta-item">
                                                    <i class="icon-dollar-sign" aria-hidden="true"></i>
                                                    <span class="meta-label"><?php _e('Ціна за га:', 'slavutska-investment'); ?></span>
                                                    <span class="meta-value"><?php echo number_format($price_per_hectare, 0, ',', ' '); ?> грн</span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($total_price): ?>
                                                <div class="meta-item meta-item--highlight">
                                                    <i class="icon-calculator" aria-hidden="true"></i>
                                                    <span class="meta-label"><?php _e('Загальна вартість:', 'slavutska-investment'); ?></span>
                                                    <span class="meta-value"><?php echo number_format($total_price, 0, ',', ' '); ?> грн</span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($purpose): ?>
                                                <div class="meta-item">
                                                    <i class="icon-target" aria-hidden="true"></i>
                                                    <span class="meta-label"><?php _e('Призначення:', 'slavutska-investment'); ?></span>
                                                    <span class="meta-value"><?php echo esc_html($purpose); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="land-plot-card-footer">
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
                            <i class="icon-map" aria-hidden="true"></i>
                        </div>
                        <h3 class="no-results-title"><?php _e('Земельних ділянок не знайдено', 'slavutska-investment'); ?></h3>
                        <p class="no-results-text">
                            <?php _e('За вашими критеріями пошуку земельних ділянок не знайдено. Спробуйте змінити параметри фільтрації або', 'slavutska-investment'); ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('land_plot')); ?>">
                                <?php _e('переглянути всі ділянки', 'slavutska-investment'); ?>
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
                <h2 class="cta-title"><?php _e('Потрібна консультація з вибору землі?', 'slavutska-investment'); ?></h2>
                <p class="cta-description">
                    <?php _e('Наші експерти допоможуть підібрати оптимальну земельну ділянку відповідно до ваших потреб', 'slavutska-investment'); ?>
                </p>
                <div class="cta-actions">
                    <a href="#contact" 
                       class="btn btn--primary btn--large"
                       data-scroll-to="contact">
                        <?php _e('Отримати консультацію', 'slavutska-investment'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/investments/')); ?>" 
                       class="btn btn--outline btn--large">
                        <?php _e('Інвестиційні пропозиції', 'slavutska-investment'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Фільтрація та сортування земельних ділянок
document.addEventListener('DOMContentLoaded', function() {
    const filterElements = {
        landType: document.getElementById('land-type-filter'),
        areaMin: document.getElementById('area-min'),
        areaMax: document.getElementById('area-max'),
        priceMin: document.getElementById('price-min'),
        priceMax: document.getElementById('price-max'),
        purpose: document.getElementById('purpose-filter'),
        sort: document.getElementById('sort-select'),
        reset: document.getElementById('reset-filters'),
        viewToggle: document.querySelectorAll('.view-btn'),
        grid: document.getElementById('land-plots-grid')
    };

    // Фільтрація
    function filterLandPlots() {
        const cards = filterElements.grid.querySelectorAll('.land-plot-card');
        const filters = {
            landType: filterElements.landType.value,
            areaMin: parseFloat(filterElements.areaMin.value) || 0,
            areaMax: parseFloat(filterElements.areaMax.value) || Infinity,
            priceMin: parseFloat(filterElements.priceMin.value) || 0,
            priceMax: parseFloat(filterElements.priceMax.value) || Infinity,
            purpose: filterElements.purpose.value
        };

        cards.forEach(card => {
            let show = true;

            // Фільтр типу землі
            if (filters.landType && !card.dataset.types.includes(filters.landType)) {
                show = false;
            }

            // Фільтр площі
            const area = parseFloat(card.dataset.area) || 0;
            if (area < filters.areaMin || area > filters.areaMax) {
                show = false;
            }

            // Фільтр ціни
            const price = parseFloat(card.dataset.price) || 0;
            if (price < filters.priceMin || price > filters.priceMax) {
                show = false;
            }

            // Фільтр призначення
            if (filters.purpose && !card.dataset.purpose.toLowerCase().includes(filters.purpose)) {
                show = false;
            }

            card.style.display = show ? '' : 'none';
        });

        updateResultsCount();
    }

    // Сортування
    function sortLandPlots() {
        const container = filterElements.grid;
        const cards = Array.from(container.querySelectorAll('.land-plot-card'));
        const sortType = filterElements.sort.value;

        cards.sort((a, b) => {
            switch (sortType) {
                case 'area-desc':
                    return parseFloat(b.dataset.area || 0) - parseFloat(a.dataset.area || 0);
                case 'area-asc':
                    return parseFloat(a.dataset.area || 0) - parseFloat(b.dataset.area || 0);
                case 'price-desc':
                    return parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0);
                case 'price-asc':
                    return parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0);
                case 'title-asc':
                    return a.querySelector('.land-plot-card-title a').textContent.localeCompare(
                        b.querySelector('.land-plot-card-title a').textContent
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
        const visibleCards = filterElements.grid.querySelectorAll('.land-plot-card[style=""], .land-plot-card:not([style])');
        const countElement = document.querySelector('.count-text');
        const count = visibleCards.length;
        
        if (countElement) {
            countElement.innerHTML = `Знайдено <strong>${count}</strong> ${count === 1 ? 'ділянку' : count < 5 ? 'ділянки' : 'ділянок'}`;
        }
    }

    // Перемикання виду
    filterElements.viewToggle.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            filterElements.viewToggle.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (view === 'map') {
                filterElements.grid.style.display = 'none';
                document.querySelector('.interactive-map').style.display = 'block';
                // Тут можна додати логіку для показу ділянок на карті
            } else {
                filterElements.grid.style.display = '';
                document.querySelector('.interactive-map').style.display = 'none';
                filterElements.grid.className = view === 'list' ? 'land-plots-list' : 'land-plots-grid';
            }
        });
    });

    // Обробники подій
    [filterElements.landType, filterElements.purpose].forEach(el => {
        if (el) el.addEventListener('change', filterLandPlots);
    });

    [filterElements.areaMin, filterElements.areaMax, filterElements.priceMin, filterElements.priceMax].forEach(el => {
        if (el) el.addEventListener('input', debounce(filterLandPlots, 500));
    });

    if (filterElements.sort) {
        filterElements.sort.addEventListener('change', () => {
            sortLandPlots();
            filterLandPlots();
        });
    }

    if (filterElements.reset) {
        filterElements.reset.addEventListener('click', () => {
            filterElements.landType.value = '';
            filterElements.areaMin.value = '';
            filterElements.areaMax.value = '';
            filterElements.priceMin.value = '';
            filterElements.priceMax.value = '';
            filterElements.purpose.value = '';
            filterElements.sort.value = 'date-desc';
            
            filterLandPlots();
            sortLandPlots();
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