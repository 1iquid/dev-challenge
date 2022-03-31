<?php

// Shortcode that allows you to display the information about the quotes of the current post or a specific post passed as an attribute.

function quotes_shortcode($atts, $content = null){  

	global $post;

	extract(shortcode_atts( array('post_id' => $post->ID), $atts));


	// Get post quotes
	$quotes_content = get_post_meta($post_id, 'dev_challenge_quotes', true);


	// Build the html to display the quotes
	$html = '<div class="quotes-container">' . $quotes_content . '</div>'; 

	return $html;
}
   
 add_shortcode('sc_quotes', 'quotes_shortcode');