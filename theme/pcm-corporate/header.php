<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('منوی اصلی', 'pcm-corporate'),
        'footer' => __('منوی فوتر', 'pcm-corporate'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('pcm-corporate-style', get_stylesheet_uri(), [], '1.0.0');
    wp_enqueue_style('vazirmatn', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/webfont/Vazirmatn.css', [], null);
});

add_action('widgets_init', function () {
    register_sidebar([
        'name' => __('Sidebar اصلی', 'pcm-corporate'),
        'id' => 'main-sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ]);
});
