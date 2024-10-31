<?php 
/**
 * Post type selector
 * 
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if direct file access

function post_type_checkbox($field, $settings, $setting_id) {
    extract($field);

    $value = isset($settings[$id]) ? $settings[$id] : array();
    $field_name = $setting_id.'['.$id.'][]';

    $post_types = get_post_types(
        array('public' => true),
        'objects'
    ); ?>

    <fieldset class="tpl-field-<?php echo $type; ?>" id="tpl-field-<?php echo $id; ?>">
    <?php foreach($post_types as $post_type): ?>
            <label>
                <?php
                    $is_checked = in_array($post_type->name, $value) ? ' checked' : '';
                    printf(
                        '<input type="checkbox"  name="%1$s" value="%2$s" %3$s>%4$s',
                        $field_name,
                        $post_type->name,
                        $is_checked,
                        $post_type->label
                    );
                ?>
            </label>
        
    <?php endforeach; ?>
    </fieldset>

    <?php if(isset($description)): ?>
        <p class="description"><?php echo $description; ?></p>
    <?php endif;
}


/**
 * Input type select
 * 
 * @since 1.0.0
 */
function checkbox($field, $settings, $setting_id) {
    extract($field);

    $value = isset($settings[$id]) ? $settings[$id] : array();
   
    $field_name = $setting_id.'['.$id.'][]';
    ?>

    <fieldset class="tpl-field-<?php echo $type; ?>" id="tpl-field-<?php echo $id; ?>">
    <?php foreach($options as $key => $option): ?>
            <label>
                <?php
                    $is_checked = in_array($key, $value) ? ' checked' : '';
                    printf(
                        '<input type="checkbox"  name="%1$s" value="%2$s" %3$s>%4$s',
                        $field_name,
                        $key,
                        $is_checked,
                        $option
                    );
                ?>
            </label>
        
    <?php endforeach; ?>
    </fieldset>

    <?php if(isset($description)): ?>
        <p class="description"><?php echo $description; ?></p>
    <?php endif;
}

/**
 * Wrapper input, child is color picker
 * 
 * @since 1.0.0
 */
function wrapper($field, $settings, $setting_id) {
    extract($field);

    $value = isset($settings[$id]) ? $settings[$id] : array();
    ?>

    <fieldset class="tpl-field-<?php echo $type; ?>" id="tpl-field-<?php echo $id; ?>">
    <?php foreach($childs as $option): 
        $field_name = $setting_id.'['.$id.']'.'['.$option['id'].']';
        $child_value = isset($value[$option['id']]) ? $value[$option['id']]: '#ff0000';
    ?>
            <label>
                <?php
                    if($option['type'] == 'color_picker'):
                    printf(
                        '%3$s <input type="color" name="%1$s" value="%2$s">',
                        $field_name,
                        $child_value,
                        $option['title']
                    );
                    endif; // only need color_picker 
                ?>
            </label>
        
    <?php endforeach; ?>
    </fieldset>

    <?php if(isset($description)): ?>
        <p class="description"><?php echo $description; ?></p>
    <?php endif;
}


/**
 * Input type radio
 * 
 * @since 1.0.0
 */
function readio_button($field, $settings, $setting_id) {
    extract($field);

    $value = isset($settings[$id]) ? $settings[$id] : '';
    $field_name = $setting_id.'['.$id.']';

    ?>

    <fieldset class="tpl-field-<?php echo $type; ?>" id="tpl-field-<?php echo $id; ?>">
    <?php foreach($options as $key => $option): ?>
            <label>
                <?php
                    $is_checked = ($key === $value) ? ' checked' : '';
                    printf(
                        '<input type="radio"  name="%1$s" value="%2$s" %3$s>%4$s',
                        $field_name,
                        $key,
                        $is_checked,
                        $option
                    );
                ?>
            </label>
        
    <?php endforeach; ?>
    </fieldset>

    <?php if(isset($description)): ?>
        <p class="description"><?php echo $description; ?></p>
    <?php endif;
}


/**
 * Advance multiput select box
 * 
 * @since 1.0.0
 */
function advance_multi_select($field, $settings, $setting_id) {
    extract($field);

    $value = isset($settings[$id]) ? $settings[$id] : array();
    $field_name = $setting_id.'['.$id.'][]';
    ?>

    <fieldset class="tpl-field-<?php echo $type; ?>" id="tpl-field-<?php echo $id; ?>">
    <select name="<?php echo $field_name; ?>" multiple>

    <?php 
    $options = array_replace(array_flip($value), $options); // sort as select items;

    foreach($options as $key => $option): 
        $is_selected = in_array($key, $value) ? ' selected' : '';    
    ?>
        <?php 
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                $key,
                $is_selected,
                $option
            );
        ?>
            
    <?php endforeach; ?>
    </select>
    </fieldset>

    <?php if(isset($description)): ?>
        <p class="description"><?php echo $description; ?></p>
    <?php endif;
}