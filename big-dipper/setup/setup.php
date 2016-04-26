<?php
/**
 * Author: Vitaly Kukin
 * Date: 23.04.2016
 * Time: 21:01
 */

/**
 * Setup the plugin
 */
function bidi_install() {

    require( BIDI_PATH . 'setup/sql.php' );

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    foreach( bidi_sql_list() as $key ){
        dbDelta($key);
    }

    update_site_option( 'bidi-version', BIDI_VERSION  );
}

/**
 * Uninstall plugin
 */
function bidi_uninstall() {}

/**
 * Check installed plugin
 */
function bidi_installed() {

    if ( !current_user_can('install_plugins') ) return;

    if ( get_site_option('bidi-version') < BIDI_VERSION )
        bidi_install( );
}
add_action( 'admin_menu', 'bidi_installed' );

/**
 * When activate plugin
 */
function bidi_activate() {

    bidi_installed();

    do_action( 'bidi_activate' );
}

/**
 * When deactivate plugin
 */
function bidi_deactivate() {

    do_action( 'bidi_deactivate' );
}

function bidi_init_scripts(){

    $screen = get_current_screen();

    if ( !isset( $screen->post_type ) || !in_array($screen->post_type, array('post', 'page') ) )
        return false;

    printf('<link id="%s" href="%s" rel="stylesheet" type="text/css"/>' . "\n",
        'bidi-style', plugins_url('css/main.css?ver=1.0', dirname(__FILE__) . '../') );

    wp_register_script( 'bidi-main', plugins_url( 'js/bidi.js', dirname(__FILE__) ), array('jquery'), '1.0' );
    wp_enqueue_script( 'bidi-main' );

    return true;
}
add_action( 'admin_head', 'bidi_init_scripts' );