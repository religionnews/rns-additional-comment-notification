<?php
/*
Plugin Name: RNS Additional Comment Notification
Plugin URI: https://github.com/religionnews/rns-additional-comment-notification
Description: Send an email to an additional address when a comment is left on a WordPress site.
Version: 1.0
Author: David Herrera
Author URI:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Register and define the plugin settings
add_action( 'admin_init', 'rns_acn_admin_init' );
function rns_acn_admin_init() {
  register_setting(
    'discussion',
    'rns_acn_address',
    'rns_acn_validate_address'
  );
  add_settings_section(
    'rns_acn_main_section',
    'RNS Additional Comment Notification',
    'rns_acn_main_section_description', // The function that echos an explanation of the section
    'discussion'
  );
  add_settings_field(
    'rns_acn_address_section',
    'Email address',
    'rns_acn_address_input', // The function that echos the form field
    'discussion',
    'rns_acn_main_section' // The section in which to show the field
  );
}

// Explanation of the section
function rns_acn_main_section_description() {
  echo '<p>Enter an email address to be notified when a comment is submitted.</p>';
}

// Display and fill the form field
function rns_acn_address_input() {
  $address = get_option( 'rns_acn_address' );
  echo '<input id="rns_acn_text" name="rns_acn_address" type="text" size="50" value="' . $address . '" />';
}

// Validate input
function rns_acn_validate_address( $input ) {
  $valid = is_email( $input );
  if ( $valid != $input ) {
    add_settings_error(
      'rns_acn_text',
      'rns_acn_text_error',
      'Error: Not a valid email address',
      'error'
    );
    // Do not overwrite the old address if the input fails validation
    return get_option( 'rns_acn_address' );
  } else {
    return $valid;
  }
}

// Send an email to the saved address when a comment is submitted
add_action( 'comment_post', 'rns_acn_notify_address' );
function rns_acn_notify_address( $commentID ) {
  if ( get_option( 'rns_acn_address' ) ) {
    $blogname = get_bloginfo( 'blogname' );
    $to = get_option( 'rns_acn_address' );
    $subject = 'New comment at ' . $blogname;
    $body = 'A new comment has been submitted at ' . $blogname . '.

Comment:

' . get_comment_text( $commentID ) . '

Status: ' . wp_get_comment_status( $commentID ) . '

View in Dashboard: ' . admin_url('comment.php?action=editcomment&c=') . $commentID;

    wp_mail( $to, $subject, $body );
  }
}

