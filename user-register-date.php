<?php
/*
Plugin Name: User Register Date for WP
Plugin URI: https://github.com/jjmontalban/user-register-date
Description: Show the user registration date in WordPress.
Version: 1.0
Author: JJMontalban
Author URI: https://jjmontalban.github.io
Text Domain: user-register-date
License: GPL-3.0-or-later
License URI: https://spdx.org/licenses/GPL-3.0-or-later.html
 */


if ( ! defined( 'ABSPATH' ) ) exit;
 // Load translations
function urdfw_load_textdomain() {
    load_plugin_textdomain( 'user-register-date', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'urdfw_load_textdomain' );

// Add registration date column in user list
function urdfw_add_registration_date_column( $columns ) {
    $columns['registration_date'] = __( 'Registration Date', 'user-register-date' );
    return $columns;
}
add_filter( 'manage_users_columns', 'urdfw_add_registration_date_column' );

// Display registration date in user list column
function urdfw_display_registration_date_in_column( $value, $column_name, $user_id ) {
    if ( $column_name == 'registration_date' ) {
        $user_info = get_userdata( $user_id );
        $registration_date = $user_info->user_registered;
        $formatted_date = date_i18n( get_option( 'date_format' ), strtotime( $registration_date ) );
        return $formatted_date;
    }
    return $value;
}
add_filter( 'manage_users_custom_column', 'urdfw_display_registration_date_in_column', 10, 3 );

// Add registration date field in user profile page
function urdfw_add_registration_date_field( $user ) {
    $registration_date = $user->user_registered;
    $formatted_date = date_i18n( get_option( 'date_format' ), strtotime( $registration_date ) );
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="registration_date"><?php esc_html_e('User Registration Date', 'user-register-date'); ?></label></th>
            <td>
                <input type="text" name="registration_date" id="registration_date" value="<?php echo esc_attr( $formatted_date ); ?>" class="regular-text" readonly="readonly" />
                <p class="description"><?php esc_html_e( 'The user registration date cannot be edited.', 'user-register-date' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'urdfw_add_registration_date_field' );
add_action( 'edit_user_profile', 'urdfw_add_registration_date_field' );


// PAllow sorting by column date
function urdfw_sortable_registration_date_column( $columns ) {
    $columns['registration_date'] = 'registration_date';
    return $columns;
}
add_filter( 'manage_users_sortable_columns', 'urdfw_sortable_registration_date_column' );

// Sort date column correctly
function urdfw_custom_sort_by_registration_date( $query ) {
    if ( ! is_admin() ) {
        return;
    }

    $orderby = $query->get( 'orderby' );

    if ( 'registration_date' === $orderby ) {
        $query->set( 'orderby', 'registered' );
    }
}
add_action( 'pre_get_users', 'urdfw_custom_sort_by_registration_date' );
