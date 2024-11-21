<?php

function university_files()
{
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style("university_main_styles", get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style("university_extra_styles", get_theme_file_uri('/build/index.css'));
    wp_enqueue_style("font-awesome", '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style("google-font", '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
}
add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerMenuOne', 'Footer Menu 1');
    register_nav_menu('footerMenuTwo', 'Footer Menu 2');
    add_theme_support('title-tag'); // gives wp the control to dynamically change <title></title> tag to be the current page, archive,  
}

add_action('after_setup_theme', 'university_features');

/* This function modify the query as we like before excuting it in the database. */
function university_adjust_queries($query)
{
    $today = date('Ymd');
    //$query->set('posts_per_page', '1'); // This will affect every query regardless of post type or url slug and will affect admin dashboard aswell.
    /* if you're not in the admin dashboard and only on the event archive and only if it's the main wordpress query and not a custom query*/
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
