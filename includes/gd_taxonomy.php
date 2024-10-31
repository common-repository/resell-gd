<?php
/**
Name: GoDaddy Resseller core functions
Version: 1.1
Author: Larry Wakeman
Author URI: http://www.larrywakeman.com/
*/
/**
** Copyright &copy; Larry Wakeman - 2015
**
** All rights reserved. No part of this publication may be reproduced, stored in a retrieval system, 
** or transmitted in any form or by any means without the prior written permission of the copyright
** owner and must contain the avove copyright notice.
**
** Permission is granted under GNU General Public License (GPL) http://opensource.org/licenses/GPL-3.0.
*/

class gd_taxonomy {
	
/**
* class variables.
*/
  // this variable will hold url to the plugin  
  protected $plugin_url;
  protected $plugin_dir;
    
  // Option class
  protected $option_class;

  // Custom fields
  protected $meta_data;

/**
* class constructpr.
*/
	function __construct() {

		// Description of custom columns		
	  $this->meta_data = array(
	  												 array('field' => 'product-id', 'type' => 'number, readonly', 'title' => 'Product ID',),
														 array('field' => 'product-name', 'type' => 'text, readonly', 'title' => 'Product Name',),
														 array('field' => 'product-group-section-id', 'type' => 'number, readonly', 'title' => 'Section ID',),
														 array('field' => 'product-group-section-name', 'type' => 'text, readonly', 'title' => 'Section Name',),
														 array('field' => 'product-export-group', 'type' => 'text, readonly', 'title' => 'Product Export Group',),
														 array('field' => 'retail-min', 'type' => 'price, readonly', 'title' => 'Retail Min',),
														 array('field' => 'retail-max', 'type' => 'price, readonly', 'title' => 'Retail Max',),
														 array('field' => 'retail-default', 'type' => 'price, readonly', 'title' => 'Retail Default',),
														 array('field' => 'retail-price', 'type' => 'price', 'title' => 'Retail Price',),
														 array('field' => 'sale-price', 'type' => 'price', 'title' => 'Sale Price',),
														 array('field' => 'sale-start', 'type' => 'date', 'title' => 'Sale Start',),
														 array('field' => 'sale-end', 'type' => 'date', 'title' => 'Sale End',),
											 );
											
	}

/**
* class initialization.
*/
	function initialize ($plugin_url, $plugin_dir, $option_class) {
		$this->plugin_url = $plugin_url;
		$this->plugin_dir = $plugin_dir;
		$this->option_class = $option_class;
		
    // add custom post type
		add_action( 'init', array( $this, 'create_posttype' ));
		add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ));
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_filter("manage_edit-gd_product_columns", array( $this, "gd_product_edit_columns"));
		add_action("manage_posts_custom_column", array( $this, "gd_product_custom_columns"));
		add_action( 'restrict_manage_posts', array( $this, 'gd_product_manage_posts' ));
		add_filter( 'posts_where' , array( $this, 'gd_product_filter_posts' ) );
		add_filter( 'manage_edit-gd_product_sortable_columns' , array( $this, 'edit_posts_orderby' ) );

	}
  
/**
* Set up Products Grid
*/
  function gd_product_edit_columns($columns) {
	  // remove wp-seo columns
	  if (isset($columns['wpseo-score'])) unset ($columns['wpseo-score']);
	  if (isset($columns['wpseo-title'])) unset ($columns['wpseo-title']);
	  if (isset($columns['wpseo-metadesc'])) unset ($columns['wpseo-metadesc']);
	  if (isset($columns['wpseo-focuskw'])) unset ($columns['wpseo-focuskw']);
	  // add gd-product columns 
		$columns['product-id'] = __("Product ID");
		$columns['retail-price'] = __("Retail Price");
		$columns['sale-price'] = __("Sale Price");
		$columns['retail-default'] = __("Default Price");
		$columns['product-export-group'] = __("Product Export Group");
		$columns['product-group-section-name'] = __("Section Name");
		$columns['product-group-section-id'] = __("Section ID");
		return $columns;
  }
  
/**
* Populate custom columns
*/
  function gd_product_custom_columns($column) {
		global $post;
		switch( $column ) {
			case "product-id":
				echo get_post_meta( $post->ID, 'product-id', true);
				break;
			case "retail-price":
				echo get_post_meta( $post->ID, 'retail-price', true);
				break;
			case "sale-price":
				echo get_post_meta( $post->ID, 'sale-price', true);
				break;
			case "product-export-group":
				echo get_post_meta( $post->ID, 'product-export-group', true);
				break;
			case "product-group-section-name":
				echo get_post_meta( $post->ID, 'product-group-section-name', true);
				break;
			case "product-group-section-id":
				echo get_post_meta( $post->ID, 'product-group-section-id', true);
				break;
			case "retail-default":
				echo get_post_meta( $post->ID, 'retail-default', true);
				break;
		}
  }
  
/**
* Set up custom filters
*/
  function gd_product_manage_posts($request) {
	  global $typenow;
	  global $post;
	  global $wpdb;
	  if( $typenow == 'gd_product' ) {
		  $tax_obj = get_taxonomy('gd_product_taxonomy');
		  $tax_name = $tax_obj->labels->name;
		  $terms = get_terms('gd_product_taxonomy', array( 'hide_empty' => 0 ));
		  foreach ($terms as $term) {
				$values = $wpdb->get_col("SELECT DISTINCT meta_value FROM ".$wpdb->postmeta." WHERE meta_key = '".$term->name."' ORDER BY meta_value");
			  echo "<select name='$term->name' id='$term->term_id' class='postform'>";
			  echo "<option value=''>Show All $term->name</option>";
			  foreach ($values as $value) { 
				  echo '<option value="'.$value.'"';
				  if ($_GET[$term->name] == $value) echo ' selected="selected"';
				  echo '>' . $value.'</option>'; 
			  }
			  echo "</select>";
		  }
	  }
    return $request;
  }
  
/**
* Do custom filters
*/
  function gd_product_filter_posts($where) {
	  global $wpdb;
	  if( is_admin() ) {
		  if ( isset( $_GET['product-export-group'] ) && !empty($_GET['product-export-group'])) {
				$where .= " AND ID IN (SELECT post_id FROM ".$wpdb->postmeta;
				$where .=  					" WHERE meta_key='product-export-group'";
				$where .=  					" AND meta_value = '".$_GET['product-export-group']."')";
			}
		  if ( isset( $_GET['product-group-section-name'] ) && !empty($_GET['product-group-section-name'])) {
				$where .= " AND ID IN (SELECT post_id FROM ".$wpdb->postmeta;
				$where .=  					" WHERE meta_key='product-group-section-name'";
				$where .=  					" AND meta_value = '".$_GET['product-group-section-name']."')";
			}
	  }
	  return $where;
  }
  
/**
* Set up custom sorts
*/
	function edit_posts_orderby($orderby_statement) {
		$orderby_statement['product-id'] = 'product-id';
		return $orderby_statement;
	}
	
/**
* Create Post Types
*/
	function create_posttype() {
				
	  register_post_type( 'gd_product',
	    array(
	      'labels' => array(
	        'name' => __( 'GoDaddy Products' ),
	        'singular_name' => __( 'GoDaddy Product' )
	      ),
	      'public' => true,
	      'has_archive' => false,
	      'menu_icon' => $this->plugin_url.'/images/GoDaddy.png',
	      'capabilities' => array('create_posts' => true,
	      												'edit_posts' => true,
	      												'read_posts' => true,
	      												'delete_posts' => true,),
	      'map_meta_cap' => true,
	    )
	  );
	  $this->create_taxonomy();
  }
  
/**
* Create Taxonomy
*/
	function create_taxonomy() {
		$labels = array(
		      'name'              => _x( 'GoDaddy Products', 'taxonomy general name' ),
		      'singular_name'     => _x( 'GoDaddy Product', 'taxonomy singular name' ),
		      'search_items'      => __( 'Search GoDaddy Products' ),
		      'all_items'         => __( 'All GoDaddy Products' ),
		      'edit_item'         => __( 'Edit GoDaddy Product' ),
		      'update_item'       => __( 'Update GoDaddy Product' ),
		      'add_new_item'      => __( 'Add New GoDaddy Product' ),
		      'new_item_name'     => __( 'New GoDaddy Product Name' ),
		      'menu_name'         => __( 'GoDaddy Products' ),
		  );
    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => false,
        'show_admin_column' => false,
        'query_var'         => false,
        'rewrite'           => array( 'slug' => false ),
    );
  	register_taxonomy(
			'gd_product_taxonomy',
			array('gd_product'),
			$args
		);
		Register_Taxonomy_For_Object_Type( 'gd_product_taxonomy', 'gd_product' );
		wp_insert_term( 'product-export-group', 'gd_product_taxonomy');
		wp_insert_term( 'product-group-section-name', 'gd_product_taxonomy');
	}
	
/**
* Create Meta Boxes
*/
  function meta_boxes ($post_type) {

	  if ($post_type == 'gd_product') {
			foreach ($this->meta_data as $item) {
				$this->add_meta ( $item );
			}
		}
		
	}
  
/**
* Add Meta Boxes
*/
	function add_meta ( $item ) {
		$args = array('field' => $item['field'], 'title' => $item['title'], 'type' => $item['type'], );
		add_meta_box( 'gd_product-'.$item['field'],
									$item['title'],
									array($this, 'display_field'),
									'gd_product',
									'advanced',
									'high',
									$args
								);
	}
	
/**
* Display Meta Boxes
*/
	function display_field ($post, $item) {
		$value = '';
		$value = get_post_meta( $post->ID, $item['args']['field'], true );
		echo '<table class="form-table"><tr><th scope="row">';
		echo '<label for="gd_product-'.$item['args']['field'].'">';
		_e( $item['args']['title'] );
		echo '</label> </th><td>';
		echo '<input type="text" style= "width: 99%;" class="gd_product-'.$item['args']['type'].'" id="gd_product-'.$item['args']['field'].'" name="'.$item['args']['field'].'" value="' . esc_attr( $value ) . '" size="25" />';
		if ($item['args']['type'] == 'date') 
			echo 'Format as m/d/yyyy, as Godaddy requires';
		echo '</td></tr></table>';
		
	}
	
/**
* Save Meta Data
*/
	public function save_post( $post_id ) {
		if (isset($_POST['post_type']) && $_POST['post_type'] == 'gd_product') {
			foreach ($this->meta_data as $item) {
				update_post_meta( $post_id, $item['field'], sanitize_text_field( $_POST[$item['field']] ) );
			}
		}
		return $post_id;
	}
	
}
