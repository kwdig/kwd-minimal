<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<?php if ( is_search() ) : ?>
<h1 class="page-title"><?php printf( esc_html__( '「%s」の検索結果', 'kwd-minimal' ), esc_html( get_search_query( false ) ) ); ?></h1>
<?php elseif ( is_archive() ) : ?>
<h1 class="page-title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
<?php else : ?>
<h1 class="screen-reader-text"><?php esc_html_e( '記事一覧', 'kwd-minimal' ); ?></h1>
<?php endif; ?>
<?php if ( have_posts() ) : ?>
<ul class="title-list post-list" id="post-list">
<?php while ( have_posts() ) : the_post(); ?>
<li><a href="<?php the_permalink(); ?>"><?php echo esc_html( kwd_minimal_title() ); ?></a></li>
<?php endwhile; ?>
</ul>
<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => __( '前へ', 'kwd-minimal' ), 'next_text' => __( '次へ', 'kwd-minimal' ) ) ); ?>
<?php else : ?>
<p><?php esc_html_e( '記事が見つかりませんでした。メニュー内の検索もご利用ください。', 'kwd-minimal' ); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
