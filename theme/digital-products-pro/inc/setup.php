<?php
if (!defined('ABSPATH')) exit;
add_action('after_setup_theme',function(){
 load_theme_textdomain('digital-products-pro-full',DPP_DIR.'/languages');
 add_theme_support('title-tag');
 add_theme_support('post-thumbnails');
 add_theme_support('custom-logo',['height'=>80,'width'=>240,'flex-height'=>true,'flex-width'=>true]);
 add_theme_support('html5',['search-form','comment-form','comment-list','gallery','caption','style','script']);
 add_theme_support('align-wide');
 add_theme_support('responsive-embeds');
 add_theme_support('woocommerce');
 register_nav_menus(['primary'=>__('Primary Menu','digital-products-pro-full'),'footer'=>__('Footer Menu','digital-products-pro-full')]);
});
add_action('init',function(){
 register_block_pattern_category('dpp',['label'=>__('Digital Products Pro','digital-products-pro-full')]);
});
