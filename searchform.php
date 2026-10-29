<?php defined( 'ABSPATH' ) || exit; $kwd_search_id = wp_unique_id( 'kwd-search-' ); ?>
<form role="search" aria-label="<?php esc_attr_e( 'メニュー内の検索', 'kwd-minimal' ); ?>" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
<label class="screen-reader-text" for="<?php echo esc_attr( $kwd_search_id ); ?>"><?php esc_html_e( 'サイト内検索', 'kwd-minimal' ); ?></label>
<input id="<?php echo esc_attr( $kwd_search_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query( false ) ); ?>" placeholder="<?php esc_attr_e( 'サイト内検索', 'kwd-minimal' ); ?>">
<button type="submit"><?php esc_html_e( '検索', 'kwd-minimal' ); ?></button>
</form>
