<?php
namespace PCM;

if (!defined('ABSPATH')) {
    exit;
}

final class Plugin
{
    private static ?Plugin $instance = null;

    public static function instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->bootstrap();
    }

    private function bootstrap(): void
    {
        $this->load_dependencies();
        $this->register_hooks();
    }

    private function load_dependencies(): void
    {
        require_once PCM_PLUGIN_DIR . 'includes/Database/Schema.php';
        require_once PCM_PLUGIN_DIR . 'includes/Security/CapabilityManager.php';
        require_once PCM_PLUGIN_DIR . 'includes/API/RestBootstrap.php';
    }

    private function register_hooks(): void
    {
        register_activation_hook(PCM_PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(PCM_PLUGIN_FILE, [$this, 'deactivate']);

        add_action('init', [$this, 'register_post_types']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function activate(): void
    {
        if (function_exists('dbDelta')) {
            require_once PCM_PLUGIN_DIR . 'includes/Database/Schema.php';
            \
            PCM\Database\Schema::create_tables();
        }

        $capabilities = new \
            PCM\Security\CapabilityManager();
        $capabilities->register_roles_and_capabilities();

        flush_rewrite_rules();
    }

    public function deactivate(): void
    {
        flush_rewrite_rules();
    }

    public function register_post_types(): void
    {
        // Reserved for future custom post types if needed.
    }

    public function register_admin_menu(): void
    {
        add_menu_page(
            __('PCM Admin', 'pcm'),
            __('PCM Admin', 'pcm'),
            'pcm_manage_employees',
            'pcm-admin',
            [$this, 'render_admin_main'],
            'dashicons-businessperson',
            26
        );
    }

    public function render_admin_main(): void
    {
        echo '<div class="wrap"><h1>' . esc_html__('PCM Admin', 'pcm') . '</h1><p>' . esc_html__('Admin SPA is rendered through the React app.', 'pcm') . '</p></div>';
    }

    public function register_rest_routes(): void
    {
        $routes = new \PCM\API\RestBootstrap();
        $routes->register();
    }
}
