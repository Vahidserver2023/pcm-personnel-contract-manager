<?php
namespace PCM;

if (!defined('ABSPATH')) {
    exit;
}

final class Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->load_dependencies();
        $this->register_hooks();
    }

    private function load_dependencies(): void
    {
        require_once PCM_PLUGIN_DIR . 'includes/Database/Schema.php';
        require_once PCM_PLUGIN_DIR . 'includes/Security/CapabilityManager.php';
        require_once PCM_PLUGIN_DIR . 'includes/Security/NonceValidator.php';
        require_once PCM_PLUGIN_DIR . 'includes/API/RestBootstrap.php';
        require_once PCM_PLUGIN_DIR . 'includes/Payroll/SalaryRegulation.php';
        require_once PCM_PLUGIN_DIR . 'includes/Payroll/PayItem.php';
        require_once PCM_PLUGIN_DIR . 'includes/Payroll/SalaryCalculationEngine.php';
        require_once PCM_PLUGIN_DIR . 'includes/Payroll/PayrollService.php';
    }

    private function register_hooks(): void
    {
        register_activation_hook(PCM_PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(PCM_PLUGIN_FILE, [$this, 'deactivate']);

        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function activate(): void
    {
        if (function_exists('dbDelta')) {
            \PCM\Database\Schema::create_tables();
        }

        \PCM\Security\CapabilityManager::register_roles_and_capabilities();
        flush_rewrite_rules();
    }

    public function deactivate(): void
    {
        flush_rewrite_rules();
    }

    public function register_admin_menu(): void
    {
        if (!current_user_can('pcm_manage_employees')) {
            return;
        }

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
        echo '<div class="wrap"><h1>' . esc_html__('PCM Admin', 'pcm') . '</h1><p>' . esc_html__('Admin SPA loads through the React app.', 'pcm') . '</p></div>';
    }

    public function register_rest_routes(): void
    {
        $routes = new \PCM\API\RestBootstrap();
        $routes->register();
    }
}
