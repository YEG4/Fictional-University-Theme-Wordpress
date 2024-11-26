<?php
require get_theme_file_path('/includes/search-route.php');
function university_custom_rest()
{
    register_rest_field('post', 'authorName', args: array(
        'get_callback' => function () {
            return get_the_author();
        }
    ));
}

add_action('rest_api_init', 'university_custom_rest');


function pageBanner($args = NULL)
{
    if (!isset($args['title']))
        $args['title'] = get_the_title();
    if (!isset($args['subtitle']))
        $args['subtitle'] = get_field('page_banner_subtitle');
    if (!isset($args['photo']))
        if (get_field('page_banner_background_image') and !is_archive() and !is_home())
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        else
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php
        echo $args['photo']; ?>)">
        </div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
    <?php
}
function university_files()
{
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style("university_main_styles", get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style("university_extra_styles", get_theme_file_uri('/build/index.css'));
    wp_enqueue_style("font-awesome", '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style(
        "google-font",
        '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i'
    );


    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
    ));
}
add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerMenuOne', 'Footer Menu 1');
    register_nav_menu('footerMenuTwo', 'Footer Menu 2');
    add_theme_support('title-tag'); // gives wp the control to dynamically change <title></title> tag to be the current page, archive,
    add_theme_support('post-thumbnails'); // allows us to add thumbnails to our posts (featured image 1-1 relationship)
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorProtrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

/* This function modify the query as we like before excuting it in the database. */
function university_adjust_queries($query)
{
    $today = date('Ymd');
    //$query->set('posts_per_page', '1'); // This will affect every query regardless of post type or url slug and will
// affect admin dashboard aswell.
/* if you're not in the admin dashboard and only on the event archive and only if it's the main wordpress query and not
a custom query*/
    if (!is_admin() and is_post_type_archive('event') and is_main_query()) {
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set(
            'meta_query',
            array(
                [
                    'key' => 'event_date', // custom field
                    'compare' => '>=',
                    'value' => $today,
                    'type' => 'numeric' // value's type that i'm comparing
                ]
            ),
        );
    }

    if (!is_admin() and is_post_type_archive('program') and is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', '-1'); // infinity. display all programs
    }
}

/* This hook(pre_get_posts) invokes the function before sending query to database*/
add_action('pre_get_posts', 'university_adjust_queries');

?>