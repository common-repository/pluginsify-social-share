<?php 
/**
 * Remove all data after delate plugin
 * 
 * @since      1.0.0
*/

defined( 'WP_UNINSTALL_PLUGIN' ) || exit; // Exit if accessed directly

delete_option('tpl_social_settings'); // delete plugin settings
