<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.meworla.com
 * @since             1.0.0
 * @package           AddressixAPI
 *
 * @wordpress-plugin
 * Plugin Name:       Addressix API
 * Plugin URI:        https://www.addressix.com/
 * Description:       Addressix API
 * Version:           1.0.0
 * Author:            Meworla GmbH
 * Author URI:        http://www.meworla.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       addressixapi
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/addressix/wordpress-addressix-api
 * GitHub Branch:     master
 */

define( 'AIXAPI__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once(AIXAPI__PLUGIN_DIR . 'class.addressixapi.php' );

?>