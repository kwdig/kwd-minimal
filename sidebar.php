<?php defined( 'ABSPATH' ) || exit;
if ( is_active_sidebar( 'sidebar-right' ) ) : ?>
<aside class="sidebar" aria-label="<?php esc_attr_e( '右サイドバー', 'kwd-minimal' ); ?>">
<?php dynamic_sidebar( 'sidebar-right' ); ?>
</aside>
<?php endif; ?>
