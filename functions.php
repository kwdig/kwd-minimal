<?php
/** KWD Minimal theme functions. */
defined( 'ABSPATH' ) || exit;

function kwd_minimal_setup() {
    load_theme_textdomain( 'kwd-minimal', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'editor-style.css' );
    register_nav_menus( array( 'primary' => __( 'ハンバーガーメニュー', 'kwd-minimal' ) ) );
    $GLOBALS['content_width'] = 640;
}
add_action( 'after_setup_theme', 'kwd_minimal_setup' );
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

function kwd_minimal_assets() {
    // Keep this small stylesheet in the document to avoid a render-blocking round trip.
    $css_file = get_stylesheet_directory() . '/style.css';
    $css = is_readable( $css_file ) ? file_get_contents( $css_file ) : false;
    if ( false !== $css ) {
        wp_register_style( 'kwd-minimal', false, array(), '1.0.32' );
        wp_enqueue_style( 'kwd-minimal' );
        wp_add_inline_style( 'kwd-minimal', $css );
    } else {
        wp_enqueue_style( 'kwd-minimal', get_stylesheet_uri(), array(), '1.0.32' );
    }
    wp_enqueue_script( 'kwd-minimal', get_template_directory_uri() . '/assets/theme.js', array(), '1.0.32', array( 'in_footer' => true, 'strategy' => 'defer' ) );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'kwd_minimal_assets' );

function kwd_minimal_widgets() {
    register_sidebar( array(
        'name' => __( '右サイドバー（広告300×300対応）', 'kwd-minimal' ),
        'id' => 'sidebar-right',
        'description' => __( '未設定なら本文640pxの中央寄せになります。検索はメニュー内にあります。', 'kwd-minimal' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'kwd_minimal_widgets' );

function kwd_minimal_title() {
    $title = get_the_title();
    return '' !== trim( $title ) ? $title : __( '（無題）', 'kwd-minimal' );
}

function kwd_minimal_menu_fallback() {
    echo '<ul class="menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'ホーム', 'kwd-minimal' ) . '</a></li>';
    wp_list_pages( array( 'title_li' => '' ) );
    echo '</ul>';
}

function kwd_minimal_breadcrumbs() {
    if ( is_front_page() ) { return; }
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'パンくず', 'kwd-minimal' ) . '"><ol><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'ホーム', 'kwd-minimal' ) . '</a></li>';
    $category_path = is_category() || is_singular( 'post' );
    if ( $category_path ) {
        $category = is_category() ? get_queried_object() : null;
        if ( ! $category ) {
            $categories = get_the_category( get_queried_object_id() );
            usort( $categories, static function ( $a, $b ) {
                $depth = count( get_ancestors( $b->term_id, 'category', 'taxonomy' ) ) - count( get_ancestors( $a->term_id, 'category', 'taxonomy' ) );
                return $depth ? $depth : $a->term_id - $b->term_id;
            } );
            $category = $categories ? $categories[0] : null;
        }
        if ( $category && ! is_wp_error( $category ) ) {
            $ids = array_merge( array_reverse( get_ancestors( $category->term_id, 'category', 'taxonomy' ) ), array( $category->term_id ) );
            foreach ( $ids as $id ) {
                $term = get_term( $id, 'category' );
                if ( ! $term || is_wp_error( $term ) ) { continue; }
                $url = get_term_link( $term );
                if ( is_wp_error( $url ) ) { continue; }
                $current = is_category() && (int) $id === (int) get_queried_object_id() ? ' aria-current="page"' : '';
                echo '<li><a href="' . esc_url( $url ) . '"' . $current . '>' . esc_html( $term->name ) . '</a></li>';
            }
        }
        echo '</ol></nav>';
        return;
    }
    if ( is_singular() ) {
        $object = get_queried_object();
        if ( is_page() ) {
            foreach ( array_reverse( get_post_ancestors( $object->ID ) ) as $ancestor ) {
                echo '<li><a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a></li>';
            }
        }
        $label = get_the_title( $object );
    } elseif ( is_search() ) {
        $label = sprintf( __( '「%s」の検索結果', 'kwd-minimal' ), get_search_query( false ) );
    } elseif ( is_404() ) {
        $label = __( 'ページが見つかりません', 'kwd-minimal' );
    } elseif ( is_home() ) {
        $label = get_the_title( get_option( 'page_for_posts' ) );
    } else {
        $label = wp_strip_all_tags( get_the_archive_title() );
    }
    echo '<li aria-current="page">' . esc_html( $label ) . '</li></ol></nav>';
}

/** Fill with same-category posts first, then recent posts, without duplicates. */
function kwd_minimal_related_ids( $post_id ) {
    $base = array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 10,
        'fields' => 'ids', 'ignore_sticky_posts' => true, 'no_found_rows' => true,
        'has_password' => false, 'post__not_in' => array( $post_id ),
        'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ) );
    $categories = wp_get_post_categories( $post_id );
    $ids = array();
    if ( ! is_wp_error( $categories ) && $categories ) {
        $query = new WP_Query( array_merge( $base, array( 'category__in' => $categories ) ) );
        $ids = $query->posts;
    }
    if ( count( $ids ) < 10 ) {
        $query = new WP_Query( array_merge( $base, array( 'posts_per_page' => 10 - count( $ids ), 'post__not_in' => array_merge( array( $post_id ), $ids ) ) ) );
        $ids = array_merge( $ids, $query->posts );
    }
    return $ids;
}

function kwd_minimal_related() {
    if ( post_password_required() ) { return; }
    $ids = kwd_minimal_related_ids( get_the_ID() );
    if ( ! $ids ) { return; }
    echo '<section class="related" aria-labelledby="related-title"><h2 id="related-title">' . esc_html__( '関連記事', 'kwd-minimal' ) . '</h2><ul class="title-list">';
    foreach ( $ids as $id ) {
        $title = get_the_title( $id );
        echo '<li><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html( trim( $title ) ? $title : __( '（無題）', 'kwd-minimal' ) ) . '</a></li>';
    }
    echo '</ul></section>';
}
