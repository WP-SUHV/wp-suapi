<?php

if (!defined('ABSPATH')) {
    exit;
}
use WP_SUAPI\WP_SUAPI_Post_Types;

define('WP_SUAPI_DEBUG', true);

class WP_SUAPI
{

    /**
     * The single instance of WP_SUAPI.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * Holds all post types objects
     * @var WP_SUAPI_PostTypes
     */
    public $post_types;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.0.0')
    {
        $this->_version = $version;
        $this->_token = 'wp-suapi';

        // Load plugin environment variables
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->script_suffix = defined('WP_SUAPI_DEBUG') && WP_SUAPI_DEBUG ? '' : '.min';

        register_activation_hook($this->file, array($this, 'install'));

        // Load frontend JS & CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 10);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 10);

        // Load admin JS & CSS
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);

        // Load API for generic admin functions
        if (is_admin()) {
            $this->admin = new WP_SUAPI_Admin_API();
        }

        // Handle localisation
        $this->load_plugin_textdomain();
        add_action('init', array($this, 'load_localisation'), 0);
        add_action('init', array($this, 'register_cpt'), 0);
    } // End __construct ()

    /**
     * Loads CPT
     */
    public function register_cpt()
    {
        $this->post_types = new WP_SUAPI_Post_Types();
    }

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles()
    {
        wp_register_style($this->_token . '-frontend', esc_url($this->assets_url) . 'css/' . $this->_token . '-frontend' . $this->script_suffix . '.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-frontend');
    } // End enqueue_styles ()

    /**
     * Load frontend Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_scripts()
    {
        if (defined('WP_SUAPI_DEBUG') && WP_SUAPI_DEBUG) {
            wp_register_script($this->_token . '-frontend', esc_url($this->assets_url) . 'js/src/' . $this->_token . '-frontend' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        } else {
            wp_register_script($this->_token . '-frontend', esc_url($this->assets_url) . 'js/' . $this->_token . '-frontend' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        }
        wp_enqueue_script($this->_token . '-frontend');
    } // End enqueue_scripts ()

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles($hook = '')
    {
        wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/' . $this->_token . '-admin' . $this->script_suffix . '.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-admin');
    } // End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts($hook = '')
    {
        if (defined('WP_SUAPI_DEBUG') && WP_SUAPI_DEBUG) {
            wp_register_script($this->_token . '-admin', esc_url($this->assets_url) . 'js/src/' . $this->_token . '-admin' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        } else {
            wp_register_script($this->_token . '-admin', esc_url($this->assets_url) . 'js/' . $this->_token . '-admin' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        }
        wp_enqueue_script($this->_token . '-admin');
    } // End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation()
    {
        load_plugin_textdomain('wp-suapi', false, dirname(plugin_basename($this->file)) . '/lang/');
    } // End load_localisation ()

    /**
     * Load plugin textdomain
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain()
    {
        $domain = 'wp-suapi';

        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, dirname(plugin_basename($this->file)) . '/lang/');
    } // End load_plugin_textdomain ()

    /**
     * Main WP_SUAPI Instance
     *
     * Ensures only one instance of WP_SUAPI is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WP_SUAPI()
     * @return Main WP_SUAPI instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }

        return self::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    } // End __wakeup ()

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->_log_version_number();
    } // End install ()

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number()
    {
        update_option($this->_token . '_version', $this->_version);
    } // End _log_version_number ()

}
