<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<h1 class="entry-title"><?php echo esc_html( kwd_minimal_title() ); ?></h1>
<div class="entry-content"><?php the_content(); wp_link_pages( array( 'before' => '<nav class="page-links" aria-label="' . esc_attr__( '記事内のページ', 'kwd-minimal' ) . '">', 'after' => '</nav>' ) ); ?></div>
</article>
<?php kwd_minimal_related(); ?>
<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
<?php endwhile; get_footer(); ?>
