<?php
/**
Name: Resell GD admin functions
Version: 1.3
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

class gd_admin {
	
/**
* class variables.
*/
  // this variable will hold url to the plugin  
  protected $plugin_url;
  protected $plugin_dir;
    
  // Option class
  protected $option_class;

  // Description Color
  protected $desc_color;

/**
* class constructpr.
*/
	function __construct() {
		$this->desc_color = '#000';
	}

/**
* class initialization.
*/
	function initialize ($plugin_url, $plugin_dir, $option_class) {
		$this->plugin_url = $plugin_url;
		$this->plugin_dir = $plugin_dir;
		$this->option_class = $option_class;
		
    // add the options page
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
		// ajax support
		add_action( 'admin_footer', array( $this, 'my_action_javascript') );
		add_action( 'wp_ajax_my_action', array( $this, 'my_action_my_action' ));

	}

/**
* Create Admin menus
*/
	function admin_menu () {
	add_options_page( 'Resell GD Options','Resell GD','manage_options','godday_options', array( &$this, 'godday_page' ) );
    add_submenu_page('edit.php?post_type=gd_product','Shortcode Generator' ,'Shortcode Generator', 'activate_plugins', 'gd_product-shortcode-generator', array(&$this, 'shortcode_generator'));
    add_submenu_page('edit.php?post_type=gd_product','Product Import' ,'Product Import', 'activate_plugins', 'gd_product-import-products', array(&$this, 'product_import'));
    add_submenu_page('edit.php?post_type=gd_product','Pricing Import' ,'Pricing Import', 'activate_plugins', 'gd_product-import-prices', array(&$this, 'pricing_import'));
    add_submenu_page('edit.php?post_type=gd_product','Pricing Export' ,'Pricing Export', 'activate_plugins', 'gd_product-export-prices', array(&$this, 'pricing_export'));
	}
	
/**
* Settings Page 
*/
	function  godday_page () {
		GLOBAL $wpdb;
?>
<div id="wpbody-content" tabindex="0" aria-label="Main content">
	<div class="wrap">
		<h2>Resell GD Settings</h2>
<?php
    if (isset($_POST['submit'])) {
	    if (isset($_POST['purge_products'])) {
 				$wpdb->query("DELETE  FROM $wpdb->posts WHERE `post_type` LIKE 'gd_product'");
				$wpdb->query("DELETE FROM $wpdb->postmeta WHERE `post_id` NOT IN (SELECT ID FROM dr_posts)");
		    unset ($_POST['purge_products']);
			}
	    unset ($_POST['submit']);
      if (update_option($this->option_class, $_POST)) echo '<h3>'.__('Options saved').'</h3>';;
    }
    $options = get_option($this->option_class);
    if (!$options) $options = array(
    															'program_id' => '',
    															'search_url' => '',
    															'cart_url' => '',
    															);
?>
		<form novalidate="novalidate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="program_id"><?php echo __('GoDaddy Program ID'); ?></label></th>
					<td><input id="program_id" class="regular-text" type="text" value="<?php echo $options['program_id']; ?>" name="program_id">
					<p style="color: <?php echo $this->desc_color; ?>;" id="program_id-description" class="description"><?php echo __('From Reseller Dashboard->Settings->Contact Information.'); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="search_url"><?php echo __('Domain Search URL'); ?></label></th>
					<td><input id="search_url" class="regular-text" type="text" value="<?php echo $options['search_url']; ?>" name="search_url">
					<p style="color: <?php echo $this->desc_color; ?>;" id="search_url-description" class="<?php echo __('description">URL of Search Results Page.'); ?></p></td>
				</tr>
				<!--tr>
					<th scope="row"><label for="cart_url"><?php echo __('Cart URL'); ?></label></th>
					<td><input id="cart_url" class="regular-text" type="text" value="<?php echo $options['cart_url']; ?>" name="cart_url">
					<p style="color: <?php echo $this->desc_color; ?>;" id="cart_url-description" class="description"><?php echo __('GoDaddy Shopping Cart URL.'); ?></p></td>
				</tr-->
				<tr>
					<th scope="row"><label for=""><?php echo __('Purge GoDaddy Products'); ?></label></th>
					<td><input id="purge_products" type="checkbox" name="purge_products">
						<p style="color: <?php echo $this->desc_color; ?>;" id="purge_products-description" class="description">
							<?php echo __('Remove All GoDaddy products from database.'); ?>
						</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input id="submit" class="button button-primary" type="submit" value="<?php echo __('Save Changes'); ?>" name="submit">
			</p>
		</form>
	</div>
</div>
<?php
	}
	
/**
* AJAX Javaascript
*/
	function  my_action_javascript () {
?>
<script type="text/javascript" >
	function ajax_function (action, value, div) {
		if (value == 0) {
			alert ('Select a valid value');
			return;
		}
		jQuery('#'+div).html('<img src="<?php echo $this->plugin_url; ?>images/ajax-loader.gif" style="margin-top: 45px; margin-left: 90px;">');
		jQuery('#'+div).show();
		var data = {
			'action': action,
			'value': value
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#'+div).html(response);
		});
	}
	
	function create_shortcode(value) {
		jQuery('#shortcode-container').html('<h3><?php echo __('Copy the following line and paste it into your post or page'); ?></h3>[gd_products '+value+']');
		jQuery('#shortcode-container').show();
	}
</script>
<?php
	}
	
/**
* AJAX Action
*/
	function my_action_my_action () {
	global $wpdb;
?>
			<h3><?php echo __('Select Product Section Name'); ?></h3>
			<select id="import_group_select" name="import_group_select" onchange="javascript: create_shortcode(this.value);">
				<option value="-1"><?php echo __('Select the Product Section Name.'); ?></option>
<?php
		$sections = $wpdb->get_results("SELECT DISTINCT n.meta_value as name, g.meta_value as id  
																	  FROM $wpdb->postmeta n 
																	  INNER JOIN $wpdb->postmeta g ON g.post_id = n.post_id 
																	  INNER JOIN $wpdb->postmeta x ON x.post_id = n.post_id 
																		WHERE n.meta_key = 'product-group-section-name' 
																		AND g.meta_key = 'product-group-section-id'
																		AND x.meta_key = 'product-export-group' 
																		AND x.meta_value = '".$_POST['value']."'
																		ORDER BY n.meta_value", ARRAY_A);
		foreach ($sections as $section) {
			echo '				<option value="'.$section['id'].'">'.$section['name'].'</option>'."\n";
		}
?>
			</select>
<?php
	wp_die(); 
	}
	
/**
* Shortcose Generator
*/
	function shortcode_generator() {
		GLOBAL $wpdb;
?>
<div id="wpbody-content" tabindex="0" aria-label="Main content">
	<div class="wrap">
		<h2><?php echo __('GoDaddy Shortcode Generator'); ?></h2>
		<div id="import_group">
			<h3><?php echo __('Select Product Import Group'); ?></h3>
			<select id="import_group_select" name="import_group_select" onchange="javascript: ajax_function('my_action', this.value, 'group-section_div');">
				<option value="0"><?php echo __('Select the Product Import Group.'); ?></option>
<?php
		$exports = $wpdb->get_col("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = 'product-export-group' ORDER BY 'meta_value'" );
		foreach ($exports as $item) 
			echo '<option value="'.$item.'">'.$item.'</option>';
?>
			</select>
		</div>
		<div id="group-section_div" style="display: none;">
		</div>
		<div id="shortcode-container" style="display: none;">
		</div>
	</div>
</div>
<?php
	}
	
/**
* Product Import
*/
	function product_import () {
		GLOBAL $wpdb;
		$imports = array();
		// find import files
		if ($dh = opendir($this->plugin_dir.'imports')) {
	    while ($file = readdir($dh)) {
        if (strpos($file, '.json')) {
	        $imports[]['file'] = $file;
        }
	    }
			closedir($dh);
		}
		foreach ($imports as $key => $item) 
			$imports[$key]['import'] = ucwords(str_ireplace('-', ' ', str_ireplace('.json', '', $imports[$key]['file'])));
    $message = '';
    if (isset($_POST['submit'])) {
	    if ($_POST['file_select'] == '0') $message = 'You did not select an import';
    	else {
		    // Generate list of existing product-group-sections
		    $sections = array();
		    $reverse = array();
		    $imported = 0;
				$temp = $wpdb->get_results("SELECT DISTINCT m1.meta_value AS `product-group-section-name`, 
																										m2.meta_value AS `product-group-section-id`
																		FROM dr_postmeta m1
																		INNER JOIN  dr_postmeta m2 ON m1.post_id = m2.post_id
																		WHERE m1.meta_key = 'product-group-section-name' 
																		AND m2.meta_key = 'product-group-section-id'
																		ORDER BY m2.meta_key", ARRAY_A );
				foreach ($temp as $section) {
					$sections[$section['product-group-section-id']] = $section['product-group-section-name'];
					$reverse[$section['product-group-section-name']] = $section['product-group-section-id'];
				}
	    	$array = json_decode(file_get_contents($this->plugin_dir.'imports/'.$_POST['file_select']), true); // get the import data
	    	$product_group_section_name = ucwords(str_ireplace('-', ' ', str_ireplace('.json', '', $_POST['file_select'])));
	    	foreach ($array as $prod_array) {
				  $post = array(
											'post_content' => $prod_array['product-name'].' - '.$prod_array['product-id'].' - '.$prod_array['product-group-section-id'],
											'post_name' => $prod_array['product-name'],
											'post_title' => $prod_array['product-name'],
											'post_status' => 'publish', 
											'post_type' => 'gd_product',
											);
					$querystr = "
					    SELECT DISTINCT $wpdb->posts.ID
					    FROM $wpdb->posts
					    INNER JOIN  $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
					    INNER JOIN  $wpdb->postmeta m2 ON $wpdb->posts.ID = m2.post_id
					    WHERE $wpdb->postmeta.meta_key = 'product-id'
					    AND $wpdb->postmeta.meta_value = '".$prod_array['product-id']."'";
				  $pageposts = $wpdb->get_col($querystr);
					if (isset($pageposts['0'])) $post['ID'] = $pageposts['0'];
			    $id = wp_insert_post($post);
			    if (strpos($prod_array['product-name'], '-')) {
						$temp = explode('-', $prod_array['product-name']);
						$product_name = $temp[count($temp) - 1];
						unset($temp[count($temp) - 1]);
						$group_name = trim(implode('-', $temp));
					} else {
						$product_name = $prod_array['product-name'];
						$group_name = $prod_array['product-name'];
					}
					if (!isset($reverse[$group_name])) {
						$sections[] = $group_name;
						$reverse[$group_name] = max(array_keys($sections));
					}
					// Check and generate product-group-section-id
					$query = "SELECT m1.`meta_value` 
										FROM $wpdb->postmeta m1 
										INNER JOIN $wpdb->postmeta m2 ON m1.`post_id` = m2.`post_id` 
										WHERE m1.`meta_key` = 'product-group-section-id'
										AND m2.`meta_key` = 'product-group-section-name'
										AND m2.`meta_value` = '".sanitize_text_field($group_name)."'";
					$mid = $wpdb->get_var($query);
					if (is_null($mid)) {
						$query = "SELECT MAX(CAST(`meta_value` AS SIGNED INTEGER))
											FROM $wpdb->postmeta 
											WHERE `meta_key` = 'product-group-section-id'";
						$mid = $wpdb->get_var($query) + 1;
					}
					update_post_meta( $id, 'product-name', sanitize_text_field($product_name));
					update_post_meta( $id, 'product-id', sanitize_text_field($prod_array['product-id']) );
					update_post_meta( $id, 'product-group-section-id', $mid);
					update_post_meta( $id, 'product-group-section-name', sanitize_text_field($group_name) );
					update_post_meta( $id, 'product-export-group', sanitize_text_field(ucwords(str_ireplace('-', ' ', str_ireplace('.json', '', $_POST['file_select'])))) );
					update_post_meta( $id, 'retail-default', sanitize_text_field($prod_array['retail_default']) );
					set_time_limit(60); // These imports can be long
			    $imported++;
	    	}
	    }
    }
?>
<div id="wpbody-content" tabindex="0" aria-label="Main content">
	<div class="wrap">
		<h2><?php echo __('GoDaddy Product Import'); ?></h2>
<?php
		if ($message) echo '<p style="color: '.$this->desc_color.'">'.$message.'</p>';
?>
		<form novalidate="novalidate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="program_id">Import Type</label></th>
					<td>
						<select id="file_select" name="file_select">
							<option value="0"><?php echo __('Select the set of products to install.'); ?></option>
<?php
		foreach ($imports as $item) 
			echo '<option value="'.$item['file'].'">'.$item['import'].'</option>';
?>
						</select>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input id="submit" class="button button-primary" type="submit" value="<?php echo __('Do Import'); ?>
				" name="submit">
			</p>
		</form>
	</div>
</div>
<?php
	}
	
/**
* Pricing Import
*/
	function pricing_import () {
		GLOBAL $wpdb;
		$message = '';
    if (isset($_POST['submit'])) {
			if (move_uploaded_file($_FILES['importfile']['tmp_name'], $this->plugin_dir.'/tmp/'.$_FILES['importfile']['name'])) {
		    $imported = 0;
				if ($_FILES['importfile']['type'] == 'text/xml') {
					$contents = file_get_contents($this->plugin_dir.'/tmp/'.$_FILES['importfile']['name']);
					$parser = xml_parser_create();
					xml_parse_into_struct($parser, $contents, $vals, $index);
					xml_parser_free($parser);
					$start = false;
					$count = 0;
					$row = array();
					foreach ($vals as $key => $value) {
						if ($value['tag'] == 'DATA') {
							if (is_numeric($value['value']) || $start) {
								$start = true;
								$count ++;
								$row[$count] = $value['value'];
								if ($count == 8) {
						    	$imported = $this->update_pricing($row, $imported);
									$count = 0;
									$row = array();
								}
							}
						}
					}
				} else if ($_FILES['importfile']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
				  $zip = new ZipArchive;
					if (!$zip->open($this->plugin_dir.'/tmp/'.$_FILES['importfile']['name']) === TRUE) {
					  echo 'Error opening file<br />';
					  exit;
					}
				  $zip->extractTo($this->plugin_dir.'/tmp/');
				  $zip->close();
					$contents = file_get_contents($this->plugin_dir.'/tmp/xl/worksheets/sheet.xml');
					$parser = xml_parser_create();
					xml_parse_into_struct($parser, $contents, $vals, $index);
					xml_parser_free($parser);
					$row = array();
					$index = 1;
					foreach ($vals as $key => $value) {
 						if ($value['tag'] == 'X:ROW' && $value['type'] == 'close') {
							if (count($row) != 0) {
					    	$imported = $this->update_pricing($row, $imported);
								$row = array();
								$index = 1;
							}
						}
						if ($value['tag'] == 'X:C') {
							if (isset($vals[$key+1]['value'])) {
								$row[$index] = $vals[$key+1]['value'];
								$index++;
							}
						}
					}
				} else if ($_FILES['importfile']['type'] == 'application/vnd.ms-excel.sheet.macroEnabled.12') {
					require_once($this->plugin_dir.'/includes/eiseXLSX.php');
					try { 
					    $xlsx = new eiseXLSX($this->plugin_dir.'/tmp/'.$_FILES['importfile']['name']);
					} catch(eiseXLSX_Exception $e) {
						$message = $_FILES['importfile']['type'].' file format is not supported';
					}
					for ($i = 1; $i <= $xlsx->getRowCount(); $i++) {	// skip the first teo rows
						$row = array();
						for ($j = 1; $j <= 8; $j++) $row[] = $xlsx->data('R'.$i.'C'.$j);
				  	$imported = $this->update_pricing($row, $imported);
					}
				} else {
						$message = $_FILES['importfile']['type'].' file format is not supported';
				}
		    if ($message == '') $message = $imported.' prices imported';
		    unlink($this->plugin_dir.'/tmp/'.$_FILES['importfile']['name']);
			} else $message = 'Error opening import file';
    }
?>
<div id="wpbody-content" tabindex="0" aria-label="Main content">
	<div class="wrap">
		<h2><?php echo __('GoDaddy Pricing Import'); ?></h2>
<?php
		if ($message) echo '<p style="color: '.$this->desc_color.'">'.$message.'</p>';
?>
		<form enctype="multipart/form-data" novalidate="novalidate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="program_id"><?php echo __('Import File'); ?></label></th>
					<td>
						<input name="importfile" type="file">
					</td>
				</tr>
			</table>
			<p class="submit">
				<input id="submit" class="button button-primary" type="submit" value="<?php echo __('Import File'); ?>" name="submit">
			</p>
		</form>
	</div>
</div>
<?php
	}

/** 
* write or update product pricing
*/
	private function update_pricing ($row, $imported) {
		GLOBAL $wpdb;
		if (!is_numeric($row['1'])) return $imported;
	  $post = array(
								'post_content' => $row['2'].' - '.$row['1'].' - ',
								'post_name' => $row['2'],
								'post_title' => $row['2'],
								'post_status' => 'publish', 
								'post_type' => 'gd_product',
								);
		$querystr = "
		    SELECT $wpdb->posts.* 
		    FROM $wpdb->posts
		    INNER JOIN  $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
		    WHERE $wpdb->postmeta.meta_key = 'product-id'
		    AND $wpdb->postmeta.meta_value = '".trim($row['1'])."'";
	  $pageposts = $wpdb->get_results($querystr, ARRAY_A);
	  $existing = $pageposts['0'];
		if (!is_null($existing)) {
			$post['ID'] = $existing['ID'];
			$post['post_content'] = $existing['post_content'];
		}
	  $id = wp_insert_post($post);
		update_post_meta( $id, 'product-id', sanitize_text_field(trim($row['1'])) );
		update_post_meta( $id, 'retail-min', sanitize_text_field($row['3']) );
		update_post_meta( $id, 'retail-max', sanitize_text_field($row['4']) );
		update_post_meta( $id, 'retail-price', sanitize_text_field($row['5']) );
		update_post_meta( $id, 'sale-price', sanitize_text_field($row['6']) );
		update_post_meta( $id, 'sale-start', sanitize_text_field($row['7']) );
		update_post_meta( $id, 'sale-end', sanitize_text_field($row['8']) );
		$imported++;
		return $imported;
}

/**
* Pricing Export
*/
	function pricing_export () {
		GLOBAL $wpdb;
		$message = '';
    if (isset($_POST['submit'])) {
			$meta = get_user_meta(wp_get_current_user()->ID, 'session_tokens', true);
			foreach ($meta as $key => $value);
			$file = $this->plugin_dir.'/tmp/'.$_POST['group_select'].'_'.$key.'.xlsx';
	    if ($_POST['group_select'] != '0') {
				require_once($this->plugin_dir.'/includes/eiseXLSX.php');	
				$xlsx = new eiseXLSX($this->plugin_dir.'/includes/templates/template2007.xlsx');
				$posts = get_posts(array('posts_per_page'   => -1,
																 'meta_key' => 'product-export-group', 
																 'meta_value' => ucwords(str_replace('-', ' ',$_POST['group_select'])), 
																 'post_type' => 'gd_product', 
																));
				foreach ($posts as $key => $item) {
					$customs = get_post_custom($item->ID);
					foreach ($customs as $key2 => $custom) {
						$posts[$key]->{str_replace('-', '_', $key2)} = $custom['0'];
					}
				}
				$i = 3;
				foreach ($posts as $item) {
					$xlsx->data('A'.$i, $item->product_id);
					$xlsx->data('B'.$i, $item->post_title);
					$xlsx->data('C'.$i, $item->retail_min);
					$xlsx->data('D'.$i, $item->retail_max);
					$xlsx->data('E'.$i, $item->retail_price);
					$xlsx->data('F'.$i, $item->sale_price);
					$xlsx->data('G'.$i, $item->sale_start);
					$xlsx->data('H'.$i, $item->sale_end);
					$i++;
				}
		    $xlsx->Output($file, "F");
		    $message = '<a href="'.str_replace($this->plugin_dir, $this->plugin_url, $file).'" target="_BLANK">Export File</a>';
		  } else {
		    $message = 'You did not select an export';
		  }
    }
		$exports = $wpdb->get_col("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = 'product-export-group'" );
?>
<div id="wpbody-content" tabindex="0" aria-label="Main content">
	<div class="wrap">
		<h2><?php echo __('GoDaddy Pricing Export'); ?></h2>
<?php
		if ($message) echo '<p style="color: '.$this->desc_color.'">'.$message.'</p>';
?>
		<form novalidate="novalidate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="program_id"><?php echo __('Export Type'); ?></label></th>
					<td>
						<select id="group_select" name="group_select">
							<option value="0"><?php echo __('Select the set of products to export.'); ?></option>
<?php
		foreach ($exports as $item) 
			echo '<option value="'.strtolower(str_replace(' ', '-',$item)).'">'.$item.'</option>';
?>
						</select>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input id="submit" name="submit" class="button button-primary" type="submit" value="<?php echo __('Do Export'); ?>" name="submit">
			</p>
		</form>
	</div>
</div>
<?php
	}
	
}
