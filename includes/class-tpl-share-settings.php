<?php 
/**
 * Plugin Setting page
 */

defined( 'ABSPATH' ) || exit; // Exit if direct file access

class Tpl_Share_Settings {

    /**
     * Copy of plugins base class
     * @var Tpl_Social_Share
     */
    private $base;

    /**
    * Setting consturctor
    * Initialize all setting actions
    * 
    * @since      1.0.0
    */
    public function __construct($base) {
        $this->base = $base;

        add_action('admin_menu', array($this, 'menu_page'));
        add_action( 'admin_init',    array($this, 'register_settings'));
    }

    /**
    * Add Menu Page
    * Initialize plugin all actions
    * 
    * @since      1.0.0
    */
    public function menu_page() {
        add_menu_page(
            __('Pluginsify Social Share Settings','tpl-socail-share'),
            __('Pluginsify Social Share','tpl-socail-share'),
            'manage_options',
            'tpl-social-settings',
            array($this, 'view_page'),
            'dashicons-share',
            20
        );
    }

    public function view_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'tpl_messages', 'tpl_messages', __( 'Settings Saved', 'tpl-socail-share' ), 'updated' );
        }
    ?>
          <div class="wrap tpl-setting-page">
            <h1><?php _e('Settings','tpl-socail-share'); ?></h1>
            <p>Pluginsify Social Share Settings</p>

            <?php settings_errors( 'tpl_messages' ); ?>

            <form action="options.php" method="post">
                    <?php 
                        settings_fields('tpl_setting');
                        do_settings_sections('tpl-social-settings');
                        submit_button( 'Save Settings' ); 
                    ?>
                </form>
          </div>
    <?php
    }

    /**
    * Class constructor
    * Initialize plugin all actions
    * 
    * @since      1.0.0
    */
    public function register_settings() {
        register_setting( 'tpl_setting', $this->base->get_setting_id()); 
        add_settings_section('tpl-settings-section', '', '__return_false', 'tpl-social-settings');
        $this->add_settings_fields();
    }

    /**
    * Printing according field setting
    * 
    * @since      1.0.0
    */

    public function add_settings_fields() {
        $fields = $this->field_settings();

        foreach ($fields as $field): 
            add_settings_field( $field['id'], $field['title'], array($this, 'render_fields'), 'tpl-social-settings', 'tpl-settings-section', $field);
        endforeach;
    }

    /**
    * Printing according field setting
    * 
    * @since      1.0.0
    */
    public function render_fields($field) {

        require_once($this->base->get_path('includes/settings-fields-type.php'));
        $field_type = $field['type'];
        
        if(function_exists($field_type)) {
            $field_type($field, $this->base->get_settings(), $this->base->get_setting_id());
        }
    }

    /**
    * All field configurations
    * 
    * @since      1.0.0
    *
    * @return  array of settings field.
    */
    public function field_settings () {
        $fields = array(
            array(
                'id' => 'visiblity',
                'title' => 'Social Media',
                'type' => 'advance_multi_select',
                'options' => array(
                    'facebook' => 'Facebook',
                    'twitter'   => 'Twitter',
                    'pinterest'  => 'Pinterest',
                    'linkedin' => 'Linkedin',
                    'whatsapp' => 'Whatsapp'
                ),
                'description' => 'Select and sort by dragging  icons to show in page. Whatsapp icon will show only in mobile.'
            ),

            array(
                'id' => 'post_types',
                'title' => 'Post Types',
                'type' => 'post_type_checkbox',
                'description' => 'Select posts type whre icons show.'
            ),

            array(
                'id' => 'positions',
                'title' => 'View Position',
                'type' => 'checkbox',
                'description' => 'Select positions of the page where icons show. Use shortcode <strong class="code">[pgfyshare]</strong> to place it in custom locaton.',
                'options' => array(
                    'before_content' => 'Before Content',
                    'feature_image'  => 'Inside Feature Image',
                    'sticky_left'   => 'Sticky Left',
                    'after_content'  => 'After Content',
                    
                ),
            ),

            array(
                'id' => 'icons_color',
                'title' => 'Icon Color',
                'type' => 'wrapper',
                'childs' => array(
                    array(
                        'id' => 'facebook',
                        'title' => 'Facebook',
                        'type' => 'color_picker'
                    ),

                    array(
                        'id' => 'twitter',
                        'title' => 'Twitter',
                        'type' => 'color_picker'
                    ),

                    array(
                        'id' => 'pinterest',
                        'title' => 'Pinterest',
                        'type' => 'color_picker'
                    ),

                    array(
                        'id' => 'linkedin',
                        'title' => 'Linkedin',
                        'type' => 'color_picker'
                    ),

                    array(
                        'id' => 'whatsapp',
                        'title' => 'Whatsapp',
                        'type' => 'color_picker'
                    ),

                ),
                'description' => 'Change color of icon.'
            ),

            array(
                'id' => 'icon_size',
                'title' => 'Icon Size',
                'type' => 'readio_button',
                'options' => array(
                    'small' => 'Small',
                    'medium'   => 'Medium',
                    'large'  => 'Large',
                )
            ),
        );
        return $fields;
    }
}