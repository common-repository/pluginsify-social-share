<?php 
/*
* Template for showing all social share output
*/
// global $settings;

defined( 'ABSPATH' ) || exit; // Exit if direct file access

$icons = apply_filters('tpl_social_icons', array(
    'facebook'  => '<i class="tpl-icon icon-facebook">&#xe801;</i>',
    'twitter'   => '<i class="tpl-icon icon-twitter">&#xe800;</i>',
    'linkedin'  => '<i class="tpl-icon icon-linkedin">&#xe802;</i>',
    'pinterest' => '<i class="tpl-icon icon-pinterest">&#xe803;</i>',
    'whatsapp'  => '<i class="tpl-icon icon-whatsapp">&#xf232;</i>',
));

$html = '';

$html .= '<div class="tpl-social-share-bar icons-size-'.$settings['icon_size'].'">';
    $html .= apply_filters('tpl_share_title', sprintf('<span class="tpl-share-label">%s</span>', __('Share:','tpl-socail-share')));
    $html .= '<ul>';

        foreach($settings['visiblity'] as $media): 

            if(!wp_is_mobile() && $media === 'whatsapp') continue;

            $html .= '<li class="tpl-social-'.$media.'">';

                $html .= sprintf('<a href="%s" style="color:%s" target="_blank">%s</a>', 
                    $this->get_share_url($media), 
                    $settings['icons_color'][$media], 
                    $icons[$media]
                );

            $html .= '</li>';
        endforeach;

    $html .= '<ul>';
$html .= '</div>';

return apply_filters('tpl_social_bar', $html, $settings);