<?php
namespace WP_SUAPI;

if (!defined('ABSPATH')) {
    exit;
}

use Cuztom_Post_Type;
use WP_SUAPI\Object\Club;

class WP_SUAPI_Post_Types
{
    /**
     * Holds all post type objects
     *
     * @var  array
     */
    private $post_types = [];

    public function __construct()
    {
        $this->add_post_types();
    }

    /**
     * Adds post types
     * @link   https://github.com/gizburdt/cuztom/wiki/Post-Types
     * */
    public function add_post_types()
    {
        $this->post_types['team'] = new Cuztom_Post_Type('team', array(
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail')
        ));
        register_cuztom_post_type('Team');
        $this->add_meta_boxes();
    }

    /**
     * Adds meta boxes
     * @link https://github.com/gizburdt/cuztom/wiki/Meta-Boxes
     */
    public function add_meta_boxes()
    {
        $apiHandler = WP_SUAPI_API_Handler::GET_INITIALIZED_API_HANDLER();
        print_r(get_option("api-club"));
        $this->post_types['team']->add_meta_box(
            'su_meta',
            'swiss unihockey Meta Data',
            array(
                array(
                    'name' => 'showsuhvdetails',
                    'label' => __('swiss unihockey ranking and fixtures', 'wp-suapi'),
                    'description' => __('Show swiss unihockey ranking and fixtures', 'wp-suapi'),
                    'type' => 'yesno'
                ),
                array(
                    'name' => 'suhvteamid',
                    'label' => __('swiss unihockey Team ID', 'wp-suapi'),
                    'description' => __('Needed for API at swissunihockey.ch', 'wp-suapi'),
                    'type' => 'select',
                    'options' => array_reduce(
                        $apiHandler->getTeamsForClub(new Club(get_option("wp-suapi_api-club"), "EMPTY_CLUB_CALL")),
                        function ($result, $item) {
                            $result[$item->getTeamId()] = $item->getTeamName();
                            return $result;
                        },
                        array()
                    ),
                    'show_admin_column' => true,
                    'admin_column_sortable' => true,
                    'admin_column_filter' => true,
                )
            )
        );

        $this->post_types['team']->add_meta_box(
            'team_meta',
            'Team Meta',
            array(
                array(
                    'name' => 'showplayerdetails',
                    'label' => __('Link to player profiles', 'wp-suapi'),
                    'description' => __('Show detailed player profiles', 'wp-suapi'),
                    'type' => 'yesno'
                ),
                array(
                    'name' => 'newscategory',
                    'label' => __('Select Category', 'wp-suapi'),
                    'description' => __('Select which news category should be assigned to this team', 'wp-suapi'),
                    'type' => 'term_select',
                    'args' => array(
                        'taxonomy' => 'category',
                    )
                ),
                array(
                    'name' => 'emailkontakt',
                    'label' => 'E-Mail Kontakt',
                    'description' => 'E-Mail Kontakt',
                    'type' => 'text',
                    'show_admin_column' => false,
                    'admin_column_sortable' => false,
                    'admin_column_filter' => false,
                ),
                array(
                    'name' => 'agegroup',
                    'label' => 'Jahrgänge',
                    'description' => 'Jahrgänge für das Team',
                    'type' => 'text',
                    'show_admin_column' => false,
                    'admin_column_sortable' => false,
                    'admin_column_filter' => false,
                )
            )
        );
    }
}
