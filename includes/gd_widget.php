<?php
/**
Name: GoDaddy Resseller User functions
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

class gd_widget extends WP_Widget {
	
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
		$this->option_class = 'godaddyreseller';
    $this->options = get_option($this->option_class);
		parent::__construct(
			'GoDaddySearch', // Base ID
			'Domain Search', // Name
			array( 'description' => 'GoDaddy Domain Search', ) // Args
		);
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
<form method="post" action="<?php echo $this->options['search_url']; ?>" target="GoDaddy" style="margin-bottom: 13px;">
	<div class="gd_widget_div">
		  <input class="gd_widget_input" maxlength="63" name="domainToCheck" type="text" placeholder="<?php echo __('Grab your domain now!'); ?>" />
	  	<input class="gd_widget_submit" name="submit" type="submit" value="<?php echo __('GO!'); ?>" />
	  <input name="checkAvail" type="hidden" value="1"/> 
	</div>
</form>
<?php
		echo $args['after_widget'];
	}

/**
* Back-end widget form.
*/
public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
<?php 
	}

}
