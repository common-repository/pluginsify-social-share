<?php 
/**
 * Shortcode for social icons 
 * user [pgfyshare]
 */

defined( 'ABSPATH' ) || exit; // Exit if direct file access

add_shortcode( 'pgfyshare',  function() {
    return $this->get_template();
});