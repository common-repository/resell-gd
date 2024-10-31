=== Resell GD ===
Contributors: lcwakeman
Donate link: http://larrywakeman.com/download/godaddy-reseller-plugin/
Plugin URI: http://larrywakeman.com/download/godaddy-reseller-plugin/
Author URI: http://larrywakeman.com/
Tags: Resell GD, storefront interface, domain name searches
Requires at least: 4.2
Tested up to: 5.3
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Resell GD Plugin - Allow GoDaddy Resellers to redirect product sales and perform domain name search through their reseller.

== Description ==

Resell GD Plugin - Allow GoDaddy Resellers to redirect product sales and perform domain name search through their reseller.

This plugin helps Resell GDs set up a store front on their own site that allows their users to perform Domain searched
and purchase other hosting and domain products.

Features include:
*	Upgrade Safe customization
* Importing of exported GoDaddy Price Lists
* Exporting of Price Lists for importing into GoDaddy

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure Resell GD
1. Import GoDaddy Products
1. Import GoDaddy Prices
1. Create a page with the [gd_domain_search] shortcode for a domain search page
1. Add the Domain Search widget for domain searches from the sidebar
1. Create page(s) with the [gd_products <group id>] shortcode to allow users to select a product and add it to the cart.

== Frequently Asked Questions ==

= How can I change the look of the Domain Search Widget =

The html and the css for the widget are very spartan. It is dimply a form with a text box and a submit button. The first change you might want
to make is to the css for the widget. Follow the following steps to change the css.

1. Create the resell-gd_custom directory in the plugins directory of you site if it doesn't exist.
1. Create the file custom.css if it doesn't exist.
1. If the following rules do not exist, create them or edit the existing rules. Note that the sprite image I am using is in the
resell-gd_custom/images directory.
1. Save the files and check it out.

`.gd_widget_submit {
    background: rgba(0, 0, 0, 0) url("images/readmore-sprite.png") no-repeat scroll left top;
    border: none;
    border-radius: 23px;
    display: block;
    height: 46px;
    width: 200px;
    font-size: 125%;
    color: #fff;
}

.gd_widget_input {
	width: 200px;
}
`

To add text to the widget, do the following:

1. Create the resell-gd_custom directory in the plugins directory of you site if it doesn't exist.
1. Create the file gd_widget_custom.php. Insert the following:

`<?php

class gd_widget_custom extends gd_widget {
	
	function __construct() {
		parent::__construct();
  }

	public function widget( $args, $instance ) {
		$args['after_widget'] = '<h4 class="text-center" style="color: #a9a883;">It All Starts With A Domain Name</h4>'.$args['after_widget'];
		parent::widget( $args, $instance );
  }

}
`

The above will add the text 'It All Starts With A Domain Name' below the widget.


== Upgrade Notice ==

= 1.0 =
* Initial version.

= 1.1 =
* GoDaddy Import/Export format change.
* FAQ changes.

= 1.2 =
* depreciated code updated
*  Added Hosting Account Login and Create Widget.
* Fixed Import Issues. Purging and reimporting products and pricing required after this upgrade. Admin->Settings->Resell GD.
* Internationalization

= 1.3 =
* Better error reporting
* Multiple Sedtion ID support for gd_products shortcode - [gd_products id id...]. This is required because some products
that you may want to display together end up with different section ids.

= 1.4 =
* PHP 7.1

== Screenshots ==

1. Configure Resell GD. Enter your GoDaddy Program ID and Domain Search URL. The Purge GoDaddy Products checkbox will delete all the GoDaddy products from the database.
2. Import GoDaddy Products. Select the Export Group, the GoDaddy Price management page the products are on, and click the Do Import button. The import can rakde some time.
3. Import GoDaddy Prices. These are the prices exported from the GoDaddy Price management page.
4. Add your shortcodes to your posts or pages. You can use the Shortcode Generator to generate a shortcode.

== Changelog ==

= 1.0 =
* Initial version.

= 1.1 =
* GoDaddy Import/Export format change.
* FAQ changes.

= 1.2 =
* depreciated code updated
* Added Hosting Account Login and Create Widget.
* Fixed Import Issues. Purging and reimporting products and pricing required after this upgrade. Admin->Settings->Resell GD.
* Internationalization

= 1.3 =
* Better error reporting
* Multiple Sedtion ID support for gd_products shortcode - [gd_products id id...]. This is required because some products
that you may want to display together end up with different section ids.

= 1.4 =
* PHP 7.1

== Customization ==

This plugin is built completely with PHP Objects. All of the obhects except godaddyreseller.php can be inherited for customiztion.
There are some rules for this:

* The customized object must be in the custom directory.
* The filename must be <object name>_custom.php, i.e. gd_admin_custom.php
* The class must be named <object name>_custom and inherit <object name>, i.e `class gd_core_custom inherits gd_core {`.
* It should include a constructor that calls the parent constructor:
`	function __construct() {
		parent::__construct();
	}
`

javascript and css files may be customized also. The custom items are placed in the custom folder and named custom.js and 
custom.css. The default scripts and css files are enqueued before the custom scripts.

