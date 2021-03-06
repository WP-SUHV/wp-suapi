<?php

if (!defined('ABSPATH')) {
  exit;
}

use WP_SUAPI\WP_SUAPI_API_Handler;

class WP_SUAPI_Settings
{

  /**
   * The single instance of WP_SUAPI_Settings.
   * @var    object
   * @access  private
   * @since    1.0.0
   */
  private static $_instance = null;

  /**
   * The main plugin object.
   * @var    object
   * @access  public
   * @since    1.0.0
   */
  public $parent = null;

  /**
   * Prefix for plugin settings.
   * @var     string
   * @access  public
   * @since   1.0.0
   */
  public $base = '';

  /**
   * Available settings for plugin.
   * @var     array
   * @access  public
   * @since   1.0.0
   */
  public $settings = array();

  public function __construct($parent)
  {
    $this->parent = $parent;

    $this->base = "wp-suapi_";

    // Initialise settings
    add_action('init', array($this, 'init_settings'), 11);

    // Register plugin settings
    add_action('admin_init', array($this, 'register_settings'));

    // Add settings page to menu
    add_action('admin_menu', array($this, 'add_menu_item'));

    // Add settings link to plugins page
    add_filter('plugin_action_links_' . plugin_basename($this->parent->file), array($this, 'add_settings_link'));
  }

  /**
   * Initialise settings
   * @return void
   */
  public function init_settings()
  {
    $this->settings = $this->settings_fields();
  }

  /**
   * Add settings page to admin menu
   * @return void
   */
  public function add_menu_item()
  {
    $page = add_options_page(__('SUAPI Settings', 'wp-suapi'), __('SUAPI Settings', 'wp-suapi'), 'manage_options', $this->parent->_token . '_settings', array($this, 'settings_page'));
    add_action('admin_print_styles-' . $page, array($this, 'settings_assets'));
  }

  /**
   * Load settings JS & CSS
   * @return void
   */
  public function settings_assets()
  {
    // We're including the WP media scripts here because they're needed for the image upload field
    // If you're not including an image upload then you can leave this function call out
    wp_enqueue_media();

    if (defined('WP_SUAPI_DEBUG') && WP_SUAPI_DEBUG) {
      wp_register_script($this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/src/' . $this->parent->_token . '-settings' . $this->parent->script_suffix . '.js', array('jquery'), '1.0.0');
    } else {
      wp_register_script($this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/' . $this->parent->_token . '-settings' . $this->parent->script_suffix . '.js', array('jquery'), '1.0.0');
    }
    wp_enqueue_script($this->parent->_token . '-settings-js');
  }

  /**
   * Add settings link to plugin list table
   *
   * @param  array $links Existing links
   *
   * @return array        Modified links
   */
  public function add_settings_link($links)
  {
    $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __('Settings', 'wp-suapi') . '</a>';
    array_push($links, $settings_link);
    return $links;
  }

  /**
   * Build settings fields
   * @return array Fields to be displayed on settings page
   */
  private function settings_fields()
  {
    $settings['apiconnection'] = array(
      'title' => __('API Connection', 'wp-suapi'),
      'description' => __('swiss unihockey API Connection', 'wp-suapi'),
      'fields' => array(
        array(
          'id' => 'api-url',
          'label' => __('API URL', 'wp-suapi'),
          'description' => __('URL to the swiss unihockey API.', 'wp-suapi'),
          'type' => 'text',
          'default' => 'https://api-v2.swissunihockey.ch/api',
          'placeholder' => __('https://api-v2.swissunihockey.ch/api', 'wp-suapi')
        ),
        array(
          'id' => 'api-key',
          'label' => __('API Key', 'wp-suapi'),
          'description' => __('Key to the API', 'wp-suapi'),
          'type' => 'text',
          'default' => '',
          'placeholder' => __('Your personal key', 'wp-suapi')
        ),
        array(
          'id' => 'api-version',
          'label' => __('API Version', 'wp-suapi'),
          'description' => __('Select the API Version', 'wp-suapi'),
          'type' => 'select',
          'options' => array('v1' => 'V1', 'v2' => 'V2'),
          'default' => 'v2'
        )
      )
    );

    if ($this->check_api_connection_setup()) {
      try {
        $apiHandler = WP_SUAPI_API_Handler::GET_INITIALIZED_API_HANDLER();
        if ($apiHandler->isConnected()) {
          $allClubs = array_reduce(
            $apiHandler->getClubs(),
            function (&$result, $item) {
              $result[$item->getClubId()] = $item->getClubName();
              return $result;
            },
            array()
          );
          $settings['apiconnection']['fields'][] =
            array(
              'id' => 'api-club',
              'label' => __('swiss unihockey Club', 'wp-suapi'),
              'description' => __('Select the swiss unihockey Club', 'wp-suapi'),
              'type' => 'select',
              'options' => $allClubs,
              'default' => ''
            );
        } else {
          $settings['apiconnection']['fields'][] =
            array(
              'id' => 'api-club',
              'label' => __('swiss unihockey Club', 'wp-suapi'),
              'description' => __('Select the swiss unihockey Club', 'wp-suapi'),
              'type' => 'select',
              'options' => array('noConnection' => 'No Connection'),
              'default' => 'noConnection'
            );
        }
      } catch (\GuzzleHttp\Exception\RequestException $e) {
        new Cuztom_Notice($this->_token . " RequestException: " . $e->getMessage() . " - " . $e->getResponse()->getReasonPhrase(), 'error');
      }
    }

    $settings['extra'] = array(
      'title' => __('Extra', 'wp-suapi'),
      'description' => __('WP SUAPI Plugin settings', 'wp-suapi'),
      'fields' => array(
        array(
          'id' => 'extra-usecache',
          'label' => __('Use cache', 'wp-suapi'),
          'description' => __('Cache the API results', 'wp-suapi'),
          'type' => 'checkbox',
          'default' => 'checked'
        )
      )
    );

    $settings = apply_filters($this->parent->_token . '_settings_fields', $settings);

    return $settings;
  }

  /**
   * Register plugin settings
   * @return void
   */
  public function register_settings()
  {
    if (is_array($this->settings)) {

      // Check posted/selected tab
      $current_section = '';
      if (isset($_POST['tab']) && $_POST['tab']) {
        $current_section = $_POST['tab'];
      } else {
        if (isset($_GET['tab']) && $_GET['tab']) {
          $current_section = $_GET['tab'];
        }
      }

      foreach ($this->settings as $section => $data) {

        if ($current_section && $current_section != $section) {
          continue;
        }

        // Add section to page
        add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token . '_settings');

        foreach ($data['fields'] as $field) {

          // Validation callback for field
          $validation = '';
          if (isset($field['callback'])) {
            $validation = $field['callback'];
          }

          // Register field
          $option_name = $this->base . $field['id'];
          register_setting($this->parent->_token . '_settings', $option_name, $validation);

          // Add field to page
          add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section, array('field' => $field, 'prefix' => $this->base));
        }

        if (!$current_section) {
          break;
        }
      }
    }
  }

  public function settings_section($section)
  {
    $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
    echo $html;
  }

  /**
   * Load settings page content
   * @return void
   */
  public function settings_page()
  {

    // Build page HTML
    $html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
    $html .= '<h2>' . __('SUAPI Settings', 'wp-suapi') . '</h2>' . "\n";

    $tab = '';
    if (isset($_GET['tab']) && $_GET['tab']) {
      $tab .= $_GET['tab'];
    }

    // Show page tabs
    if (is_array($this->settings) && 1 < count($this->settings)) {

      $html .= '<h2 class="nav-tab-wrapper">' . "\n";

      $c = 0;
      foreach ($this->settings as $section => $data) {

        // Set tab class
        $class = 'nav-tab';
        if (!isset($_GET['tab'])) {
          if (0 == $c) {
            $class .= ' nav-tab-active';
          }
        } else {
          if (isset($_GET['tab']) && $section == $_GET['tab']) {
            $class .= ' nav-tab-active';
          }
        }

        // Set tab link
        $tab_link = add_query_arg(array('tab' => $section));
        if (isset($_GET['settings-updated'])) {
          $tab_link = remove_query_arg('settings-updated', $tab_link);
        }

        // Output tab
        $html .= '<a href="' . $tab_link . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";

        ++$c;
      }

      $html .= '</h2>' . "\n";
    }

    $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

    // Get settings fields
    ob_start();
    settings_fields($this->parent->_token . '_settings');
    do_settings_sections($this->parent->_token . '_settings');
    $html .= ob_get_clean();

    $html .= '<p class="submit">' . "\n";
    $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
    $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'wp-suapi')) . '" />' . "\n";
    $html .= '</p>' . "\n";
    $html .= '</form>' . "\n";
    $html .= '</div>' . "\n";

    echo $html;
  }

  /**
   * Main WP_SUAPI_Settings Instance
   *
   * Ensures only one instance of WP_SUAPI_Settings is loaded or can be loaded.
   *
   * @since 1.0.0
   * @static
   * @see WP_SUAPI()
   * @return Main WP_SUAPI_Settings instance
   */
  public static function instance($parent)
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self($parent);
    }
    return self::$_instance;
  } // End instance()

  /**
   * Cloning is forbidden.
   *
   * @since 1.0.0
   */
  public function __clone()
  {
    _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
  } // End __clone()

  /**
   * Unserializing instances of this class is forbidden.
   *
   * @since 1.0.0
   */
  public function __wakeup()
  {
    _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
  }

  private function check_api_connection_setup()
  {
    return !empty(get_option("wp-suapi_api-url")) && !empty(get_option("wp-suapi_api-version"));
  }

}
