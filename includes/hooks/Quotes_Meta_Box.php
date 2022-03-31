<?php


add_filter('gutenberg_can_edit_post', '__return_false', 10);


add_action( 'add_meta_boxes', 'dev_challenge_add_meta_box');


/**
 * The function responsible for creating the actual meta box.
 *
 * @since    0.2.0
 */

 function dev_challenge_add_meta_box() {

    add_meta_box(
        'quotes-post',
        __('Citas', 'dev-challenge'),
        'dev_challenge_display_meta_box',
        'post',
        'normal',
        'default'
    );

}

/**
 * Renders the content of the meta box.
 *
 */
 function dev_challenge_display_meta_box() {

    $content = get_post_meta( get_the_ID(), 'dev_challenge_quotes', true );

    $editor_id = 'quotes';

    wp_editor( $content, $editor_id);

    // Add a nonce field for security
    wp_nonce_field( 'quotes_save', 'quotes_nonce' );   
}
 

add_action( 'save_post', 'dev_challenge_save_post');


// Sanitizes and save the information associated with this post.


function dev_challenge_save_post( $post_id ) {
 
    /* If we're not working with a 'post' post type or the user doesn't have permission to save,
     * then we exit the function.
     */
    if ( ! is_valid_post_type() || ! user_can_save( $post_id, 'quotes_nonce', 'quotes_save' ) ) {
        return;
    }

    if ( ! empty( $_POST['quotes'] ) ) {
 
        //Sanitizes content for allowed HTML tags for post content.
        $content = wp_kses_post($_POST['quotes']);
        
        
        update_post_meta( $post_id, 'dev_challenge_quotes', $content );
 
    }else {
     
        if ( '' !== get_post_meta( $post_id, 'dev_challenge_quotes', true ) ) {
            delete_post_meta( $post_id, 'dev_challenge_quotes' );
        }
         
    }
 
}

