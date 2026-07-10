<?php get_header(); ?>
<main class="container page-content">
<?php while(have_posts()): the_post(); ?>
<article><p class="eyebrow"><?php echo esc_html(get_the_date()); ?></p><h1><?php the_title(); ?></h1><?php if(has_post_thumbnail()) the_post_thumbnail('large'); ?><?php the_content(); ?></article>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
