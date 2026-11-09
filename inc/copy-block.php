<?php
/** Copyable plain-text blocks. @license GPL-2.0-or-later */
defined( 'ABSPATH' ) || exit;

function kwd_minimal_register_copy_block() {
    if ( ! shortcode_exists( 'copy_block' ) ) {
        add_shortcode( 'copy_block', 'kwd_minimal_copy_block' );
    }
}
add_action( 'init', 'kwd_minimal_register_copy_block', 20 );

function kwd_minimal_copy_assets() {
    wp_enqueue_style( 'kwd-minimal-copy-block', get_template_directory_uri() . '/assets/copy-block.css', array(), '1.0.38' );
    wp_enqueue_script( 'kwd-minimal-copy-block', get_template_directory_uri() . '/assets/copy-block.js', array(), '1.0.38', array( 'in_footer' => true, 'strategy' => 'defer' ) );
}

function kwd_minimal_copy_assets_for_post() {
    global $shortcode_tags;
    $post = get_queried_object();
    if ( is_singular() && $post instanceof WP_Post
        && isset( $shortcode_tags['copy_block'] ) && 'kwd_minimal_copy_block' === $shortcode_tags['copy_block']
        && has_shortcode( $post->post_content, 'copy_block' ) ) {
        kwd_minimal_copy_assets();
    }
}
add_action( 'wp_enqueue_scripts', 'kwd_minimal_copy_assets_for_post' );

// Also cover a shortcode rendered late by a widget or another content source.
function kwd_minimal_copy_late_styles() {
    if ( wp_style_is( 'kwd-minimal-copy-block', 'enqueued' ) && ! wp_style_is( 'kwd-minimal-copy-block', 'done' ) ) {
        wp_print_styles( 'kwd-minimal-copy-block' );
    }
}
add_action( 'wp_footer', 'kwd_minimal_copy_late_styles', 5 );

function kwd_minimal_copy_block( $atts, $content = '' ) {
    $atts = shortcode_atts( array( 'label' => __( 'コピー', 'kwd-minimal' ) ), $atts, 'copy_block' );
    $id = wp_unique_id( 'kwd-copy-' );
    // Match the previous MU plugin: preserve its whitespace/entity handling.
    $text = trim( html_entity_decode( wp_strip_all_tags( str_replace( array( '<br />', '<br/>', '<br>', '</p>' ), array( "\n", "\n", "\n", "\n" ), (string) $content ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
    kwd_minimal_copy_assets();
    $copy_icon = '<svg class="kwd-copy-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"></rect><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"></path></svg>';
    $check_icon = '<svg class="kwd-check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"></path></svg>';
    return '<div class="kwd-copy-block kwd-copy-block--chat"><div class="kwd-copy-toolbar"><span class="kwd-copy-kind" aria-hidden="true">' . esc_html__( 'テキスト', 'kwd-minimal' ) . '</span><button type="button" class="kwd-copy-button" data-copy-target="' . esc_attr( $id ) . '" aria-label="' . esc_attr__( '枠内のテキストをコピー', 'kwd-minimal' ) . '">' . $copy_icon . $check_icon . '<span class="kwd-copy-label">' . esc_html( $atts['label'] ) . '</span></button><span class="kwd-copy-status" role="status" aria-live="polite" data-success="' . esc_attr__( 'コピーしました', 'kwd-minimal' ) . '" data-error="' . esc_attr__( 'コピーできませんでした。本文を選択してコピーしてください。', 'kwd-minimal' ) . '" data-error-label="' . esc_attr__( 'コピーできません', 'kwd-minimal' ) . '"></span></div><pre id="' . esc_attr( $id ) . '" class="kwd-copy-text" tabindex="0" aria-label="' . esc_attr__( 'コピー用テキスト', 'kwd-minimal' ) . '">' . esc_html( $text ) . '</pre></div>';
}
