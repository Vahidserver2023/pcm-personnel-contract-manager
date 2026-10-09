<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="container inner">
        <div class="brand">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <?php bloginfo('name'); ?>
            </a>
        </div>
        <nav class="site-nav" aria-label="منوی اصلی">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'fallback_cb' => function () {
                    echo '<a href="' . esc_url(home_url('/')) . '">صفحه اصلی</a>';
                },
            ]);
            ?>
        </nav>
    </div>
</header>
