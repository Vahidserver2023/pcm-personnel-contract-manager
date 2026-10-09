<?php
/**
 * Plugin Name: Personnel Contract Manager
 * Description: HR, payroll, contracts, attendance and payroll regulation management for WordPress.
 * Version: 0.1.1
 * Author: PCM Team
 * Text Domain: pcm
 * Requires at least: 6.4
 * Requires PHP: 8.2
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('PCM_PLUGIN_FILE')) {
    define('PCM_PLUGIN_FILE', __FILE__);
}

if (!defined('PCM_PLUGIN_DIR')) {
    define('PCM_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('PCM_PLUGIN_URL')) {
    define('PCM_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once PCM_PLUGIN_DIR . 'includes/Plugin.php';

add_action('plugins_loaded', function () {
    \PCM\Plugin::instance();
});
