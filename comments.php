<?php defined( 'ABSPATH' ) || exit; if ( post_password_required() ) { return; } ?>
<section class="comments-area" aria-label="<?php esc_attr_e( 'コメント', 'kwd-minimal' ); ?>">
<?php if ( have_comments() ) : ?>
<h2><?php esc_html_e( 'コメント', 'kwd-minimal' ); ?></h2>
<ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 0 ) ); ?></ol>
<?php the_comments_navigation(); ?>
<?php endif; ?>
<?php comment_form(); ?>
</section>
