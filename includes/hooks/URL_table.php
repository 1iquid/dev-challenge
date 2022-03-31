<?php


add_action( 'init', 'register_urls_table', 1 );
add_action( 'switch_blog', 'register_urls_table' );
 

 // Function used by wp cron to check links found in post content

function check_urls(){

	global $wpdb;

	//Get unverified posts

	$args = array(
	    'post_type'  => 'post',
	    'posts_per_page' => -1,    
	    'meta_query' => array(
	        array(
	            'key'   => 'check_urls',
	            'compare' => 'NOT EXISTS'
	        )
	    )
	);

	$postslist = get_posts($args);

	$dom = new DomDocument();

	foreach($postslist as $post) {
		//delete_post_meta($post->ID, 'check_urls');		

		$post_content = $post->post_content;
		//Convert content to HTML Dom		
		@$dom->loadHTML($post_content);
		
		// get all tags 'a'
		foreach ($dom->getElementsByTagName('a') as $item) {
			$href = $item->getAttribute('href');

			if(!empty($href)){
				// Get status link
				$status = url_status($href);

				//If the link does not meet the conditions, we record its status in the url table

				if($status !== null){
					$inserted = $wpdb->insert($wpdb->urls,
						array(
							'url' => $href,
							'status' => $status,
							'origin' => $post->ID
						),
						array(
							'%s',
							'%s',
							'%d'
						)
					);


					//We save a metadata that tells us if the post has already been checked
					if($inserted){
						update_post_meta($post->ID, 'check_urls', 1); 
					}
				}
			}		   
		}
		
	}
}

// Create an option in the administration menu to see the data of the broken links
function dev_challenge_register_my_custom_menu_page(){

	add_menu_page( 
		__('Enlaces Rotos', 'dev-challenge'),
		__('Enlaces Rotos', 'dev-challenge'),
		'manage_options',
		'broken-links-plugin',
		'broken_links_table', 
		'dashicons-admin-links',
		85
	);
}


add_action('admin_menu', 'dev_challenge_register_my_custom_menu_page');


//Shows the view with the data table of the links found
function broken_links_table() {
	
	$wp_list_table = new Link_List_Table();
	$wp_list_table->prepare_items(); ?>

	<div class="wrap">
		<h2><?php _e('Enlaces Rotos', 'dev-challenge'); ?></h2>
		<form id="links-list-form" method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />			
			<?php $wp_list_table->display(); ?>					
		</form>
	</div><?php
}