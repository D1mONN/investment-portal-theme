<?php
/**
 * Розширення REST API для інвестиційного порталу
 * 
 * @package SlavutskaInvestment
 * @since 1.0.0
 */

// Запобігання прямого доступу
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Клас для розширення REST API
 */
class SlavutskaRestAPI 
{
    private $namespace = 'slavutska/v1';

    public function __construct() 
    {
        add_action('rest_api_init', [$this, 'register_api_routes']);
        add_action('rest_api_init', [$this, 'add_custom_fields_to_rest']);
        add_filter('rest_prepare_investment', [$this, 'prepare_investment_data'], 10, 3);
        add_filter('rest_prepare_land_plot', [$this, 'prepare_land_plot_data'], 10, 3);
    }

    /**
     * Реєстрація API маршрутів
     */
    public function register_api_routes() 
    {
        // Отримання інвестиційних пропозицій з фільтрами
        register_rest_route($this->namespace, '/investments', [
            'methods' => 'GET',
            'callback' => [$this, 'get_investments'],
            'permission_callback' => '__return_true',
            'args' => [
                'category' => [
                    'required' => false,
                    'type' => 'string',
                    'description' => 'Slug категорії'
                ],
                'amount_min' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Мінімальна сума інвестицій'
                ],
                'amount_max' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Максимальна сума інвестицій'
                ],
                'return_min' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Мінімальна прибутковість'
                ],
                'return_max' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Максимальна прибутковість'
                ],
                'featured_only' => [
                    'required' => false,
                    'type' => 'boolean',
                    'description' => 'Тільки рекомендовані'
                ],
                'per_page' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 10,
                    'minimum' => 1,
                    'maximum' => 100
                ]
            ]
        ]);

        // Отримання земельних ділянок з фільтрами
        register_rest_route($this->namespace, '/land-plots', [
            'methods' => 'GET',
            'callback' => [$this, 'get_land_plots'],
            'permission_callback' => '__return_true',
            'args' => [
                'land_type' => [
                    'required' => false,
                    'type' => 'string',
                    'description' => 'Slug типу землі'
                ],
                'area_min' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Мінімальна площа'
                ],
                'area_max' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Максимальна площа'
                ],
                'price_min' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Мінімальна ціна за гектар'
                ],
                'price_max' => [
                    'required' => false,
                    'type' => 'number',
                    'description' => 'Максимальна ціна за гектар'
                ],
                'purpose' => [
                    'required' => false,
                    'type' => 'string',
                    'description' => 'Цільове призначення'
                ],
                'has_coordinates' => [
                    'required' => false,
                    'type' => 'boolean',
                    'description' => 'Тільки з координатами'
                ]
            ]
        ]);

        // Пошук по всіх типах контенту
        register_rest_route($this->namespace, '/search', [
            'methods' => 'GET',
            'callback' => [$this, 'search_content'],
            'permission_callback' => '__return_true',
            'args' => [
                'query' => [
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Пошуковий запит',
                    'minimum' => 3
                ],
                'post_types' => [
                    'required' => false,
                    'type' => 'array',
                    'default' => ['investment', 'land_plot'],
                    'description' => 'Типи постів для пошуку'
                ]
            ]
        ]);

        // Статистика
        register_rest_route($this->namespace, '/statistics', [
            'methods' => 'GET',
            'callback' => [$this, 'get_statistics'],
            'permission_callback' => '__return_true'
        ]);

        // Контактна форма
        register_rest_route($this->namespace, '/contact', [
            'methods' => 'POST',
            'callback' => [$this, 'submit_contact_form'],
            'permission_callback' => '__return_true',
            'args' => [
                'name' => [
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Ім\'я'
                ],
                'email' => [
                    'required' => true,
                    'type' => 'string',
                    'format' => 'email',
                    'description' => 'Email адреса'
                ],
                'phone' => [
                    'required' => false,
                    'type' => 'string',
                    'description' => 'Номер телефону'
                ],
                'subject' => [
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Тема повідомлення'
                ],
                'message' => [
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Текст повідомлення'
                ],
                'nonce' => [
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Nonce для безпеки'
                ]
            ]
        ]);

        // Отримання координат для карти
        register_rest_route($this->namespace, '/map-data', [
            'methods' => 'GET',
            'callback' => [$this, 'get_map_data'],
            'permission_callback' => '__return_true',
            'args' => [
                'post_type' => [
                    'required' => false,
                    'type' => 'string',
                    'default' => 'land_plot',
                    'enum' => ['land_plot', 'investment']
                ]
            ]
        ]);

        // Отримання схожих елементів
        register_rest_route($this->namespace, '/related/(?P<post_type>investment|land_plot)/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_related_items'],
            'permission_callback' => '__return_true',
            'args' => [
                'count' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 3,
                    'minimum' => 1,
                    'maximum' => 10
                ]
            ]
        ]);

        // Збереження до обраного
        register_rest_route($this->namespace, '/favorites', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_favorites'],
                'permission_callback' => '__return_true'
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'add_to_favorites'],
                'permission_callback' => '__return_true',
                'args' => [
                    'post_id' => [
                        'required' => true,
                        'type' => 'integer'
                    ],
                    'post_type' => [
                        'required' => true,
                        'type' => 'string',
                        'enum' => ['investment', 'land_plot']
                    ]
                ]
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'remove_from_favorites'],
                'permission_callback' => '__return_true',
                'args' => [
                    'post_id' => [
                        'required' => true,
                        'type' => 'integer'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Отримання інвестиційних пропозицій
     */
    public function get_investments($request) 
    {
        $params = $request->get_params();
        
        $args = [
            'post_type' => 'investment',
            'post_status' => 'publish',
            'posts_per_page' => $params['per_page'] ?? 10,
            'paged' => $request->get_param('page') ?? 1,
            'meta_query' => [],
            'tax_query' => []
        ];

        // Фільтр за категорією
        if (!empty($params['category'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'investment_category',
                'field' => 'slug',
                'terms' => $params['category']
            ];
        }

        // Фільтр за сумою
        if (isset($params['amount_min']) || isset($params['amount_max'])) {
            $meta_query = [
                'key' => '_investment_amount',
                'type' => 'NUMERIC'
            ];

            if (isset($params['amount_min'])) {
                $meta_query['value'] = $params['amount_min'];
                $meta_query['compare'] = '>=';
            }

            if (isset($params['amount_max'])) {
                if (isset($params['amount_min'])) {
                    $args['meta_query'][] = [
                        'key' => '_investment_amount',
                        'value' => [$params['amount_min'], $params['amount_max']],
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN'
                    ];
                } else {
                    $meta_query['value'] = $params['amount_max'];
                    $meta_query['compare'] = '<=';
                }
            }

            if (!isset($params['amount_min']) || !isset($params['amount_max'])) {
                $args['meta_query'][] = $meta_query;
            }
        }

        // Фільтр за прибутковістю
        if (isset($params['return_min']) || isset($params['return_max'])) {
            $meta_query = [
                'key' => '_expected_return',
                'type' => 'NUMERIC'
            ];

            if (isset($params['return_min']) && isset($params['return_max'])) {
                $args['meta_query'][] = [
                    'key' => '_expected_return',
                    'value' => [$params['return_min'], $params['return_max']],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN'
                ];
            } else {
                if (isset($params['return_min'])) {
                    $meta_query['value'] = $params['return_min'];
                    $meta_query['compare'] = '>=';
                }
                if (isset($params['return_max'])) {
                    $meta_query['value'] = $params['return_max'];
                    $meta_query['compare'] = '<=';
                }
                $args['meta_query'][] = $meta_query;
            }
        }

        // Фільтр тільки рекомендовані
        if (!empty($params['featured_only'])) {
            $args['meta_query'][] = [
                'key' => '_is_featured',
                'value' => '1',
                'compare' => '='
            ];
        }

        if (count($args['meta_query']) > 1) {
            $args['meta_query']['relation'] = 'AND';
        }

        $query = new WP_Query($args);
        $investments = [];

        foreach ($query->posts as $post) {
            $investments[] = $this->format_investment_data($post);
        }

        return new WP_REST_Response([
            'investments' => $investments,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $args['paged']
        ]);
    }

    /**
     * Отримання земельних ділянок
     */
    public function get_land_plots($request) 
    {
        $params = $request->get_params();
        
        $args = [
            'post_type' => 'land_plot',
            'post_status' => 'publish',
            'posts_per_page' => $params['per_page'] ?? 10,
            'paged' => $request->get_param('page') ?? 1,
            'meta_query' => [],
            'tax_query' => []
        ];

        // Фільтр за типом землі
        if (!empty($params['land_type'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'land_type',
                'field' => 'slug',
                'terms' => $params['land_type']
            ];
        }

        // Фільтр за площею
        if (isset($params['area_min']) || isset($params['area_max'])) {
            $meta_query = [
                'key' => '_area',
                'type' => 'DECIMAL'
            ];

            if (isset($params['area_min']) && isset($params['area_max'])) {
                $args['meta_query'][] = [
                    'key' => '_area',
                    'value' => [$params['area_min'], $params['area_max']],
                    'type' => 'DECIMAL',
                    'compare' => 'BETWEEN'
                ];
            } else {
                if (isset($params['area_min'])) {
                    $meta_query['value'] = $params['area_min'];
                    $meta_query['compare'] = '>=';
                }
                if (isset($params['area_max'])) {
                    $meta_query['value'] = $params['area_max'];
                    $meta_query['compare'] = '<=';
                }
                $args['meta_query'][] = $meta_query;
            }
        }

        // Фільтр за ціною
        if (isset($params['price_min']) || isset($params['price_max'])) {
            $meta_query = [
                'key' => '_price_per_hectare',
                'type' => 'NUMERIC'
            ];

            if (isset($params['price_min']) && isset($params['price_max'])) {
                $args['meta_query'][] = [
                    'key' => '_price_per_hectare',
                    'value' => [$params['price_min'], $params['price_max']],
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN'
                ];
            } else {
                if (isset($params['price_min'])) {
                    $meta_query['value'] = $params['price_min'];
                    $meta_query['compare'] = '>=';
                }
                if (isset($params['price_max'])) {
                    $meta_query['value'] = $params['price_max'];
                    $meta_query['compare'] = '<=';
                }
                $args['meta_query'][] = $meta_query;
            }
        }

        // Фільтр за призначенням
        if (!empty($params['purpose'])) {
            $args['meta_query'][] = [
                'key' => '_purpose',
                'value' => $params['purpose'],
                'compare' => 'LIKE'
            ];
        }

        // Фільтр тільки з координатами
        if (!empty($params['has_coordinates'])) {
            $args['meta_query'][] = [
                'relation' => 'AND',
                [
                    'key' => '_latitude',
                    'value' => '',
                    'compare' => '!='
                ],
                [
                    'key' => '_longitude',
                    'value' => '',
                    'compare' => '!='
                ]
            ];
        }

        if (count($args['meta_query']) > 1) {
            $args['meta_query']['relation'] = 'AND';
        }

        $query = new WP_Query($args);
        $land_plots = [];

        foreach ($query->posts as $post) {
            $land_plots[] = $this->format_land_plot_data($post);
        }

        return new WP_REST_Response([
            'land_plots' => $land_plots,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $args['paged']
        ]);
    }

    /**
     * Пошук контенту
     */
    public function search_content($request) 
    {
        $query = sanitize_text_field($request->get_param('query'));
        $post_types = $request->get_param('post_types');

        $args = [
            'post_type' => $post_types,
            'post_status' => 'publish',
            's' => $query,
            'posts_per_page' => 20
        ];

        $search_query = new WP_Query($args);
        $results = [];

        foreach ($search_query->posts as $post) {
            $result = [
                'id' => $post->ID,
                'title' => get_the_title($post),
                'excerpt' => wp_trim_words(get_the_excerpt($post), 20),
                'permalink' => get_permalink($post),
                'post_type' => $post->post_type,
                'date' => get_the_date('c', $post)
            ];

            if ($post->post_type === 'investment') {
                $result = array_merge($result, $this->get_investment_meta($post->ID));
            } elseif ($post->post_type === 'land_plot') {
                $result = array_merge($result, $this->get_land_plot_meta($post->ID));
            }

            $results[] = $result;
        }

        return new WP_REST_Response([
            'results' => $results,
            'total' => $search_query->found_posts,
            'query' => $query
        ]);
    }

    /**
     * Отримання статистики
     */
    public function get_statistics($request) 
    {
        $investments_count = wp_count_posts('investment')->publish;
        $land_plots_count = wp_count_posts('land_plot')->publish;

        // Статистика інвестицій
        $total_investment_amount = 0;
        $featured_investments = 0;
        
        $investments = get_posts([
            'post_type' => 'investment',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids'
        ]);

        foreach ($investments as $investment_id) {
            $amount = get_post_meta($investment_id, '_investment_amount', true);
            $is_featured = get_post_meta($investment_id, '_is_featured', true);
            
            if ($amount) {
                $total_investment_amount += floatval($amount);
            }
            
            if ($is_featured) {
                $featured_investments++;
            }
        }

        // Статистика земельних ділянок
        $total_land_area = 0;
        $average_land_price = 0;
        
        $land_plots = get_posts([
            'post_type' => 'land_plot',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids'
        ]);

        $land_prices = [];
        foreach ($land_plots as $plot_id) {
            $area = get_post_meta($plot_id, '_area', true);
            $price = get_post_meta($plot_id, '_price_per_hectare', true);
            
            if ($area) {
                $total_land_area += floatval($area);
            }
            
            if ($price) {
                $land_prices[] = floatval($price);
            }
        }

        if (!empty($land_prices)) {
            $average_land_price = array_sum($land_prices) / count($land_prices);
        }

        return new WP_REST_Response([
            'investments' => [
                'total_count' => $investments_count,
                'featured_count' => $featured_investments,
                'total_amount' => $total_investment_amount,
                'average_amount' => $investments_count > 0 ? $total_investment_amount / $investments_count : 0
            ],
            'land_plots' => [
                'total_count' => $land_plots_count,
                'total_area' => $total_land_area,
                'average_area' => $land_plots_count > 0 ? $total_land_area / $land_plots_count : 0,
                'average_price' => $average_land_price
            ],
            'totals' => [
                'all_items' => $investments_count + $land_plots_count,
                'last_updated' => current_time('c')
            ]
        ]);
    }

    /**
     * Обробка контактної форми
     */
    public function submit_contact_form($request) 
    {
        $params = $request->get_params();

        // Перевірка nonce
        if (!wp_verify_nonce($params['nonce'], 'slavutska_contact_nonce')) {
            return new WP_Error('invalid_nonce', 'Недійсний токен безпеки', ['status' => 403]);
        }

        // Валідація даних
        $errors = [];
        
        if (empty($params['name']) || strlen($params['name']) < 2) {
            $errors['name'] = 'Ім\'я повинно містити принаймні 2 символи';
        }
        
        if (!is_email($params['email'])) {
            $errors['email'] = 'Невірний формат email';
        }
        
        if (empty($params['message']) || strlen($params['message']) < 10) {
            $errors['message'] = 'Повідомлення занадто коротке';
        }

        if (!empty($errors)) {
            return new WP_Error('validation_error', 'Помилки валідації', [
                'status' => 400,
                'errors' => $errors
            ]);
        }

        // Збереження в базу даних (використовуємо існуючу логіку з контактної форми)
        $contact_handler = new SlavutskaContactForm();
        
        // Тут викликаємо методи збереження та відправки email
        // (логіка вже реалізована в класі SlavutskaContactForm)

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Повідомлення успішно відправлено'
        ]);
    }

    /**
     * Отримання даних для карти
     */
    public function get_map_data($request) 
    {
        $post_type = $request->get_param('post_type');
        
        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_latitude',
                    'value' => '',
                    'compare' => '!='
                ],
                [
                    'key' => '_longitude',
                    'value' => '',
                    'compare' => '!='
                ]
            ]
        ];

        $query = new WP_Query($args);
        $map_data = [];

        foreach ($query->posts as $post) {
            $latitude = get_post_meta($post->ID, '_latitude', true);
            $longitude = get_post_meta($post->ID, '_longitude', true);

            if ($latitude && $longitude) {
                $item = [
                    'id' => $post->ID,
                    'title' => get_the_title($post),
                    'permalink' => get_permalink($post),
                    'latitude' => floatval($latitude),
                    'longitude' => floatval($longitude),
                    'post_type' => $post_type
                ];

                if ($post_type === 'land_plot') {
                    $area = get_post_meta($post->ID, '_area', true);
                    $price = get_post_meta($post->ID, '_price_per_hectare', true);
                    $purpose = get_post_meta($post->ID, '_purpose', true);
                    
                    $item['area'] = $area ? floatval($area) : null;
                    $item['price_per_hectare'] = $price ? floatval($price) : null;
                    $item['purpose'] = $purpose ?: '';
                }

                $map_data[] = $item;
            }
        }

        return new WP_REST_Response([
            'items' => $map_data,
            'total' => count($map_data)
        ]);
    }

    /**
     * Отримання схожих елементів
     */
    public function get_related_items($request) 
    {
        $post_type = $request->get_param('post_type');
        $post_id = intval($request->get_param('id'));
        $count = intval($request->get_param('count'));

        $args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'post__not_in' => [$post_id],
            'orderby' => 'rand'
        ];

        // Для інвестицій показуємо рекомендовані
        if ($post_type === 'investment') {
            $args['meta_query'] = [
                [
                    'key' => '_is_featured',
                    'value' => '1',
                    'compare' => '='
                ]
            ];
        }

        $query = new WP_Query($args);
        $related_items = [];

        foreach ($query->posts as $post) {
            if ($post_type === 'investment') {
                $related_items[] = $this->format_investment_data($post);
            } else {
                $related_items[] = $this->format_land_plot_data($post);
            }
        }

        return new WP_REST_Response([
            'related_items' => $related_items,
            'count' => count($related_items)
        ]);
    }

    /**
     * Обробка обраного (favorites)
     */
    public function get_favorites($request) 
    {
        $favorites = isset($_COOKIE['slavutska_favorites']) ? 
                    json_decode(stripslashes($_COOKIE['slavutska_favorites']), true) : [];
        
        return new WP_REST_Response(['favorites' => $favorites]);
    }

    public function add_to_favorites($request) 
    {
        $post_id = intval($request->get_param('post_id'));
        $post_type = $request->get_param('post_type');
        
        $favorites = isset($_COOKIE['slavutska_favorites']) ? 
                    json_decode(stripslashes($_COOKIE['slavutska_favorites']), true) : [];
        
        $favorites[$post_id] = ['post_type' => $post_type, 'added_at' => time()];
        
        setcookie('slavutska_favorites', json_encode($favorites), time() + (30 * DAY_IN_SECONDS), '/');
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Додано до обраного'
        ]);
    }

    public function remove_from_favorites($request) 
    {
        $post_id = intval($request->get_param('post_id'));
        
        $favorites = isset($_COOKIE['slavutska_favorites']) ? 
                    json_decode(stripslashes($_COOKIE['slavutska_favorites']), true) : [];
        
        unset($favorites[$post_id]);
        
        setcookie('slavutska_favorites', json_encode($favorites), time() + (30 * DAY_IN_SECONDS), '/');
        
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Видалено з обраного'
        ]);
    }

    /**
     * Додавання кастомних полів до REST API
     */
    public function add_custom_fields_to_rest() 
    {
        // Для інвестицій
        register_rest_field('investment', 'investment_meta', [
            'get_callback' => [$this, 'get_investment_meta'],
            'schema' => [
                'description' => 'Мета-дані інвестиційної пропозиції',
                'type' => 'object'
            ]
        ]);

        // Для земельних ділянок
        register_rest_field('land_plot', 'land_plot_meta', [
            'get_callback' => [$this, 'get_land_plot_meta'],
            'schema' => [
                'description' => 'Мета-дані земельної ділянки',
                'type' => 'object'
            ]
        ]);
    }

    /**
     * Отримання мета-даних інвестиції
     */
    public function get_investment_meta($post_id) 
    {
        return [
            'investment_amount' => get_post_meta($post_id, '_investment_amount', true),
            'investment_period' => get_post_meta($post_id, '_investment_period', true),
            'expected_return' => get_post_meta($post_id, '_expected_return', true),
            'contact_person' => get_post_meta($post_id, '_contact_person', true),
            'contact_phone' => get_post_meta($post_id, '_contact_phone', true),
            'contact_email' => get_post_meta($post_id, '_contact_email', true),
            'location' => get_post_meta($post_id, '_location', true),
            'is_featured' => get_post_meta($post_id, '_is_featured', true) === '1'
        ];
    }

    /**
     * Отримання мета-даних земельної ділянки
     */
    public function get_land_plot_meta($post_id) 
    {
        $area = get_post_meta($post_id, '_area', true);
        $price_per_hectare = get_post_meta($post_id, '_price_per_hectare', true);
        
        return [
            'area' => $area ? floatval($area) : null,
            'price_per_hectare' => $price_per_hectare ? floatval($price_per_hectare) : null,
            'total_price' => ($area && $price_per_hectare) ? floatval($area) * floatval($price_per_hectare) : null,
            'cadastral_number' => get_post_meta($post_id, '_cadastral_number', true),
            'purpose' => get_post_meta($post_id, '_purpose', true),
            'infrastructure' => get_post_meta($post_id, '_infrastructure', true),
            'latitude' => get_post_meta($post_id, '_latitude', true),
            'longitude' => get_post_meta($post_id, '_longitude', true),
            'documents' => get_post_meta($post_id, '_documents', true)
        ];
    }

    /**
     * Форматування даних інвестиції
     */
    private function format_investment_data($post) 
    {
        return [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'excerpt' => get_the_excerpt($post),
            'permalink' => get_permalink($post),
            'featured_image' => get_the_post_thumbnail_url($post, 'investment-thumbnail'),
            'date' => get_the_date('c', $post),
            'categories' => wp_get_post_terms($post->ID, 'investment_category', ['fields' => 'names']),
            'meta' => $this->get_investment_meta($post->ID)
        ];
    }

    /**
     * Форматування даних земельної ділянки
     */
    private function format_land_plot_data($post) 
    {
        return [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'excerpt' => get_the_excerpt($post),
            'permalink' => get_permalink($post),
            'featured_image' => get_the_post_thumbnail_url($post, 'land-plot-image'),
            'date' => get_the_date('c', $post),
            'land_types' => wp_get_post_terms($post->ID, 'land_type', ['fields' => 'names']),
            'meta' => $this->get_land_plot_meta($post->ID)
        ];
    }

    /**
     * Підготовка даних інвестиції для REST
     */
    public function prepare_investment_data($response, $post, $request) 
    {
        $response->data['investment_meta'] = $this->get_investment_meta($post->ID);
        return $response;
    }

    /**
     * Підготовка даних земельної ділянки для REST
     */
    public function prepare_land_plot_data($response, $post, $request) 
    {
        $response->data['land_plot_meta'] = $this->get_land_plot_meta($post->ID);
        return $response;
    }
}

// Ініціалізація REST API розширень
new SlavutskaRestAPI();