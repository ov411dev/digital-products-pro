<?php
if (!defined('ABSPATH')) exit;
add_action('wp_enqueue_scripts',function(){
 wp_enqueue_style('dpp-main',DPP_URI.'/assets/css/main.css',[],DPP_VERSION);
 wp_enqueue_script('dpp-main',DPP_URI.'/assets/js/main.js',[],DPP_VERSION,true);
 wp_localize_script('dpp-main','DPPTheme',['darkModeDefault'=>get_theme_mod('dpp_dark_mode_default',false)]);
});
