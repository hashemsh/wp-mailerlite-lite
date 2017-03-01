<?php
class WP_Mailerlite
{
    private static $plugin_name = 'wp-mailerlite-lite';
    private static $version = '1.0.0';

    public function __construct()
    {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        new WPMI_Actions();
    }

    private function load_dependencies()
    {
        require_once WPMI_PATH.'includes/wpmi-i18n.php';
        require_once WPMI_PATH.'includes/vendor/autoload.php';
        require_once WPMI_PATH.'includes/wpmi-admin.class.php';
        require_once WPMI_PATH.'includes/wpmi-public.class.php';
        require_once WPMI_PATH.'includes/wpmi-actions.class.php';
        require_once WPMI_PATH.'includes/wpmi-widget.class.php';
    }

    private function set_locale()
    {
        $plugin_i18n = new WPMI_i18n();
        add_action('plugins_loaded', array($plugin_i18n, 'load_plugin_textdomain'));
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new WPMI_Admin();
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
        add_action('admin_menu', array($plugin_admin, 'admin_menu'));
        add_action('admin_init', array($plugin_admin, 'admin_init'));
        add_action('widgets_init', array($plugin_admin, 'widgets_init'));
	    add_filter('plugin_row_meta', array($plugin_admin, 'add_setting_link_meta'), 10, 2 );

    }

    private function define_public_hooks()
    {
        $plugin_public = new WPMI_Public();
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));
        add_action('wp_ajax_mailerlite_add_subscriber', array($plugin_public, 'ajax_add_subscriber'));
        add_action('wp_ajax_nopriv_mailerlite_add_subscriber', array($plugin_public, 'ajax_add_subscriber'));

    }

    public static function get_name() {
        return self::$plugin_name;
    }

    public static function get_version() {
        return self::$version;
    }

}