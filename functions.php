<?php

function enqueue_parent_theme_styles()
{
    // wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_parent_theme_styles');


function enqueue_custom_child_scripts() {
    wp_enqueue_style('globals-style', get_stylesheet_directory_uri() . '/css/globals.css', array(), '1.0', 'all');
    wp_enqueue_style('styleguide-style', get_stylesheet_directory_uri() . '/css/styleguide.css', array(), '1.0', 'all');
    wp_enqueue_style('style-style', get_stylesheet_directory_uri() . '/css/style.css', array(), '1.0', 'all');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css', array(), '6.0.0', 'all');
    wp_enqueue_script('vue3-script', 'https://unpkg.com/vue@3.3.8/dist/vue.global.prod.js', array(), null, true);

    wp_enqueue_style('litepicker-css', 'https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css', array(), null, 'all');
    wp_enqueue_script('litepicker-js', 'https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js', array('vue3-script'), null, true);

    $api_key = 'AIzaSyBY1nLDcGY1NNgV89rnDR8jg_eBsQBJ39E'; 
    wp_enqueue_script('multi-step', get_stylesheet_directory_uri() . "/js/multi-step-form.js", array('litepicker-js'), null, true);
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=places", array('multi-step'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_child_scripts');




// add shortcode to display form
add_shortcode('multi-step-from-display', 'custom_form_display_func');
function custom_form_display_func($atts)
{
    ob_start();
    include_once(get_stylesheet_directory() . '/form-content.php');
    return ob_get_clean();
}

function custom_post_type_price_ranges() {
    $labels = array(
        'name'               => 'Price Ranges',
        'singular_name'      => 'Price Range',
        'menu_name'          => 'Price Ranges',
        'add_new'            => 'Add New',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'price-ranges' ),
        'supports'           => array('custom-fields'),
        'menu_icon'          => 'dashicons-money', 
        'show_in_rest'       => true,

    );

    register_post_type( 'price_range', $args );
}
add_action( 'init', 'custom_post_type_price_ranges' );
