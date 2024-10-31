<?php
/**
Plugin Name: Resell GD
Version: 1.4
Plugin URI: tbd
Donate URI: tbd
Description: GoDaddy Reseller Plugin - Allow Godaddy resellers to redirect product sales and perform domain name search through their reseller
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
** Permission is granted under GNU General Public License (GPL) http://opensource.org/licenses/GPL-2.0.
*/
	global $wp_version;
	
	// do version checks    
	$exit_msg = __('GoDaddy Resseller requires WordPress 4.2 and MySQL version 5.1 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>');
	
	if (version_compare($wp_version, "4.2", "<")) {
    exit($exit_msg);
	}
	
	global $wpdb;
	$exit_msg = __('GoDaddy Resseller requires MySQL 5.0 or newer.');
	$query = "SHOW VARIABLES LIKE 'version'";
	if (!$version = $wpdb->get_row($query, ARRAY_A)) {
    exit($exit_msg);
	}

	$temp = explode('.', $version['Value']);
	if ($temp['0'] < 5) {
    exit($exit_msg);
	} else if ($temp['1'] < 1 && $temp['0'] == 5) {
    exit($exit_msg);
  }
  
  // Make sure the tmp directory exists
  $tmpfile = str_replace('\\', '/', plugin_dir_path( __FILE__ )).'tmp';
  if (!file_exists ($tmpfile)) {
	  mkdir ($tmpfile, 755);
  }
  
  // Option class has changed, copy old option class to current one
  $options = get_option('dmc_user_list');
  if ($options && (isset($options['program_id']) || isset($options['program_id']))) {
	  update_option('godaddyreseller', array('program_id' => $options['program_id'], 'search_url' => $options['search_url']));
		unset ($options['program_id']);
		unset ($options['search_url']);
		if (count($options) != 0) update_option('dmc_user_list', $options);
	}
/**
* Function add_class
*/
  function add_gd_class($classname) {
	  include_once (str_replace('\\', '/', plugin_dir_path( __FILE__ )).'includes/'.$classname.'.php');
    $custom_dir = str_replace('\\', '/', plugin_dir_path( __FILE__ ));
    $custom = substr($custom_dir, 0, strlen($custom_dir) - 1).'_custom/'.$classname.'_custom.php';
    if (file_exists($custom)) {
		  include_once ($custom);
      $custom = $classname.'_custom';
      $class = new $custom();
    } else {
      $class = new $classname();
    }
    $class->initialize(plugin_dir_url( __FILE__ ), str_replace('\\', '/', plugin_dir_path( __FILE__ )), 'godaddyreseller');
  }
  
  add_gd_class('gd_core');
  if (is_admin()) {
    add_gd_class('gd_admin');
  } else {
    add_gd_class('gd_user');
  }
  add_gd_class('gd_taxonomy');
