<?php

function remove_original_js(){
    global $new_instance;
    remove_action('wp_enqueue_scripts', array( $new_instance,'add_conversation_scripts') );
    add_action('wp_enqueue_scripts', 'cs_add_conversation_scripts');
}
add_action('after_setup_theme', 'remove_original_js', 999);


function cs_add_conversation_scripts() {
    global $current_user;
    wp_enqueue_script('conversation', get_stylesheet_directory_uri() . '/js/conversation.js', array(
        'jquery',
        'underscore',
        'backbone',
        'appengine',
        'front',
        'mjob-auth',
        'ae-message-js', 'fb_chat'), ET_VERSION, true);

    wp_localize_script('conversation', 'conversation_global', array(
        'file_max_size' => '',
        'file_types' => '',
        'conversation_title' => __('Conversation by ' . $current_user->display_name, 'enginethemes'),
        'message_title' => __('Message from ' . $current_user->display_name, 'enginethemes')
    ));
}
