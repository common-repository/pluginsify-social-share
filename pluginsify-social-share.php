<?php
/**
 * Plugin Name: Pluginsify Social Share
 * Plugin URI: https://wordpress.org/plugins/pgfy-social-share
 * Description: Share WordPress posts, pages, custom pages in social media
 * Version: 1.0.0
 * Requires WordPress version: 4.5
 * Requires PHP version: 5.6
 * Author: Pluginsify
 * Author URI: https://pluginsify.com/
 * License: GPLv3 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: tpl-socail-share
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit; // Exit if direct file access

if(!class_exists('Tpl_Social_Share')):

class Tpl_Social_Share {
    /**
	 * The single instance of the class.
	 *
	 * @var object
	 */
    static protected $instance;

    /**
	 * Name of option to add option for plugin settings
     * 
	 * @const string settings name
	 */
    const TPL_SHARE_SETTINGS_ID = 'tpl_social_settings';

    /**
    * Constructor
    * Initialize plugin all actions
    * 
    * @since      1.0.0
     */
    public function __construct() {
        register_activation_hook(__FILE__, array($this, 'on_activation'));
        register_deactivation_hook(__FILE__, array($this, 'on_deactivation'));
        
        add_action('plugins_loaded', array($this, 'on_plugin_loaded'));
    }

    /**
    * Apply default plugin setting on plugin activation
    * 
    * @since      1.0.0
     */
    public function on_activation() {
        $this->add_default_setting();
    }


    /**
    * Insert default plugin setting
    * 
    * @since      1.0.0
    */
    
    public function add_default_setting() {
        $default_setting = array(
            'post_types' => array('post','page'),
            'positions'  => array('before_content','sticky_left','after_content','feature_image'),
            'icons_color' => array(
                'facebook' => '#3b5999',
                'twitter' => '#55acee',
                'pinterest' => '#bd081c',
                'linkedin' => '#0077B5',
                'whatsapp' => '#25D366'
            ),

            'icon_size' => 'small',
            
            'visiblity' => ['facebook', 'twitter', 'pinterest', 'linkedin', 'whatsapp']
        );

        $option_id = $this->get_setting_id();
        
        if(!get_option($option_id))
            update_option($option_id, $default_setting);
    }

    /**
    * Dogin stuff after plugin deactivation
    * Do night right now
    * 
    * @since      1.0.0
     */
    public function on_deactivation() {

    }

    /**
    * Doing action after plugin loaded
    * 
    * @since      1.0.0
     */
    public function on_plugin_loaded() {

        $this->load_textdomain();

        // check dependency
        if( !$this->dependency_ok()) {
            add_action( 'admin_init', array( $this, 'deactivate_self' ) );
            add_action( 'admin_notices', array( $this, 'display_dependency_notice' ) );
			return; // exit further execution 
        }

        $this->includes();
        $this->hooks();
    }

    /**
    * Load plugin textdomain
    * 
    * @since      1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain('tpl-socail-share', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
    }

    /**
    * Include all fatures
    * 
    * @since      1.0.0
     */
    public function includes() {
        $this->view_social_icons();
        $this->shortcode();
        $this->plugin_settings();
    }

    /**
    * Include social share template
    * 
    * @since      1.0.0
     */
    public function view_social_icons() {
        add_action('wp', array($this, 'template_controller'));
    }

    /**
    * Controlling and show / hide templates
    * 
    * @since      1.0.0
     */
    public function template_controller() {

        $template = $this->get_template();

        $settings = $this->get_settings();
        $view_locations = isset($settings['positions']) ? $settings['positions'] : array();
        $view_post_types = $settings['post_types'] ? $settings['post_types'] : array();

        
        // checking allowed post types and post location
        $allow_view = !empty($view_locations) && !empty($view_post_types) && is_singular($view_post_types);
        
        if($allow_view):

            // if(in_array('after_title', $view_locations)) {
            //     add_filter('the_title', function($title) use($template) {
            //         return $title . $template;
            //     }); 
            // }
            // Removed it due to unexpected placement, it place inside title <h1> tag.

            if(in_array('before_content', $view_locations)) {
                add_filter('the_content', function($content) use($template) {
                    return $template . $content;
                }); 
            }

            if(in_array('sticky_left', $view_locations)) {
                add_filter('the_content', function($content) use($template) {
                    return sprintf('%1$s <div class="tpl-socail-fl">%2$s</div>',$content, $template);
                }); 
            }

            if(in_array('after_content', $view_locations)) {
                add_filter('the_content', function($content) use($template) {
                    return  $content . $template;
                }); 
            }

            if(in_array('feature_image', $view_locations)) {

                add_filter('post_thumbnail_html', function($html, $id) use($template) {
                    global $post;
                    if($html && ($post->ID === $id)) // prevent insert in sidebar feature image 
                        return sprintf('<div class="thumb-social-wrap">%1$s %2$s</div>', $html, $template);
                    return $html;
                }, 10, 2); 

            }

        endif;
    }

    /*
     * Incldue shortcode
     * 
     * @since      1.0.0
     */

    public function shortcode() {
        require_once($this->get_path('includes/shortcode.php'));
    }

    /**
    * Include settings page
    * 
    * @since      1.0.0
    */
    public function plugin_settings() {
        if(!class_exists('Tpl_Share_Settings'))
            require_once($this->get_path('includes/class-tpl-share-settings.php'));

        new Tpl_Share_Settings($this);
    }



    /*
     * All actions hook.
     * 
     * @since      1.0.0
     */
    public function hooks() {
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts')); // back-end scripts
		add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts')); // front-end scripts
    }

    /**
     * Enqueue admin styles and scripts
     * 
     * @since      1.0.0
     */
    public function admin_enqueue_scripts() {
        $screen = get_current_screen();
        if($screen->id === 'toplevel_page_tpl-social-settings'):
            wp_enqueue_script('selectize', $this->get_url('assects/js/selectize.min.js'), array('jquery','jquery-ui-core','jquery-ui-sortable'), false, true);
            wp_enqueue_script('tpl-socil-admin', $this->get_url('assects/js/admin.js'), array('jquery','jquery-ui-core','jquery-ui-sortable'), false, true);
            wp_enqueue_style('selecttize', $this->get_url('assects/css/selectize.css'));
            wp_enqueue_style('tpl-socil-admin', $this->get_url('assects/css/admin.css'));
        endif;
    }

    /**
     * Enqueue public styles and scripts
     * 
     * @since      1.0.0
     */
    public function wp_enqueue_scripts() {
        wp_enqueue_script('tpl-socil-bar', $this->get_url('assects/js/style.js'), array('jquery'), false, true);
        wp_enqueue_style('tpl-socil-bar', $this->get_url('assects/css/style.css'));
    }

    /**
    * Check is satisfy dependency 
    * 
    * @since      1.0.0
    * @return bool
     */
    public function dependency_ok() {
        $dependency_errors = $this->get_dependency_errors();
		return 0 === count( $dependency_errors );
    }

    /**
    * Return required dependency
    * 
    * @since      1.0.0
    * @return array 
    */

    public function get_dependency_errors() {
        $errors = array();

        $wordpress_version = get_bloginfo( 'version' );
        $minimum_wordpress_version   = '4.5';
        $minimum_php_version = '5.6';
        $php_version = phpversion();

        $wordpres_has_min = version_compare( $wordpress_version, $minimum_wordpress_version, '>=' );
        $php_has_min = version_compare( $php_version, $minimum_php_version, '>=' );

        if ( ! $wordpres_has_min ) {
			$errors[] = sprintf(
				__( 'The Pluginsify Social Share  plugin requires <a href="%1$s">WordPress</a> %2$s or greater to be installed and active.', 'tpl-social-share' ),
				'https://wordpress.org/',
				$minimum_wordpress_version
			);
        }
        
        if ( ! $php_has_min ) {
			$errors[] = sprintf(
				__( 'The Pluginsify Social Share plugin requires php version %1$s or greater. Please upgrate your server php version.', 'tpl-social-share' ),
				$minimum_php_version
			);
        }
        
        return $errors;
    }

    /**
    * Show plugin requied dependency error notice.
    * 
    * @since      1.0.0
    */
    public function display_dependency_notice() {
		$message = $this->get_dependency_errors();
		printf( '<div class="error"><p>%s</p></div>', implode( ' ', $message ) );
    }

    /**
    * Get settings
    * 
    * @since      1.0.0
    *
    * @return  array 
    */
    public function get_settings() {
		return  get_option($this->get_setting_id(), true);
    }

    /**
    * Get Template
    * 
    * @since      1.0.0
    * @param string $file an string of relative path of plugin root
    * @return  string
    */
    public function get_template() {
        $settings = $this->get_settings();
        $template = require($this->get_path('templates/social-bar.php'));
        return $template;
    }

    /**
    * Return absolute file path
    * 
    * @since      1.0.0
    * @param string $file an string of relative path of plugin root
    * @return  string
    */
    public function get_path($file) {
		return  plugin_dir_path( __FILE__ ) . $file;
    }


    /**
    * Return file public link
    * 
    * @since      1.0.0
    * @param string $file an string of relative path of plugin root
    * @return  string
    */
    public function get_url($file) {
		return plugin_dir_url( __FILE__ ) . $file;
    }


    /**
    * Return share link for social media
    * 
    * @since      1.0.0
    * @param string $social_media name of social media
    * @return  string URL of share link
    */
    public function get_share_url( $social_media) {
        $title = get_the_title();
        $url = get_the_permalink();
        $image = wp_get_attachment_image_src(get_post_thumbnail_id());
        
        switch($social_media){
            case 'twitter':
                return 'https://twitter.com/share?url='.$url.'&text='.$title;
                break;
    
            case 'facebook':
                return 'https://www.facebook.com/sharer.php?u='.$url;
                break;

            case 'linkedin':
                return 'https://www.linkedin.com/shareArticle?url='.$url.'&title='.$title;
                break;
    
            case 'pinterest':
                $pin_link = 'https://pinterest.com/pin/create/bookmarklet/?&url='.$url.'&description='.$title;

                if(!empty($image)) {
                    $pin_link = 'https://pinterest.com/pin/create/bookmarklet/?media='.$image[0].'&url='.$url.'&description='.$title;
                } 
                return $pin_link;
                break;

            case 'whatsapp':
                return 'whatsapp://send?text='.$title . ' '.$url;
                break;
    
            default:
                return $url;
                break;
        }
    }

    /**
    * Get plugins settings id
    * 
    * @since      1.0.0
    */
    public function get_setting_id() {
        return self::TPL_SHARE_SETTINGS_ID;
    }

    /**
    * Deactive plugin itself
    * 
    * @since      1.0.0
    */
    public function deactivate_self() {
		deactivate_plugins(plugin_basename( __FILE__ ));
    }

    public static function init() {
        if(is_null(self::$instance)) 
            self::$instance = new self;

        return self::$instance;
    }
}
endif;

Tpl_Social_Share::init();