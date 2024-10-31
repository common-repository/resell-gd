<?php
/**
Name: GoDaddy Resseller User functions
Version: 1.2
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

class gd_user {
	
/**
* class variables.
*/
  // this variable will hold url to the plugin  
  protected $plugin_url;
  protected $plugin_dir;
    
  // Option class
  protected $option_class;

  // Plugin Options
  protected $options;

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
		
    $this->options = get_option($this->option_class);
		
		add_shortcode( 'gd_domain_search', array(&$this, 'gd_domain_search') );
		add_shortcode( 'gd_products', array(&$this, 'gd_products') );
		add_shortcode( 'gd_login_form', array(&$this, 'gd_login_form') );
		add_shortcode( 'gd_login_link', array(&$this, 'gd_login_link') );
		add_shortcode( 'gd_create_link', array(&$this, 'gd_create_link') );
	}
		
/**
* Display Domain Search  Shortcode
*/
	function gd_domain_search($atts) {
		return $this->do_gd_domain_search($atts);
	}
	
	function do_gd_domain_search($atts) {
		$return = '
<form method="post" action="'.$this->options['search_url'].'" target="GoDaddy" style="margin-bottom: 13px;">
	<div class="gd_user_div">
	  <input class="gd_user_input" maxlength="63" name="domainToCheck" type="text" placeholder="'.__('Grab your domain now!').'" />
	  <input class="gd_user_submit" name="submit" type="submit" value="'.__('GO!').'" />
	  <input name="checkAvail" type="hidden" value="1"/> 
	</div>
</form>
';
		return $return;
  }
		
/**
* Display Product select
*/
	function gd_products($atts) {
		return $this->do_gd_products($atts);
	}
	
	function do_gd_products($atts) {
		GLOBAL $wpdb;
		if (!is_array($atts)) return __("Section ID not specified.");
		$exports = $wpdb->get_col("SELECT DISTINCT n.meta_value 
																FROM $wpdb->postmeta n
															  INNER JOIN $wpdb->postmeta i ON i.post_id = n.post_id 
																WHERE n.meta_key = 'product-group-section-name'
																AND i.meta_value = '".$atts['0']."'
																AND i.meta_key = 'product-group-section-id' 
																" );
		if (count($atts) == 1) {
			$posts = get_posts(array('posts_per_page' => -1,
															 'meta_key' => 'product-group-section-id', 
															 'meta_value' => $atts['0'], 
															 'post_type' => 'gd_product',
															 'order' => 'ASC',
															 'orderby' => 'ID',
															));
		} else {
			$posts = array();
			foreach ($atts as $att) {
				$temp = get_posts(array('posts_per_page' => -1,
																 'meta_key' => 'product-group-section-id', 
																 'meta_value' => $att, 
																 'post_type' => 'gd_product',
																 'order' => 'ASC',
																 'orderby' => 'ID',
																));
				$posts[] = $temp['0'];
			}
		}
		switch (count($posts)) {
			case 0;
				return __('No products in section');
				break;
			case 1:
				foreach ($posts as $temp) $post = $temp;
				$customs = get_post_custom($post->ID);
				$price = $customs['retail-price']['0'];
				$temp = explode('/', $customs['sale-start']['0']);
				$start_date = $temp['2'].'-'.str_pad($temp['0'], 2, '0', STR_PAD_LEFT).'-'.str_pad($temp['1'], 2, '0', STR_PAD_LEFT);
				$temp = explode('/', $customs['sale-end']['0']);
				$end_date = $temp['2'].'-'.str_pad($temp['0'], 2, '0', STR_PAD_LEFT).'-'.str_pad($temp['1'], 2, '0', STR_PAD_LEFT);
				$cur_date = date('Y-m-d');
				if ($start_date <= $cur_date && $end_date >= $cur_date) {
					$price = $customs['sale-price']['0'].' (Reg. '.$price.')';
				}
				$return = '
        <h4 class="gd_select_title" id="gd_select_title-'.$atts['0'].'">'.$exports['0'].'</h4>
        <form method="post" action="https://www.secureserver.net/gdshop/xt_orderform_addmany.asp?prog_id='.$this->options['program_id'].'" target="GoDaddy">
					<div class="gd_user_hosting_div">
	          <input type="hidden" name="tcount" value="1">
	          <input type="hidden" name="qty_1" value="1">
	          <input type="hidden" name="item_1" value="1">
	          <input type="hidden" name="prog_id" value="'.$this->options['program_id'].'">
	          <input type="hidden" name="pf_id_1" value="'.$customs['product-id']['0'].'">
	          <p class="gd_user_select" id="gd_user_select-'.$atts['0'].'">'.$customs['product-name']['0'].' $'.$price.'</p>
	          <input type="submit" class="gd_user_hosting_submit" value="'.__('Add to Cart').'">
					</div>
        </form>
';
				break;
			default:
				$return = '
        <h4 class="gd_select_title" id="gd_select_title-'.$atts['0'].'">'.$exports['0'].'</h4>
        <form method="post" action="https://www.secureserver.net/gdshop/xt_orderform_addmany.asp?prog_id='.$this->options['program_id'].'" target="GoDaddy">
					<div class="gd_user_hosting_div">
	          <input type="hidden" name="tcount" value="1">
	          <input type="hidden" name="qty_1" value="1">
	          <input type="hidden" name="item_1" value="1">
	          <input type="hidden" name="prog_id" value="'.$this->options['program_id'].'">
	          <select name="pf_id_1" class="gd_user_select" id="gd_user_select-'.$atts['0'].'">
';
				foreach ($posts as $post) {
					$customs = get_post_custom($post->ID);
					$price = $customs['retail-price']['0'];
					$temp = explode('/', $customs['sale-start']['0']);
					$start_date = $temp['2'].'-'.str_pad($temp['0'], 2, '0', STR_PAD_LEFT).'-'.str_pad($temp['1'], 2, '0', STR_PAD_LEFT);
					$temp = explode('/', $customs['sale-end']['0']);
					$end_date = $temp['2'].'-'.str_pad($temp['0'], 2, '0', STR_PAD_LEFT).'-'.str_pad($temp['1'], 2, '0', STR_PAD_LEFT);
					$cur_date = date('Y-m-d');
					if ($start_date <= $cur_date && $end_date >= $cur_date) {
						$price = $customs['sale-price']['0'].' (Reg. '.$price.')';
					}
					$return .= '            <option value="'.$customs['product-id']['0'].'">'.$customs['product-name']['0'].' - $'.$price.'</option>'."\n";
				}
				$return .= '
	          </select>
	          <input type="submit" class="gd_user_hosting_submit" value="'.__('Add to Cart').'">
					</div>
        </form>
';
				break;
		}
		return $return;
  }
		
/**
* Display Login Form  Shortcode
*/
	function gd_login_form($atts) {
		return $this->do_gd_login_form($atts);
	}
	
	function do_gd_login_form($atts) {
		$return = '
<form target="GoDaddy" spellcheck="false" name="login" method="POST" id="login-form" autocorrect="off" autocapitalize="off" action="https://sso.secureserver.net/v1/?path=%2Flogin.aspx%3Fci%3D9106%26spkey%3DSPSWNET-130506072552002%26prog_id%3D'.$this->options['program_id'].'&amp;app=idp&amp;plid='.$this->options['program_id'].'">
	<input type="hidden" value="idp" name="app">
	<input type="hidden" value="idp" name="realm">
	<div class="gd_login_div">
		<label class="control-label">'.__('Username or Customer no.').'</label><br />
	  <input class="gd_login_input" maxlength="63" name="name" id="username" type="text" />
	</div>
	<div class="gd_login_div">
		<label class="control-label">'.__('Password').'</label><br />
	  <input class="gd_login_password" maxlength="63" name="password" id="password" type="password" />
	</div>
	<div class="gd_login_submit_div">
	  	<input class="gd_hosting_login_link" name="submitBtn" type="submit" value="'.__('Login').'" />
	</div>
</form>
';
		return $return;
  }
		
/**
* Display Login Link  Shortcode'
*/
	function gd_login_link($atts) {
		return $this->do_gd_login_link($atts);
	}
	
	function do_gd_login_link($atts) {
		$return = '
<p class="gd_hosting_account_link">
	<a href=" https://sso.secureserver.net/?app=idp&path=%2flogin.aspx%3fci%3d9106%26spkey%3dSPSWNET-130506072552002%26prog_id%3d'.$this->options['program_id'].'&plid='.$this->options['program_id'].'" target="GoDaddy" class="gd_hosting_account_link_link">'.__('Login').'</s>
</p>
';
		return $return;
  }
		
/**
* Display Create link  Shortcode
*/
	function gd_create_link($atts) {
		return $this->do_gd_create_link($atts);
	}
	
	function do_gd_create_link($atts) {
		$return = '
<p class="gd_hosting_account_link">
	<a href="https://sso.secureserver.net/account/create?path=%2Flogin.aspx%3Fci%3D9106%26spkey%3DSPSWNET-130506072552002%26prog_id%3D'.$this->options['program_id'].'&app=idp&plid='.$this->options['program_id'].'&ssoreturnpath=/%3Fapp%3Didp%26path%3D%252flogin.aspx%253fci%253d9106%2526spkey%253dSPSWNET-130506072552002%2526prog_id%253d510674%26plid%3D'.$this->options['program_id'].'" target="GoDaddy" class="gd_hosting_account_link_link_2">'.__('Create Account').'</a>
</p>
';
		return $return;
  }

}
