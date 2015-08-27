<?php
/**
 * Include the TGM_Plugin_Activation class.
 */
require_once(__DIR__ . '/../vendor/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php');        
add_action('tgmpa_register', 'register_required_plugins' );

/**
 * Checks required plugins
 * @access  public
 * @since   1.0.0
 * @return  void
 */
function register_required_plugins () {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        array(
            'name'        => 'WordPress SEO by Yoast',
            'slug'        => 'wordpress-seo',
            'is_callable' => 'wpseo_init',
        )
    );

    $config = array(
        'id'           => 'tgmpa',                 
        'default_path' => '',                      
        'menu'         => 'tgmpa-install-plugins', 
        'parent_slug'  => 'plugins.php',           
        'capability'   => 'edit_plugins_options',  
        'has_notices'  => true,                    
        'dismissable'  => false,                    
        'dismiss_msg'  => '',                      
        'is_automatic' => false,                   
        'message'      => '',                      
    );

    tgmpa( $plugins, $config );
}
