<?php

namespace WP_SUAPI;

if (!defined('ABSPATH')) {
    exit;
}

use CPT;
use CMB_Meta_Box;
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
        $this->add_meta_boxes(array());
        add_action('p2p_init', array($this, 'register_post_connections'));
    }

    /**
     * Adds post types
     * @link   https://github.com/gizburdt/cuztom/wiki/Post-Types
     * */
    public function add_post_types()
    {
        $this->post_types['team'] = new CPT(
            array(
                'post_type_name' => 'team',
                'singular' => 'Team',
                'plural' => 'Teams',
                'slug' => 'team'
            ), array(
                'supports' => array('title', 'editor', 'thumbnail')
            )
        );
        $this->post_types['player'] = new CPT(
            array(
                'post_type_name' => 'player',
                'singular' => 'Player',
                'plural' => 'Players',
                'slug' => 'player'
            ), array(
                'supports' => array('title', 'editor', 'thumbnail')
            )
        );

        $this->post_types['team']->columns(array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Name'),
            'date' => __('Date'),
        ));
    }

    public function add_meta_boxes()
    {
        $metabox_su = new CMB_Meta_Box(array(
                'id' => 'su-meta',
                'title' => 'swiss unihockey Meta Data',
                'pages' => 'team',
                'fields' => array(
                    array(
                        'id' => 'showsuhvdetails',
                        'name' => __('swiss unihockey ranking and fixtures', 'wp-suapi'),
                        'desc' => __('Show swiss unihockey ranking and fixtures', 'wp-suapi'),
                        'type' => 'radio',
                        'options' => array(
                            __('Yes', 'wp-suapi'),
                            __('No', 'wp-suapi'),
                        ),
                    ),
                    array(
                        'id' => 'suhvteamid',
                        'name' => __('swiss unihockey Team ID', 'wp-suapi'),
                        'desc' => __('Needed for API at swissunihockey.ch', 'wp-suapi'),
                        'type' => 'select',
                        'options' => $this->get_teams_for_select(),
                        'allow_none' => true,
                        'multiple' => false,
                        'select2_options' => array()
                    ),
                ),
            )
        );

        $metabox_team = new CMB_Meta_Box(array(
            'id' => 'team',
            'title' => 'Team Meta Data',
            'pages' => 'team',
            'fields' => array(
                array(
                    'id' => 'showplayerdetails',
                    'name' => __('Link to player profiles', 'wp-suapi'),
                    'desc' => __('Show detailed player profiles', 'wp-suapi'),
                    'type' => 'radio',
                    'options' => array(
                        __('Yes', 'wp-suapi'),
                        __('No', 'wp-suapi'),
                    ),
                ),
                array(
                    'id' => 'newscategory',
                    'name' => __('Select Category', 'wp-suapi'),
                    'desc' => __('Select which news category should be assigned to this team', 'wp-suapi'),
                    'type' => 'taxonomy_select',
                    'taxonomy' => 'category',
                    'hide_empty' => false,
                    'multiple' => false,
                ),
                array(
                    'id' => 'emailcontact',
                    'name' => __('E-Mail contact', 'wp-suapi'),
                    'desc' => __('E-Mail address to send contact e-mails to', 'wp-suapi'),
                    'type' => 'email'
                ),
                array(
                    'id' => 'agegroup',
                    'name' => __('Age group', 'wp-suapi'),
                    'desc' => __('Age group which belongs to this team', 'wp-suapi'),
                    'type' => 'text_small'
                ),
            ),
        ));

        $nationalities = new WP_SUAPI_Nationalities();

        $metabox_player = new CMB_Meta_Box(array(
            'id' => 'player',
            'title' => 'Player Meta Data',
            'pages' => 'player',
            'fields' => array(
                array(
                    'id' => 'showplayerdetails',
                    'name' => __('Gender', 'wp-suapi'),
                    'desc' => __('Player gender', 'wp-suapi'),
                    'type' => 'radio',
                    'options' => array(
                        __('Male', 'wp-suapi'),
                        __('Female', 'wp-suapi'),
                    ),
                ),
                array(
                    'id' => 'nationality',
                    'name' => __('Nationality', 'wp-suapi'),
                    'desc' => __('Select nationality', 'wp-suapi'),
                    'type' => 'select',
                    'options' => $nationalities->get_nationality_options(),
                    'value' => "CH",
                    'allow_none' => false,
                ),
                array(
                    'id' => 'birthdate',
                    'name' => __('Birthdate', 'wp-suapi'),
                    'desc' => __('Date of Birth (only year will be shown)', 'wp-suapi'),
                    'type' => 'text_date  '
                ),
                array(
                    'id' => 'yearjoined',
                    'name' => __('Year joined', 'wp-suapi'),
                    'desc' => __('Date of joining the club (only year will be shown)', 'wp-suapi'),
                    'type' => 'text_date  '
                ),
                array(
                    'id' => 'personalsponsorurl',
                    'name' => __('Personal sponsor url', 'wp-suapi'),
                    'desc' => __('Personal sponsor website for this player'),
                    'type' => 'url'
                ),
                array(
                    'id' => 'personalsponsorname',
                    'name' => __('Personal sponsor name', 'wp-suapi'),
                    'desc' => __('Personal sponsor name for this player'),
                    'type' => 'text_small'
                ),
                array(
                    'id' => 'personalsponsorlogo',
                    'name' => __('Personal sponsor logo', 'wp-suapi'),
                    'desc' => __('Personal sponsor logo for this player'),
                    'type' => 'image'
                ),
            ),
        ));
    }

    public function register_post_connections()
    {
        $args = array(
            'name' => 'players_to_team',
            'from' => 'player',
            'to' => 'team',
            'sortable' => 'any',
            'reciprocal' => false,
            'admin_box' => array(
                'show' => 'any',
                'context' => 'normal',
                'can_create_post' => true
            ),
            'admin_column' => 'any',
            'cardinality' => 'many-to-many',
            'duplicate_connections' => true,
            'fields' => array(
                'playernumber' => array(
                    'title' => 'Player Number',
                    'type' => 'integer',
                ),
                'position' => array(
                    'title' => 'Position',
                    'type' => 'select',
                    'values' => array(
                        'headcoach' => __('headcoach', 'wp-suapi'),
                        'trainer' => __('trainer', 'wp-suapi'),
                        'physio' => __('physio', 'wp-suapi'),
                        'goalkeeper' => __('goalkeeper', 'wp-suapi'),
                        'midfieldplayer' => __('midfieldplayer', 'wp-suapi'),
                        'defense' => __('defense', 'wp-suapi'),
                        'center' => __('center', 'wp-suapi'),
                        'offense' => __('offense', 'wp-suapi')
                    ),
                    'default' => 'defense'
                ),
                'captain' => array(
                    'title' => 'Captain',
                    'type' => 'checkbox'
                )
            )
        );
        p2p_register_connection_type($args);
    }

    private function get_teams_for_select()
    {
        $apiHandler = WP_SUAPI_API_Handler::GET_INITIALIZED_API_HANDLER();
        if ($apiHandler->isConnected()) {
            return array_reduce(
                $apiHandler->getTeamsForClub(new Club(get_option("wp-suapi_api-club"), "EMPTY_CLUB_CALL")),
                function ($result, $item) {
                    $result[$item->getTeamId()] = $item->getTeamName();
                    return $result;
                },
                array()
            );
        } else {
            return array();
        }
    }
}
