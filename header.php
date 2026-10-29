<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( '本文へ移動', 'kwd-minimal' ); ?></a>
<div class="site-shell">
<header class="site-header">
<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
<details class="site-menu">
<summary aria-label="<?php esc_attr_e( '検索とメニュー', 'kwd-minimal' ); ?>"><span class="hamburger" aria-hidden="true"><span></span><span></span><span></span></span><span class="screen-reader-text"><?php esc_html_e( '検索とメニュー', 'kwd-minimal' ); ?></span></summary>
<div class="menu-panel">
<?php get_search_form(); ?>
<nav aria-label="<?php esc_attr_e( 'メインナビゲーション', 'kwd-minimal' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'kwd_minimal_menu_fallback' ) ); ?></nav>
</div>
</details>
</header>
<hr class="site-rule">
<div class="site-layout<?php echo is_active_sidebar( 'sidebar-right' ) ? ' has-sidebar' : ''; ?>">
<main id="main-content" tabindex="-1">
<?php kwd_minimal_breadcrumbs(); ?>
