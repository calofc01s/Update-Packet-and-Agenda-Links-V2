<?php
/**
 * Plugin Name: Update Packets and Agendas on Website V2
 * Description: Update specific packet and agenda links across the whole website with an option to mark meetings as cancelled, including cancellation notice. Version 2.1.
 * Version: 2.1
 * Author: Jesus Rodriguez
 */

// Function to add a menu item in the WordPress admin
function my_button_updater_menu_v2() {
    add_menu_page('Update Packets and Agendas on Website V2', 'Update Packets and Agendas V2', 'manage_options', 'my-button-updater-v2', 'my_button_updater_page_v2');
}
add_action('admin_menu', 'my_button_updater_menu_v2');

// Function to display the plugin's admin page
function my_button_updater_page_v2() {
    ?>
    <div class="wrap">
        <h2>Update Packet and Agenda Links V2</h2>
        <form method="post" action="options.php">
            <?php settings_fields('my-button-updater-settings-v2'); ?>
            <?php do_settings_sections('my-button-updater-settings-v2'); ?>
            <table class="form-table">
                <?php 
                $meeting_types = ['APC Board Meeting', 'APC TAC Meeting', 'APC Executive Committee', 'APC SSTAC'];
                foreach ($meeting_types as $type) {
                    $packet_option_name = strtolower(str_replace(' ', '_', $type)) . '_packet_v2';
                    $agenda_option_name = strtolower(str_replace(' ', '_', $type)) . '_agenda_v2';
                    $date_option_name = strtolower(str_replace(' ', '_', $type)) . '_date_v2';
                    $cancelled_option_name = strtolower(str_replace(' ', '_', $type)) . '_cancelled_v2';
                ?>
                <tr valign="top">
                    <th scope="row"><?php echo $type; ?> Packet</th>
                    <td><input type="text" name="<?php echo $packet_option_name; ?>" value="<?php echo esc_attr(get_option($packet_option_name)); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo $type; ?> Agenda</th>
                    <td><input type="text" name="<?php echo $agenda_option_name; ?>" value="<?php echo esc_attr(get_option($agenda_option_name)); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo $type; ?> Date and Time</th>
                    <td><input type="text" name="<?php echo $date_option_name; ?>" value="<?php echo esc_attr(get_option($date_option_name)); ?>" placeholder="YYYY-MM-DD HH:MM"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo $type; ?> Cancelled</th>
                    <td><input type="checkbox" name="<?php echo $cancelled_option_name; ?>" value="1" <?php checked(1, get_option($cancelled_option_name), true); ?>/> Mark as cancelled</td>
                </tr>
                <?php } ?>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Function to register the settings
function my_button_updater_settings_v2() {
    $meeting_types = ['APC Board Meeting', 'APC TAC Meeting', 'APC Executive Committee', 'APC SSTAC'];
    foreach ($meeting_types as $type) {
        register_setting('my-button-updater-settings-v2', strtolower(str_replace(' ', '_', $type)) . '_packet_v2');
        register_setting('my-button-updater-settings-v2', strtolower(str_replace(' ', '_', $type)) . '_agenda_v2');
        register_setting('my-button-updater-settings-v2', strtolower(str_replace(' ', '_', $type)) . '_date_v2');
        register_setting('my-button-updater-settings-v2', strtolower(str_replace(' ', '_', $type)) . '_cancelled_v2');
    }
}
add_action('admin_init', 'my_button_updater_settings_v2');

// Function to create shortcodes for each meeting type packet, agenda, date, and cancellation status
function my_custom_meeting_link_shortcode_v2($atts) {
    $atts = shortcode_atts(array(
        'type' => 'APC Board Meeting',
        'content' => 'packet', // default to packet
    ), $atts, 'custom_meeting_link_v2');

    $option_name = strtolower(str_replace(' ', '_', $atts['type'])) . '_' . $atts['content'] . '_v2';
    $option_date_name = strtolower(str_replace(' ', '_', $atts['type'])) . '_date_v2';
    $option_cancelled_name = strtolower(str_replace(' ', '_', $atts['type'])) . '_cancelled_v2';
    
    $link = esc_url(get_option($option_name));
    $date = get_option($option_date_name);
    $cancelled = get_option($option_cancelled_name);

    if ($date) {
        $date = DateTime::createFromFormat('Y-m-d H:i', $date);
        $formatted_date = $date ? $date->format('F j, Y – g:i a') : 'Date to follow';
    } else {
        $formatted_date = 'Date to follow';
    }

    $meeting_info = $atts['type'] . ' – ' . $formatted_date;

    if ($cancelled) {
        return $meeting_info . ' | Cancelled';
    }

    if (!$link || $link == '#') {
        return $meeting_info . ' | Details to follow';
    }

    $link_text = $meeting_info . ' | ' . (($atts['content'] == 'agenda') ? 'Agenda' : 'Packet');
    return '<a href="' . $link . '">' . $link_text . '</a>';
}
add_shortcode('custom_meeting_link_v2', 'my_custom_meeting_link_shortcode_v2');
?>
