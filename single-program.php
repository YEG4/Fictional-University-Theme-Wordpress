<?php
get_header();
while (have_posts()) {
    the_post();
    pageBanner();
    ?>

<div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
            <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program') ?>"><i
                    class="fa fa-home" aria-hidden="true"></i> All Programs</a> <span class="metabox__main">
                <?php the_title() ?>
            </span>
        </p>
    </div>
    <div class="generic-content">
        <?php the_content() ?>
    </div>
    <?php /*
$relatedEvents = new WP_Query(array(
'post_type' => 'event',
'meta_query' => array(
[
"key" => ""
],
)
));*/
        ?>
    <?php
        $relatedProfessors = new WP_Query(array(
            'post_type' => 'professor',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(
                [
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"'
                ]
            )
        )); ?>
    <?php if ($relatedProfessors->have_posts()): ?>
    <hr class="section-break">
    <h2 class="headline headline--medium"><?php the_title(); ?> Professors</h2>
    <ul class="professor-cards">
        <?php
                while ($relatedProfessors->have_posts()) {
                    $relatedProfessors->the_post();
                    ?>


        <li class="professor-card__list-item">
            <a class="professor-card" href="<?php the_permalink(); ?>">
                <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>" alt="">
                <span class="professor-card__name"><?php the_title(); ?></span>
            </a>
        </li>
        <?php }
                wp_reset_postdata();
                ?>
    </ul>
    <?php endif; ?>


    <?php $today = date('Ymd');
        $eventPosts = new WP_Query(array(
            'post_type' => 'event',
            'posts_per_page' => 2,
            'meta_key' => 'event_date',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                [ // filterations
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => $today,
                    'type' => 'numeric'
                ],
                [
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"'
                ]
            )
        )); ?>
    <?php if ($eventPosts->have_posts()): ?>
    <hr class="section-break">
    <h2 class="headline headline--medium">Upcoming <?php echo the_title() ?> Event(s)</h2>
    <?php
            while ($eventPosts->have_posts()) {
                $eventPosts->the_post();
                ?>

    <ul class="min-list link-list">


        <li>
            <?php get_template_part('template-parts/event'); ?>
        </li>
        <?php }
            wp_reset_postdata();
            ?>
    </ul>
    <?php endif; ?>
</div>
<?php }
get_footer();
?>