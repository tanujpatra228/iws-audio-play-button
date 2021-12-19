<?php
/**
 * Plugin Name: IWS - Audio play button
 * Description: Upload audio file in post and add the play button using this shortcode <code>[iws-audio-button]</code>
 * Author: ITs. Web Space
 * Author URI: https://www.itswebspace.in/about/
 * Version: 1.0.0
 * Requires at least: 5.7
 * Requires PHP: 7.2
 * Plugin URI: https://www.itswebspace.in/plugins/custom-plugin/?source=wp_dash
 * Text Domain: iws-audio-play-button
 */

 // Terminate if accessed directly
if(!defined('ABSPATH')){
    die();
}

define('IWS_APB_TXT_DOMAIN', 'iws-audio-play-button');
define('IWS_APB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IWS_APB_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Enqueue 
 */
function iws_cp_enqueue_scripts(){
    $script_src = IWS_APB_PLUGIN_URL.'assets/iws-audio-play-button.js';
    $script_ver = filemtime(IWS_APB_PLUGIN_PATH.'assets/iws-audio-play-button.js');

    wp_enqueue_script('iws-audio-play-button', $script_src, array('jquery'), $script_ver, true);
}
add_action('wp_enqueue_scripts', 'iws_cp_enqueue_scripts');


/**
 * Add custom field on posts
 */
function iws_cp_meta_fields(){
    $url = get_post_meta(get_the_ID(), 'iws-embed-audio', true);
    ?>
    <label for="iws-embed-audio">Select Audio file: </label>
    <input type="file" accept="audio/mp3" name="iws-embed-audio" id="iws-embed-audio" />
    <?php
}
function iws_cp_meta_box(){
    add_meta_box('iws-audio-meta-box', 'IWS Audio', 'iws_cp_meta_fields', 'post');
}
add_action('add_meta_boxes', 'iws_cp_meta_box');

function iws_cp_save_audio($post_id){
    if($_FILES['iws-embed-audio']['error'] != 0) return;

    $file = $_FILES['iws-embed-audio'];
    $ext = explode('.', $file['name']);
    $ext = $ext[count($ext)-1];
    $file_name = "$post_id.$ext";   // 5.mp3
    
    if(!metadata_exists('post', $post_id, 'iws_embed_audio_url')){
        $audio_file = wp_upload_bits($file_name, null, file_get_contents($file['tmp_name']));
        add_post_meta($post_id, 'iws_embed_audio_url', $audio_file['url']);
        add_post_meta($post_id, 'iws_embed_audio_path', esc_sql($audio_file['file']));
    }else{
        $iws_embed_audio = get_post_meta($post_id, 'iws_embed_audio_path');
        wp_delete_file($iws_embed_audio);
        $audio_file = wp_upload_bits($file_name, null, file_get_contents($file['tmp_name']));
        update_post_meta($post_id, 'iws_embed_audio_url', $audio_file['url']);
        update_post_meta($post_id, 'iws_embed_audio_path', esc_sql($audio_file['file']));
    }

}
add_action('save_post', 'iws_cp_save_audio');

function iws_play_audio(){
    $post_id = get_the_id();
    $audio_url = get_post_meta($post_id, 'iws_embed_audio_url', true);

    return "<div><div style='display:flex; align-item:center; gap:5px;'><button class='iws-play-btn' data-url='$audio_url'>Play Audio</button><span class='iws-audio' style='display:flex;'></span></div></div>";
}
add_shortcode('iws-audio-button', 'iws_play_audio');