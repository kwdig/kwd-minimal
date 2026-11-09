<?php
/** Optional descriptions built with WordPress excerpts. */
defined( 'ABSPATH' ) || exit;

function kwd_minimal_has_seo_plugin() {
    return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' )
        || function_exists( 'aioseo' ) || defined( 'SEOPRESS_VERSION' )
        || function_exists( 'the_seo_framework' );
}

function kwd_minimal_clean_description( $text ) {
    if ( ! is_string( $text ) ) { return ''; }
    $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $text = wp_strip_all_tags( strip_shortcodes( $text ) );
    $text = preg_replace( '/[\s\x{00a0}\x{3000}]+/u', ' ', $text );
    $text = preg_replace( '/\s*\[\s*(?:…|\.{3})\s*\]\s*$/u', '', trim( $text ) );
    return trim( wp_html_excerpt( $text, 120, '' ) );
}

function kwd_minimal_description_excerpt_length() { return 120; }
function kwd_minimal_description_excerpt_more() { return ''; }

function kwd_minimal_description_excerpt( $post ) {
    $post = get_post( $post );
    if ( ! $post || '' !== $post->post_password ) { return ''; }
    // Scope these changes to this call; archive excerpts keep their own settings.
    add_filter( 'excerpt_length', 'kwd_minimal_description_excerpt_length', PHP_INT_MAX );
    add_filter( 'excerpt_more', 'kwd_minimal_description_excerpt_more', PHP_INT_MAX );
    try {
        $text = get_the_excerpt( $post );
    } finally {
        remove_filter( 'excerpt_length', 'kwd_minimal_description_excerpt_length', PHP_INT_MAX );
        remove_filter( 'excerpt_more', 'kwd_minimal_description_excerpt_more', PHP_INT_MAX );
    }
    return kwd_minimal_clean_description( $text );
}

function kwd_minimal_post_description( $post ) {
    $post = get_post( $post );
    if ( ! $post || '' !== $post->post_password ) { return ''; }
    $manual = kwd_minimal_clean_description( get_post_meta( $post->ID, '_kwd_meta_description', true ) );
    return '' !== $manual ? $manual : kwd_minimal_description_excerpt( $post );
}

function kwd_minimal_meta_description() {
    if ( is_admin() || is_feed() || is_embed() || is_search() || is_404() || is_author() || is_date() ) {
        return '';
    }
    if ( ! apply_filters( 'kwd_minimal_meta_description_enabled', get_theme_mod( 'kwd_minimal_meta_description_enabled', true ) )
        || kwd_minimal_has_seo_plugin() ) { return ''; }
    $text = '';
    if ( is_front_page() && 'posts' === get_option( 'show_on_front' ) ) {
        $text = kwd_minimal_clean_description( get_theme_mod( 'kwd_minimal_front_description', '' ) );
        if ( '' === $text ) { $text = get_bloginfo( 'description', 'raw' ); }
    } elseif ( is_home() ) {
        $text = kwd_minimal_clean_description( get_theme_mod( 'kwd_minimal_posts_description', '' ) );
        if ( '' === $text ) { $text = get_bloginfo( 'description', 'raw' ); }
    } elseif ( is_singular( array( 'post', 'page' ) ) ) {
        $text = kwd_minimal_post_description( get_queried_object_id() );
    } elseif ( is_category() || is_tag() ) {
        $term = get_queried_object();
        $text = $term instanceof WP_Term ? $term->description : '';
    }
    return kwd_minimal_clean_description( $text );
}

function kwd_minimal_output_meta_description() {
    $text = kwd_minimal_meta_description();
    if ( '' !== $text ) {
        echo '<meta name="description" content="' . esc_attr( $text ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'kwd_minimal_output_meta_description', 5 );

function kwd_minimal_description_meta_boxes() {
    foreach ( array( 'post', 'page' ) as $type ) {
        add_meta_box( 'kwd-minimal-description', __( 'メタディスクリプション', 'kwd-minimal' ),
            'kwd_minimal_description_meta_box', $type, 'normal', 'default',
            array( '__block_editor_compatible_meta_box' => true ) );
    }
}
add_action( 'add_meta_boxes', 'kwd_minimal_description_meta_boxes' );

function kwd_minimal_description_meta_box( $post ) {
    wp_nonce_field( 'kwd_minimal_save_description', 'kwd_minimal_description_nonce' );
    $value = get_post_meta( $post->ID, '_kwd_meta_description', true );
    echo '<p><label for="kwd-minimal-description-input">' . esc_html__( '任意の説明文（空欄なら、抜粋または本文から自動生成）', 'kwd-minimal' ) . '</label></p>';
    echo '<textarea id="kwd-minimal-description-input" name="kwd_minimal_description" rows="3" class="widefat">' . esc_textarea( is_string( $value ) ? $value : '' ) . '</textarea>';
    echo '<p class="description">' . esc_html__( '出力は最大120文字。検索結果の表示文は検索エンジンが判断します。SEOプラグイン利用時とパスワード保護記事ではテーマから出力しません。', 'kwd-minimal' ) . '</p>';
    echo '<p><strong>' . esc_html__( '保存済みの抜粋・本文からの生成例：', 'kwd-minimal' ) . '</strong> ' . esc_html( kwd_minimal_description_excerpt( $post ) ) . '</p>';
}

function kwd_minimal_save_description( $post_id ) {
    if ( ! isset( $_POST['kwd_minimal_description_nonce'], $_POST['kwd_minimal_description'] )
        || ! is_string( $_POST['kwd_minimal_description_nonce'] ) || ! is_string( $_POST['kwd_minimal_description'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kwd_minimal_description_nonce'] ) ), 'kwd_minimal_save_description' )
        || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id )
        || ! current_user_can( 'edit_post', $post_id ) || ! in_array( get_post_type( $post_id ), array( 'post', 'page' ), true ) ) { return; }
    $value = sanitize_textarea_field( wp_unslash( $_POST['kwd_minimal_description'] ) );
    if ( '' === trim( $value ) ) {
        delete_post_meta( $post_id, '_kwd_meta_description' );
    } else {
        update_post_meta( $post_id, '_kwd_meta_description', wp_slash( $value ) );
    }
}
add_action( 'save_post', 'kwd_minimal_save_description' );

function kwd_minimal_page_excerpt_support() { add_post_type_support( 'page', 'excerpt' ); }
add_action( 'init', 'kwd_minimal_page_excerpt_support' );

function kwd_minimal_customize_descriptions( $customize ) {
    $customize->add_section( 'kwd_minimal_descriptions', array(
        'title' => __( 'メタディスクリプション', 'kwd-minimal' ), 'priority' => 135,
        'description' => __( '記事は手動の説明文・抜粋・本文の順で生成します。カテゴリーとタグは説明欄が空なら出力せず、著者・日付別一覧でも出力しません。', 'kwd-minimal' ),
    ) );
    $customize->add_setting( 'kwd_minimal_meta_description_enabled', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
    $customize->add_control( 'kwd_minimal_meta_description_enabled', array(
        'section' => 'kwd_minimal_descriptions', 'type' => 'checkbox', 'label' => __( 'テーマから説明文を出力する', 'kwd-minimal' ),
    ) );
    foreach ( array(
        'kwd_minimal_front_description' => __( 'トップページ（最新の投稿）の説明文', 'kwd-minimal' ),
        'kwd_minimal_posts_description' => __( '別に指定した投稿一覧ページの説明文', 'kwd-minimal' ),
    ) as $key => $label ) {
        $customize->add_setting( $key, array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
        $customize->add_control( $key, array( 'section' => 'kwd_minimal_descriptions', 'type' => 'textarea', 'label' => $label,
            'description' => __( '空欄ならサイトのキャッチフレーズを使用。固定ページをトップに指定した場合は、そのページの説明文・抜粋・本文を使用します。', 'kwd-minimal' ),
        ) );
    }
}
add_action( 'customize_register', 'kwd_minimal_customize_descriptions' );
