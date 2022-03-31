<?php


// Class to use WordPress admin tables with our custom database table

if (!class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Link_List_Table extends WP_List_Table {

	//The columns of the table are registered
	public function get_columns() {			

		$table_columns = array(
			'cb'		=> '<input type="checkbox" />', // to display the checkbox.			 
		  'url' => __('URL', 'dev-challenge'),
		  'status' => __('Estado','dev-challenge'),
		  'origin' => __('Origen', 'dev-challenge')
		);	

		return $table_columns;		   
	}	

	// In case there are no records to display

	public function no_items() {
		_e( 'No hay enlaces ', 'dev-challenge');
	}



	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */


	//Query, filter data, handle sorting, pagination, and any other data-manipulation required prior to rendering
  public function prepare_items() {

  	$sortable = $this->get_sortable_columns();
    // code to handle bulk actions
	  $columns = $this->get_columns();  
    //used by WordPress to build and fetch the _column_headers property
    $this->_column_headers = $this->_column_headers = array( $columns,array(), $sortable);	

    $this->handle_table_actions();

    $table_data = $this->fetch_table_data();

    $this->items = $table_data;
  }


  public function fetch_table_data() {

  	global $wpdb;
    $wpdb_table = $wpdb->urls;		
    $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'url_id';
    $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';

    $query = "SELECT * FROM $wpdb_table ORDER BY $orderby $order";

    //Number of elements in your table?
	  $totalitems = $wpdb->query($query); //return the total number of affected rows
	  //How many to display per page?
	  $perpage = 10;
	  //Which page is this?
	  $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
	  //Page Number
	  if(empty($paged) || !is_numeric($paged) || $paged <= 0 ){
	   	$paged = 1;
	  } 

	  //How many pages do we have in total?
	  $totalpages = ceil($totalitems / $perpage); //adjust the query to take pagination into account

	  if(!empty($paged) && !empty($perpage)){
	   	$offset = ($paged-1) * $perpage;
	 		$query.=' LIMIT '.(int) $offset. ',' .(int)$perpage;
	  }

	  $this->set_pagination_args( array(
	  	"total_items" => $totalitems,
      "total_pages" => $totalpages,
      "per_page" => $perpage,
     ));


    // query output_type will be an associative array with ARRAY_A.
    $query_results = $wpdb->get_results($query, ARRAY_A  );
      
    // return result array to prepare_items.
    return $query_results;		
  }    

  
	public function column_default( $item, $column_name ) {				
		switch ( $column_name ) {			
			case 'url':
				return '<a href="' . $item[$column_name] . '" target="_blank">' . $item[$column_name] . '</a>';
				break;
			case 'status':
				return '<b>' . $item[$column_name] .'</b>';
				break;
			case 'origin':
				return '<a href="' . get_edit_post_link($item[$column_name]) . '">'  . get_the_title($item[$column_name]) . '</a>';
			default:
			  return $item[$column_name];
		}
	}

	// Get value for checkbox column.
	protected function column_cb( $item ) {
		return "<input type='checkbox' name='links[]' id='{$item['url_id']}' value='{$item['url_id']}' />";
	}

	protected function get_sortable_columns() {
		/*
		 * specify which columns should have the sort icon.	
		 */
		$sortable_columns = array (
				'origin' => array('origin', false),
				'status' => array('status', false)
		);

		return $sortable_columns;
	}	

	//Returns an associative array containing the bulk action.
	public function get_bulk_actions() {
		 $actions = array(
			 'delete' => __('Eliminar', 'dev-challenge')
		 );
		 return $actions;
	}


	public function handle_table_actions() {

		global $wpdb;

		/*
		 * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
		 * action - is set if checkbox from top-most select-all is set, otherwise returns -1
		 * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
		 */    
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'delete' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );	
			/*
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );	 
			 */

			if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ) { // verify the nonce.
				$this->invalid_nonce_redirect();
			}else {

				$ids = implode( ',', array_map( 'absint', $_GET['links']));

				$wpdb->query("DELETE FROM $wpdb->urls WHERE url_id IN($ids)");
			}
		}
	}

}