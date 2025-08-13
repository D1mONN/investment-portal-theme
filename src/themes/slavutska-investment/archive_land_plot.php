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
					<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
					<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
/**
 * Карта земельних ділянок з використанням Leaflet.js
 * Інтеграція з WordPress REST API
 */

document.addEventListener('DOMContentLoaded', function() {
    let map = null;
    let markersLayer = null;
    let plotsData = [];
    
    // Конфігурація карти
    const mapConfig = {
        center: [50.2782, 26.8635], // Координати Славути
        zoom: 12,
        maxZoom: 18,
        minZoom: 8
    };
    
    // Ініціалізація карти
    function initializeMap() {
        const mapContainer = document.getElementById('plots-map');
        if (!mapContainer) {
            console.error('Контейнер карти не знайдено');
            return;
        }
        
        // Очищаємо placeholder
        mapContainer.innerHTML = '';
        
        // Створюємо карту
        map = L.map('plots-map', {
            center: mapConfig.center,
            zoom: mapConfig.zoom,
            maxZoom: mapConfig.maxZoom,
            minZoom: mapConfig.minZoom,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        // Додаємо тайли OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);
        
        // Створюємо групу для маркерів
        markersLayer = L.layerGroup().addTo(map);
        
        // Завантажуємо дані ділянок
        loadPlotsData();
        
        console.log('Карта ініціалізована успішно');
    }
    
    // Завантаження даних ділянок з API
    async function loadPlotsData() {
        try {
            showMapLoading(true);
            
            const response = await fetch('/wp-json/slavutska/v1/map-data', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.wpApiSettings?.nonce || ''
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && Array.isArray(data.plots)) {
                plotsData = data.plots;
                displayPlotsOnMap(plotsData);
                updateMapBounds();
            } else {
                throw new Error(data.message || 'Невалідні дані від API');
            }
            
        } catch (error) {
            console.error('Помилка завантаження даних ділянок:', error);
            showMapError('Не вдалося завантажити дані ділянок. Спробуйте оновити сторінку.');
        } finally {
            showMapLoading(false);
        }
    }
    
    // Відображення ділянок на карті
    function displayPlotsOnMap(plots) {
        // Очищаємо попередні маркери
        markersLayer.clearLayers();
        
        if (!plots.length) {
            showMapMessage('Ділянки для відображення відсутні');
            return;
        }
        
        plots.forEach(plot => {
            if (plot.latitude && plot.longitude) {
                createPlotMarker(plot);
            }
        });
        
        console.log(`Відображено ${plots.length} ділянок на карті`);
    }
    
    // Створення маркера для ділянки
    function createPlotMarker(plot) {
        const lat = parseFloat(plot.latitude);
        const lng = parseFloat(plot.longitude);
        
        if (isNaN(lat) || isNaN(lng)) {
            console.warn('Невалідні координати для ділянки:', plot.id);
            return;
        }
        
        // Створюємо кастомну іконку
        const customIcon = L.divIcon({
            className: 'custom-plot-marker',
            html: `
                <div class="marker-icon">
                    <i class="icon-map-pin"></i>
                </div>
            `,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30]
        });
        
        // Створюємо маркер
        const marker = L.marker([lat, lng], {
            icon: customIcon,
            title: plot.title || 'Земельна ділянка'
        });
        
        // Створюємо popup з інформацією
        const popupContent = createPopupContent(plot);
        marker.bindPopup(popupContent, {
            maxWidth: 300,
            className: 'plot-popup'
        });
        
        // Додаємо обробник кліку
        marker.on('click', function() {
            // Додаткова логіка при кліку (опціонально)
            trackMarkerClick(plot.id);
        });
        
        // Додаємо маркер до групи
        markersLayer.addLayer(marker);
    }
    
    // Створення контенту для popup
    function createPopupContent(plot) {
        const area = plot.area ? parseFloat(plot.area).toFixed(2) : 'Не вказано';
        const price = plot.price_per_hectare ? 
            new Intl.NumberFormat('uk-UA').format(plot.price_per_hectare) : 'Не вказано';
        const totalPrice = plot.area && plot.price_per_hectare ? 
            new Intl.NumberFormat('uk-UA').format(plot.area * plot.price_per_hectare) : null;
        
        return `
            <div class="plot-popup-content">
                <div class="popup-header">
                    <h3 class="popup-title">${plot.title || 'Земельна ділянка'}</h3>
                    ${plot.land_type ? `<span class="popup-badge">${plot.land_type}</span>` : ''}
                </div>
                
                <div class="popup-info">
                    ${plot.cadastral_number ? `
                        <div class="popup-meta">
                            <i class="icon-hash"></i>
                            <span>Кадастровий номер: ${plot.cadastral_number}</span>
                        </div>
                    ` : ''}
                    
                    <div class="popup-meta">
                        <i class="icon-maximize"></i>
                        <span>Площа: ${area} га</span>
                    </div>
                    
                    <div class="popup-meta">
                        <i class="icon-dollar-sign"></i>
                        <span>Ціна за га: ${price} грн</span>
                    </div>
                    
                    ${totalPrice ? `
                        <div class="popup-meta popup-meta--highlight">
                            <i class="icon-calculator"></i>
                            <span>Загальна вартість: ${totalPrice} грн</span>
                        </div>
                    ` : ''}
                    
                    ${plot.purpose ? `
                        <div class="popup-meta">
                            <i class="icon-target"></i>
                            <span>Призначення: ${plot.purpose}</span>
                        </div>
                    ` : ''}
                </div>
                
                <div class="popup-actions">
                    <a href="${plot.permalink}" class="btn btn--primary btn--small" target="_blank">
                        Детальніше
                        <i class="icon-arrow-right"></i>
                    </a>
                </div>
            </div>
        `;
    }
    
    // Оновлення меж карти для показу всіх ділянок
    function updateMapBounds() {
        if (!plotsData.length || !map) return;
        
        const validPlots = plotsData.filter(plot => 
            plot.latitude && plot.longitude && 
            !isNaN(parseFloat(plot.latitude)) && 
            !isNaN(parseFloat(plot.longitude))
        );
        
        if (!validPlots.length) return;
        
        const bounds = L.latLngBounds(
            validPlots.map(plot => [
                parseFloat(plot.latitude), 
                parseFloat(plot.longitude)
            ])
        );
        
        map.fitBounds(bounds, {
            padding: [20, 20],
            maxZoom: 15
        });
    }
    
    // Показати всі ділянки
    function showAllPlots() {
        if (!map || !plotsData.length) return;
        
        displayPlotsOnMap(plotsData);
        updateMapBounds();
    }
    
    // Скинути карту до початкового стану
    function resetMap() {
        if (!map) return;
        
        map.setView(mapConfig.center, mapConfig.zoom);
        markersLayer.clearLayers();
        displayPlotsOnMap(plotsData);
    }
    
    // Фільтрація ділянок на карті
    function filterMapPlots(filteredCards) {
        if (!map || !plotsData.length) return;
        
        // Отримуємо ID видимих ділянок
        const visiblePlotIds = Array.from(filteredCards)
            .filter(card => card.style.display !== 'none')
            .map(card => card.dataset.plotId || extractPlotIdFromCard(card));
        
        // Фільтруємо дані ділянок
        const filteredPlots = plotsData.filter(plot => 
            visiblePlotIds.includes(plot.id.toString())
        );
        
        displayPlotsOnMap(filteredPlots);
        
        if (filteredPlots.length) {
            updateMapBounds();
        }
    }
    
    // Витягання ID ділянки з картки (якщо не вказано в data-plot-id)
    function extractPlotIdFromCard(card) {
        const link = card.querySelector('.land-plot-card-title a');
        if (link) {
            const url = link.getAttribute('href');
            const match = url.match(/\/land-plot\/([^\/]+)\//);
            return match ? match[1] : null;
        }
        return null;
    }
    
    // Показ індикатора завантаження
    function showMapLoading(show) {
        const mapContainer = document.getElementById('plots-map');
        if (!mapContainer) return;
        
        if (show) {
            mapContainer.innerHTML = `
                <div class="map-placeholder">
                    <div class="loading-spinner"></div>
                    <p>Завантаження карти з ділянками...</p>
                </div>
            `;
        }
    }
    
    // Показ повідомлення про помилку
    function showMapError(message) {
        const mapContainer = document.getElementById('plots-map');
        if (!mapContainer) return;
        
        mapContainer.innerHTML = `
            <div class="map-placeholder map-error">
                <i class="icon-alert-circle"></i>
                <p>${message}</p>
                <button onclick="location.reload()" class="btn btn--primary btn--small">
                    Оновити сторінку
                </button>
            </div>
        `;
    }
    
    // Показ інформаційного повідомлення
    function showMapMessage(message) {
        const mapContainer = document.getElementById('plots-map');
        if (!mapContainer) return;
        
        if (map) {
            // Якщо карта вже ініціалізована, показуємо повідомлення поверх
            const messageDiv = document.createElement('div');
            messageDiv.className = 'map-overlay-message';
            messageDiv.innerHTML = `<p>${message}</p>`;
            mapContainer.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }
    }
    
    // Трекінг кліків по маркерах (аналітика)
    function trackMarkerClick(plotId) {
        // Відправка події в Google Analytics або інший сервіс
        if (typeof gtag !== 'undefined') {
            gtag('event', 'map_marker_click', {
                'plot_id': plotId,
                'page_location': window.location.href
            });
        }
    }
    
    // Обробники кнопок управління картою
    const showAllBtn = document.getElementById('show-all-plots');
    const resetMapBtn = document.getElementById('reset-map');
    
    if (showAllBtn) {
        showAllBtn.addEventListener('click', showAllPlots);
    }
    
    if (resetMapBtn) {
        resetMapBtn.addEventListener('click', resetMap);
    }
    
    // Інтеграція з фільтрами
    const originalFilterFunction = window.filterLandPlots;
    if (typeof originalFilterFunction === 'function') {
        window.filterLandPlots = function() {
            originalFilterFunction();
            
            // Оновлюємо карту після фільтрації
            const grid = document.getElementById('land-plots-grid');
            if (grid) {
                const visibleCards = grid.querySelectorAll('.land-plot-card[style=""], .land-plot-card:not([style])');
                filterMapPlots(visibleCards);
            }
        };
    }
    
    // Обробник кнопок "показати на карті" в картках
    document.addEventListener('click', function(e) {
        if (e.target.closest('.card-map-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.card-map-btn');
            const lat = parseFloat(btn.dataset.lat);
            const lng = parseFloat(btn.dataset.lng);
            const title = btn.dataset.title;
            
            if (map && !isNaN(lat) && !isNaN(lng)) {
                // Переключаємося на вид карти
                const mapViewBtn = document.querySelector('.view-btn--map');
                if (mapViewBtn) {
                    mapViewBtn.click();
                }
                
                // Центруємо карту на ділянці
                map.setView([lat, lng], 16);
                
                // Знаходимо і відкриваємо відповідний popup
                markersLayer.eachLayer(function(layer) {
                    if (layer.getLatLng().lat === lat && layer.getLatLng().lng === lng) {
                        layer.openPopup();
                    }
                });
            }
        }
    });
    
    // Ініціалізація при завантаженні сторінки
    initializeMap();
    
    // Експорт функцій для глобального доступу
    window.landPlotsMap = {
        showAllPlots,
        resetMap,
        filterMapPlots,
        reloadData: loadPlotsData
    };
});
</script>

<?php get_footer(); ?>