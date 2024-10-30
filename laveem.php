<?php
/* 
* +--------------------------------------------------------------------------+
* | Copyright (c) 2012 Laveem, Inc.                                          |
* +--------------------------------------------------------------------------+
* | This program is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by     |
* | the Free Software Foundation; either version 2 of the License, or        |
* | (at your option) any later version.                                      |
* |                                                                          |
* | This program is distributed in the hope that it will be useful,          |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
* | GNU General Public License for more details.                             |
* |                                                                          |
* | You should have received a copy of the GNU General Public License        |
* | along with this program; if not, write to the Free Software              |
* | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
* +--------------------------------------------------------------------------+
*/

/*
Plugin Name: Laveem Nutrition Label
Version: 0.5.0
Description: The easiest way to add nutritional information of your recipes to your blog. Laveem Label understands your recipes and provides useful nutritional information to your readers.
Author: Laveem
Author URI: http://www.laveem.com
*/

define('LAVEEM_MIN_WORDPRESS_REQUIRED', "2.7");
define('LAVEEM_WORDPRESS_VERSION_SUPPORTED', version_compare(get_bloginfo("version"), LAVEEM_MIN_WORDPRESS_REQUIRED,">="));
define('LAVEEM_ENABLED', LAVEEM_WORDPRESS_VERSION_SUPPORTED && laveem_validate_api_key('laveem_api_key'));

/**
 * Print the Laveem javascript tag
 */
function laveem_script() {
	$laveem_api_key = get_option("laveem_api_key");
	if($laveem_api_key){
?>
		<!-- Laveem tag: http://www.laveem.com -->
  		<script type="text/javascript" src="http://www.laveem.com/widget/plugin.js" data-apikey="<?php print addslashes($laveem_api_key);?>" data-platform="wordpress"></script>
  		<!-- end Laveem -->
<?php
	}
}

/**
 * Laveem plugin settings page
 */
function laveem_options(){
?>
	<div class="wrap">
    	<div class="icon32"></div>
    	<h2>Laveem Settings</h2>
<?php
		if(!LAVEEM_WORDPRESS_VERSION_SUPPORTED){
?>
    		<p style="width:50%;">Thanks for your interest in Laveem! Unfortunately, the Laveem plugin requires WordPress <?php print LAVEEM_MIN_WORDPRESS_REQUIRED ?> or newer. Please try again once you've upgraded.</p>
<?php
		}else{
      		if(get_option("is-not-first-load") && !laveem_validate_api_key("laveem_api_key")){
?>
    			<div class="error fade">
      				<p><strong>Invalid API Key.</strong>Laveem is disabled until you enter a valid API key.</p>
    			</div>
<?php
			}
?>
			<h3>Enter your API key below</h3>						
			
			<p class="instructions">Log into your Laveem account and then go to your <a href="http://www.laveem.com/account" target="_blank">Account page</a> to find your API key.</p>
			
			<form method="post" action="options.php">
  				<?php settings_fields( "laveem" ); ?>
  				<table class="form-table" style="width: auto;">
    			<tr valign="top">
      				<th style="width: auto;">API Key</th>
      				<td>
        				<input id="laveem-key" type="text" name="laveem_api_key" value="<?php print get_option( "laveem_api_key" ); ?>" class="regular-text" maxlength="40"/>
        				<span class="description">Required</span>
      				</td>
    			</tr>
  				</table>
  				<p class="submit">
    				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    				<span class="reminder" style="display: none;">&larr; Don't forget to save!</span>
  				</p>
			</form>
<?php 
		} 
?>
	</div>
<?php
}

/**
 * api key validator
 */
function laveem_validate_api_key($name) {
	$value = get_option($name);
	if(strlen($value)==40){
		return true;
	}
	return false;
}

/**
 * Initialize admin-specific hooks and settings
 */
function laveem_admin_init() {
  	wp_register_script("laveem_admin_script", WP_PLUGIN_URL . "/laveem/laveem.js");
  	wp_register_style("laveem_admin_style", WP_PLUGIN_URL . "/laveem/laveem.css");
  	// register settings for the options page
  	register_setting("laveem", "laveem_api_key", "laveem_sanitize_option");
}

/**
 * Include javascript needed by the options page
 */
function laveem_admin_includes() {
  	wp_enqueue_script("laveem_admin_script");
  	wp_enqueue_style("laveem_admin_style");
}

/**
 * Add the options menu item to the sidebar settings panel
 */
function laveem_options_menu() {
  	// add the options page to the settings menu
  	$page = add_options_page("Laveem Options", "Laveem", 8, __FILE__, "laveem_options");

  	// include plugin-specific includes on the options page
  	add_action("admin_print_scripts-" . $page, "laveem_admin_includes");
  	add_action("admin_print_styles-" . $page, "laveem_admin_includes");
}

/**
 * Sanitize an options form field on submit
 */
function laveem_sanitize_option($value) {
  	// now that the form has been submitted at least once, start showing the
  	// error for an empty/invalid api key
  	update_option( "is-not-first-load", true );
  	return htmlspecialchars($value);
}

// options
add_option("is-not-first-load");
add_option("laveem_api_key");

// hooks
// register settings for the admin options page
add_action("admin_init", "laveem_admin_init");
// add a menu item to the "settings" sidebar menu
add_action("admin_menu", "laveem_options_menu");

// add js tag to the footer
if(LAVEEM_ENABLED) {
  add_action("wp_footer", "laveem_script");
}
