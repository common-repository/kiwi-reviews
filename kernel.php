<?php
add_action('admin_menu', 'kiwi_reviews_admin_menu_add', 20);
add_action('admin_post_submit-form', 'kiwi_reviews_generate_shortcode'); // If the user is logged in

function kiwi_reviews_admin_menu_add()
{
    add_menu_page(
      'Kiwi Reviews',
      'Kiwi Reviews',
      'administrator',
      'kiwi-reviews',
      'kiwi_places',
      'dashicons-format-quote'
  );
}

if (! is_admin()) {
    add_shortcode('kiwi-review', 'kiwi_reviews');
}

function kiwi_places()
{
    require_once 'parse-places.php';
}

function kiwi_reviews($atts, $content = null)
{
    extract(shortcode_atts(array( 'id' => '' ), $atts));
    global $place_id;
    if (! empty($atts['place-id'])) {
        $place_id = $atts['place-id'];
    } else {
        $place_id = '';
    }
    require 'parse-review.php';
}

function kiwi_reviews_generate_shortcode($id)
{
    add_shortcode('kiwi-review', 'kiwi_reviews', $id);
}
