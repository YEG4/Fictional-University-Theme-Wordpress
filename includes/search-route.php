<?php

add_action('rest_api_init', 'university_register_search');
function university_register_search()
{
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE, // returns a 'GET' string
        'callback' => 'university_search_results', // a function that will be called when the url is visited
    ));
    /* They array parameter specifies what should happen when someone visits this url (localhost/plugin/wp-json/university/v1/search) */
}



function university_search_results($request)
{
    /* WP will take care of the conversion from php data structure to JSON. */

    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'event'),
        's' => sanitize_text_field($request->get_param('term'))
    ));

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
    );
    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();
        if (get_post_type() == 'post' or get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
            ));
        }
        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'title' => get_the_title(),
            ));
        }
        if (get_post_type() == 'program') {
            array_push($results['programs'], array(
                'title' => get_the_title(),
            ));
        }
        if (get_post_type() == 'event') {
            array_push($results['events'], array(
                'title' => get_the_title(),
            ));
        }

    }
    return $results;
}