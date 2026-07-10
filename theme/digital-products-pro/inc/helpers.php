<?php
if (!defined('ABSPATH')) exit;
function dpp_get($key,$fallback=''){ return get_theme_mod($key,$fallback); }
function dpp_section($name){ get_template_part('template-parts/sections/'.$name); }
function dpp_btn($text,$url,$class='primary'){
 printf('<a class="btn %s" href="%s">%s</a>',esc_attr($class),esc_url($url),esc_html($text));
}
