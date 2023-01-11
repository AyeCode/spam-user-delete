<?php
/*
Plugin Name: Spam User Delete
Description: Detects and deletes spam users.  Adds a sub menu item to the users menu item.
Version: 1.0.0
Author: AyeCode Ltd
*/

add_action('admin_menu', 'spam_user_detection_menu');

function spam_user_detection_menu() {
    add_users_page('Spam User Detection', 'Spam User Detection', 'manage_options', 'spam-user-detection', 'spam_user_detection_settings_page');
}

function spam_user_detection_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    echo '<div class="wrap">';
    echo '<h2>Spam User Detection</h2>';
    echo '<div class="notice notice-info is-dismissible"><p>Please make a BACKUP of your site before running this.  This only detects the recent spat of spam user registrations that are easily identifiale via the username.</p></div>';
    echo '<form action="" method="post">';
    echo '<input type="submit" name="find_spam_users" value="Find Spam Users" class="button-primary"> ';
    echo '<input type="submit" name="delete_spam_users" value="Delete Spam Users" class="button-secondary">';
    wp_nonce_field( 'spam_user_delete_nonce' );
    echo '</form>';
    echo '</div>';

    if (isset($_POST['find_spam_users'])) {
        find_spam_users();
    }

    if (isset($_POST['delete_spam_users'])) {
        delete_spam_users();
    }
}

function find_spam_users() {
    global $wpdb;
    
    check_admin_referer( 'spam_user_delete_nonce' );

    $spam_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users WHERE CAST( SUBSTR( display_name, -1, 1 ) AS BINARY ) IN ( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' ) AND CAST( SUBSTR( display_name, -2, 1 ) AS BINARY ) IN ( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' ) AND CAST( SUBSTR( display_name, -3, 1 ) AS BINARY ) NOT IN ( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' ) AND ID NOT IN (SELECT DISTINCT post_author FROM {$wpdb->posts})
        AND ID NOT IN (SELECT DISTINCT user_id FROM {$wpdb->comments})");

    echo '<div class="updated notice is-dismissible">';
    echo '<p>Found ' . $spam_users . ' spam users.</p>';
    echo '</div>';
}

function delete_spam_users() {
    global $wpdb;
    
    check_admin_referer( 'spam_user_delete_nonce' );

    $spam_users = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE CAST( SUBSTR( display_name, -1, 1 ) AS BINARY ) IN ( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' ) AND CAST( SUBSTR( display_name, -2, 1 ) AS BINARY ) IN ( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' ) AND CAST( SUBSTR( display_name, -3, 1 ) AS BINARY ) NOT IN ( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' ) AND ID NOT IN (SELECT DISTINCT post_author FROM {$wpdb->posts})
        AND ID NOT IN (SELECT DISTINCT user_id FROM {$wpdb->comments})");

    foreach ($spam_users as $spam_user) {
        wp_delete_user($spam_user);
    }

    echo '<div class="updated notice is-dismissible">';
    echo '<p>Deleted ' . count($spam_users) . ' spam users.</p>';
    echo '</div>';
}
