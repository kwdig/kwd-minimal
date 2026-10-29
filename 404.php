<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<h1 class="entry-title"><?php esc_html_e( 'ページが見つかりません', 'kwd-minimal' ); ?></h1>
<p><?php esc_html_e( 'メニュー内の検索をご利用いただくか、ホームへお戻りください。', 'kwd-minimal' ); ?></p>
<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'ホームへ', 'kwd-minimal' ); ?></a></p>
<?php get_footer(); ?>
