<?php
/**
Name: GoDaddy Resseller User functions
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

class gd_widget_hosting extends WP_Widget {
	
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

  // Main class
  protected $parent_class;

  // defaults class
  protected $defaults;

/**
* class constructpr.
*/
	function __construct() {
		parent::__construct(
			'GoDaddyHosting', // Base ID
			'Hosting Account', // Name
			array( 'description' => 'GoDaddy Hosting Account', ) // Args
		);
		$this->option_class = 'godaddyreseller';
    $this->options = get_option($this->option_class);
  }

/**
* Front-end display of widget.
*/
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
?>
<?php if (isset($instance['show_login']) && $instance['show_login']) { ?>
<form target="GoDaddy" spellcheck="false" name="login" method="POST" id="login-form" autocorrect="off" autocapitalize="off" action="https://sso.secureserver.net/v1/?path=%2Flogin.aspx%3Fci%3D9106%26spkey%3DSPSWNET-130506072552002%26prog_id%3D<?php echo $this->options['program_id']; ?>&amp;app=idp&amp;plid=<?php echo $this->options['program_id']; ?>">
	<input type="hidden" value="idp" name="app">
	<input type="hidden" value="idp" name="realm">
	<div class="gd_widget_div">
		<label class="control-label"><?php echo __('Username or Customer #'); ?></label><br />
	  <input class="gd_widget_input" maxlength="63" name="name" id="username" type="text" />
	</div>
	<div class="gd_widget_div">
		<label class="control-label"><?php echo __('Password<'); ?>/label><br />
	  <input class="gd_widget_password" maxlength="63" name="password" id="password" type="password" />
	</div>
	<div class="gd_widget_submit_div">
	  	<input class="gd_hosting_login_link" name="submitBtn" type="submit" value="<?php echo __('Login'); ?>" />
	</div>
</form>
<?php
		}
		if (isset($instance['show_login_link']) && $instance['show_login_link']) {
?>																																																																	
<form target="GoDaddy" spellcheck="false" name="login" method="POST" id="login-form" autocorrect="off" autocapitalize="off" action="https://sso.secureserver.net/v1/?path=%2Flogin.aspx%3Fci%3D9106%26spkey%3DSPSWNET-130506072552002%26prog_id%3D<?php echo $this->options['program_id']; ?>&amp;app=idp&amp;plid=<?php echo $this->options['program_id']; ?>">
	<div class="gd_widget_submit_div">
  	<input class="gd_hosting_login_link" name="submitBtn" type="submit" value="<?php echo __('Login'); ?>" />
	</div>
</form>
<?php
		}
		if (isset($instance['show_create_account']) && $instance['show_create_account']) {
?>
<p class="gd_hosting_account">
	<a href="https://sso.secureserver.net/account/create?path=%2Flogin.aspx%3Fci%3D9106%26spkey%3DSPSWNET-130506072552002%26prog_id%3D<?php echo $this->options['program_id']; ?>&app=idp&plid=<?php echo $this->options['program_id']; ?>&ssoreturnpath=/%3Fapp%3Didp%26path%3D%252flogin.aspx%253fci%253d9106%2526spkey%253dSPSWNET-130506072552002%2526prog_id%253d510674%26plid%3D<?php echo $this->options['program_id']; ?>" target="GoDaddy" class="gd_hosting_account_link_2"><?php echo $instance['create']; ?></a>
</p>
<?php
		}
		echo $args['after_widget'];
	}

/**
* Back-end widget form.
*/
public function form( $instance ) {
echo '<pre>'; // debug
print_r ($instance); // debug
echo '</pre>'; // debug
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		$login = ! empty( $instance['login'] ) ? $instance['login'] : __( 'Login', 'text_domain' );
		$create = ! empty( $instance['create'] ) ? $instance['create'] : __( 'Create Account', 'text_domain' );
		$show_login = ! empty( $instance['show_login'] ) ? $instance['show_login'] : 'off';
		$show_login_link = ! empty( $instance['show_login_link'] ) ? $instance['show_login_link'] : 'off';
		$show_create_account = ! empty( $instance['show_create_account'] ) ? $instance['show_create_account'] : 'off';
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'login' ); ?>"><?php _e( 'Login Caption:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'login' ); ?>" name="<?php echo $this->get_field_name( 'login' ); ?>" type="text" value="<?php echo esc_attr( $login ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'create' ); ?>"><?php _e( 'Create Account Caption:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'create' ); ?>" name="<?php echo $this->get_field_name( 'create' ); ?>" type="text" value="<?php echo esc_attr( $create ); ?>">
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'show_login' ); ?>" name="<?php echo $this->get_field_name( 'show_login' ); ?>" type="checkbox" <?php if ($show_login == 'on') echo 'checked = "checked"'; ?>">
			<label for="<?php echo $this->get_field_id( 'show_login' ); ?>"><?php _e( 'Show Login Form:' ); ?></label> 
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'show_login_link' ); ?>" name="<?php echo $this->get_field_name( 'show_login_link' ); ?>" type="checkbox" <?php if ($show_login_link == 'on') echo 'checked = "checked"'; ?>">
			<label for="<?php echo $this->get_field_id( 'show_login_link' ); ?>"><?php _e( 'Show Login Link:' ); ?></label> 
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'show_create_account' ); ?>" name="<?php echo $this->get_field_name( 'show_create_account' ); ?>" type="checkbox" <?php if ($show_create_account == 'on') echo 'checked = "checked"'; ?>">
			<label for="<?php echo $this->get_field_id( 'show_create_account' ); ?>"><?php _e( 'Show Create Account Link:' ); ?></label> 
		</p>
<?php 
	}

}
