<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
 <div class="container nav-wrap">
  <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
   <?php if(has_custom_logo()){the_custom_logo();}else{echo esc_html(get_bloginfo('name') ?: 'Digital Products Pro');} ?>
  </a>
  <nav class="main-nav">
   <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'items_wrap'=>'<ul>%3$s</ul>']); ?>
   <a class="nav-cta" href="<?php echo esc_url(dpp_get('dpp_primary_url','#pricing')); ?>">Buy Now</a>
   <button class="mode-toggle" type="button">◐</button>
  </nav>
  <button class="menu-toggle" type="button">☰</button>
 </div>
</header>
