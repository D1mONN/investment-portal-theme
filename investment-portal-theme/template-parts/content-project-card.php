<?php
/**
 * Шаблон картки інвестиційного проєкту
 *
 * @package SlavutaInvest
 */

$main_image = get_field('main_image');
$investment_amount = get_field('investment_amount');
$project_status_label = get_field('project_status');
$project_status_value = get_field_object('project_status')['value'];
$project_categories = get_the_terms(get_the_ID(), 'project_category');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('project-card swiper-slide'); ?>>
    <div class="project-card-inner">
        <?php if ($main_image) : ?>
            <div class="project-card-image">
                <a href="<?php the_permalink(); ?>">
                    <img src="<?php echo esc_url($main_image['sizes']['project-thumb']); ?>" 
                         alt="<?php echo esc_attr($main_image['alt'] ?: get_the_title()); ?>"
                         loading="lazy">
                </a>
                <?php if ($project_status_label) : ?>
                    <span class="project-status status-<?php echo esc_attr($project_status_value); ?>">
                        <?php echo esc_html($project_status_label); ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="project-card-content">
            <?php if ($project_categories && !is_wp_error($project_categories)) : ?>
                <div class="project-categories">
                    <a href="<?php echo esc_url(get_term_link($project_categories[0])); ?>" class="project-category">
                        <?php echo esc_html($project_categories[0]->name); ?>
                    </a>
                </div>
            <?php endif; ?>

            <h3 class="project-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <p class="project-card-excerpt">
                <?php echo wp_trim_words(get_field('short_description'), 15); ?>
            </p>

            <div class="project-card-footer">
                <?php if ($investment_amount) : ?>
                    <div class="project-investment">
                        <span class="investment-label"><?php esc_html_e('Інвестиції:', 'slavuta-invest'); ?></span>
                        <span class="investment-amount"><?php echo number_format($investment_amount, 0, ',', ' '); ?> грн</span>
                    </div>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="btn-read-more">
                    <?php esc_html_e('Детальніше', 'slavuta-invest'); ?>
                </a>
            </div>
        </div>
    </div>
</article>