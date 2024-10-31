<?php
/**
Name: GoDaddy Resseller core functions
Version: 1.0
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

class gd_core {
	
/**
* class variables.
*/
  // this variable will hold url to the plugin  
  protected $plugin_url;
  protected $plugin_dir;
    
  // Option class
  protected $option_class;

/**
* class constructpr.
*/
	function __construct() {
	}

/**
* class initialization.
*/
	function initialize ($plugin_url, $plugin_dir, $option_class) {
		$this->plugin_url = $plugin_url;
		$this->plugin_dir = $plugin_dir;
		$this->option_class = $option_class;
		
    // Add script and stylesheet actions
    add_action( 'wp_enqueue_scripts', array(&$this, 'enque_scripts' ));
    add_action( 'wp_enqueue_scripts', array(&$this, 'enque_styles' ));
  
    // register the activation function by passing the reference to our instance
    register_activation_hook(__FILE__, array(&$this, 'install'));

    // register the deactivation function by passing the reference to our instance
    register_deactivation_hook(__FILE__, array(&$this, 'remove'));
    
		add_action( 'widgets_init', array(&$this, 'widgets_init'));

	}
	
/**
* Widget initialization
*/
	function widgets_init() {
		// Register Widget
	  include_once ($this->plugin_dir.'includes/gd_widget.php');
    $custom = substr($this->plugin_dir, 0, strlen($this->plugin_dir) - 1).'_custom/gd_widget_custom.php';
    if (file_exists($custom)) {
		  include_once ($custom);
			register_widget("gd_widget_custom");
    } else {
			register_widget("gd_widget");
		}
	  include_once ($this->plugin_dir.'includes/gd_widget_hosting.php');
    $custom = substr($this->plugin_dir, 0, strlen($this->plugin_dir) - 1).'_custom/gd_widget_hosting_custom.php';
    if (file_exists($custom)) {
		  include_once ($custom);
			register_widget("gd_widget_hosting_custom");
    } else {
			register_widget("gd_widget_hosting");
		}
	}

/**
* load scripts
*/
  function enque_scripts () {
    wp_enqueue_script( 'jquery' );
    wp_register_script( 'gd_jscript', $this->plugin_url . 'js/jscript.js', false, false, false );
		wp_enqueue_script( 'gd_jscript' );

    // and the custom script
    $custom = substr($this->plugin_dir, 0, strlen($this->plugin_dir) - 1).'_custom/custom.js';
    if (file_exists($custom)) {
// 	    $custom = substr($this->plugin_url, 0, strlen($this->plugin_url) - 1).'_custom/custom.js';
	    wp_register_script( 'gd_jscript_custom', $custom, false, false, false );
			wp_enqueue_script( 'gd_jscript_custom' );
		}
  }

/**
* Enque Stylesheets
*/
  function enque_styles () {
		wp_register_style( 'gd_styles', $this->plugin_url.'css/style.css', false, false );
		wp_enqueue_style( 'gd_styles' );
    // and the custom script
    $custom = substr($this->plugin_dir, 0, strlen($this->plugin_dir) - 1).'_custom/custom.css';
    if (file_exists($custom))
	    $custom = substr($this->plugin_url, 0, strlen($this->plugin_url) - 1).'_custom/custom.css';
			wp_register_style( 'gd_styles_custom', $custom, false, false );
			wp_enqueue_style( 'gd_styles_custom' );
  }

/**
* install function
*/
  function install() {
    global $wpdb;
  }
  
/**
* remove function
*/
  function remove() {
  }
  
}
