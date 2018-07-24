<?php
/**
 * Library
 *
 * Create a library of reusable terms (strings) and display their
 * contents anywhere on your site with a shortcode.
 *
 * @package   Library
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/library/plugins/library
 * @copyright 2014 Patrick Daly
 *
 * @wordpress-plugin
 * Plugin Name:       Library
 * Plugin URI:        http://wordpress.org/plugins/library
 * Description:       Create a library of reusable terms (strings) and display their contents anywhere on your site with a shortcode.
 * Version:           1.1.0
 * Author:            Patrick Daly
 * Author URI:        http://developdaly.com
 * Text Domain:       library
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/developdaly/library
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-library.php' );

add_action( 'plugins_loaded', Library::get_instance() );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-library-admin.php' );
	add_action( 'plugins_loaded', Library_Admin::get_instance() );

}
