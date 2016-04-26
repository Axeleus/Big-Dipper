<?php
/**
 * Plugin Name: Bid Dipper
 * Plugin URI: http://yellowduck.me/
 * Description: Plugin to enhance functionality
 * Version: 0.2
 * Requires at least: WP 4.5
 * Author: Vitaly Kukin
 * Author URI: http://yellowduck.me/
 */

if ( !defined('BIDI_VERSION') ) define( 'BIDI_VERSION', '0.2' );
if ( !defined('BIDI_PATH') ) define( 'BIDI_PATH', plugin_dir_path( __FILE__ ) );

require( BIDI_PATH . 'core/core.php' );

if( is_admin() ) :
    require( BIDI_PATH . 'setup/setup.php' );
    require( BIDI_PATH . 'core/controller.php' );
endif;

register_activation_hook( __FILE__, 'bidi_install' );
register_uninstall_hook( __FILE__, 'bidi_uninstall' );
register_activation_hook( __FILE__, 'bidi_activate' );