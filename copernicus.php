<?php
/*
  Plugin Name: Copernicus
  Plugin URI:  http://copernicus.wiredot.com/
  Description: WordPress Framework
  Author: wiredot
  Version: 0.9.6
  Author URI: http://wiredot.com/
  License: GPLv2 or later
 */

// define path to the plugin
define( 'CP_PATH', dirname( __FILE__ ) );
define( 'CP_URL', WP_CONTENT_URL.'/plugins/copernicus' );

// main class file path
$core_class_filename = CP_PATH . '/lib/core/class-copernicus.php';

if ( file_exists( $core_class_filename ) ) {

  // load & initialize framework
  require_once $core_class_filename;

  CP::init();

} else {
  echo 'error loading plugin';
}
