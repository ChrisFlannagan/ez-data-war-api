<?php
/*
Plugin Name: EZ Data API
Description:  EZ Data Extension of WAR API
Version: 1.0
Author: ezdata
License: GPL
*/



add_filter( 'rest_pre_serve_request', function( $value ) {
	header( 'Access-Control-Allow-Origin: *' );
	header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
	header( 'Access-Control-Allow-Credentials: true' );
	header( 'Access-Control-Allow-Headers: X-WP-Nonce, Content-Type, Authorization');

	return $value;
}, 15);

class ez_data_war {

	public function ez_data_config() {
		return [
			'api_name' => 'ez',
			'version' => 1,
			'limit' => 100,
			'isolate_user_data' => true,
		];
	}

	public function add_custom_endpoint() {
		return [
			'return_request' => [
				'uri' => '/return-request',
				'access' => true,
				'callback' => [ $this, 'return_request' ]
			],
			'get_war_object' => [
				'uri' => '/war-object',
				'access' => null,
				'callback' => [ $this, 'get_war_object']
			],
		];
	}

	public function return_request( $request ) {
		return $request;
	}

	public function get_war_object( $request ) {
		$war_object = apply_filters( 'war_object', [] );
		return $war_object;
	}

	/**
	 * Data Models
	 *
	 * Items - Collection of data
	 * Groups - Collection of items
	 * Graph - Visual Representation of a group. x/y axis to be defined as item parameter
	 *
	 * @return array
	 */
	public function add_data_models() {
		return [
			'items' => [
				'name' => 'items',
				'access' => true,
				'params' => [
					'groups' => [
						'type' => 'integer',
						'required' => true,
					],
					'value' => [
						'type' => 'integer',
						'required' => false,
						'default' => null
					],
					'misc_one' => [
						'type' => 'integer',
						'required' => false,
						'default' => null
					],
					'misc_two' => [
						'type' => 'integer',
						'required' => false,
						'default' => null
					],
					'misc_three' => [
						'type' => 'string',
						'required' => false,
						'default' => null
					],
					'misc_four' => [
						'type' => 'string',
						'required' => false,
						'default' => null
					],
                    'misc_five' => [
                        'type' => 'array',
                        'required' => false,
                        'default' => null,
                    ],

				],
				'assoc' => [
					'groups' => [
						'assoc' => 'one',
						'bind'  => 'groups',
                        'match' => 'id'
					]
				]
			],
			'groups' => [
				'name' => 'groups',
				'access' => true,
				'params' => [
					'name'        => [
						'type' => 'string',
						'required' => true
					],
					'description' => 'string',
					'misc_one_label' => 'string',
					'misc_two_label' => 'string',
					'misc_three_label' => 'string',
					'misc_four_label' => 'string',
				],
				'assoc' => [
					'items' => [
						'assoc' => 'many',
						'bind' => 'id',
                        'match' => 'groups',
					]
				],
                'callback' => [
                    'delete_item' => [ $this, 'group_delete_callback' ],
                ],
			],
			'graph' => [
				'name' => 'graph',
				'access' => true,
				'params' => [
					'group'  => [ 'type' => ['integer', 'string'], 'required' => true ],
					'x_axis' => [ 'type' => 'string', 'required' => true ],
					'y_axis' => [ 'type' => 'string', 'required' => true ],
					'type'   => [ 'type' => 'string', 'required' => true ]
				],
				'assoc' => [
					'groups' => [
						'assoc' => 'one',
						'bind'  => 'group',
                        'match' => 'id'
					]
				],
				'pre_return' => [ $this, 'get_graph' ]
			],
            'table' => [
                'name' => 'table',
                'access' => true,
                'params' => [
                    'group'  => [ 'type' => ['integer', 'string'], 'required' => true ],
                    'x_axis' => [ 'type' => 'string', 'required' => true ],
                    'y_axis' => [ 'type' => 'string', 'required' => true ],
                    'columns_one_label' => [ 'type' => 'string' ],
                    'columns_two_label' => [ 'type' => 'string' ],
                ],
                'assoc' => [
                    'groups' => [
                        'assoc' => 'one',
                        'bind'  => 'group',
                        'match' => 'id'
                    ]
                ],
                'pre_return' => [ $this, 'get_table' ]
            ]
		];
	}

	public function get_graph( $request ) {
		global $wpdb;
		$table = $wpdb->prefix . 'ez_items';
		$group = $request['group'];
		$group = $group['id'];
		$user = $request['user'];
		$x_axis = $request['x_axis'];
		$y_axis = $request['y_axis'];

		if( 'created_on_month' === $y_axis ) {
			$y_axis = 'created_on';
			$request['results'] = $wpdb->get_results( "SELECT MONTH( $y_axis ) as 'label', YEAR( $y_axis ) as 'year', SUM($x_axis) as 'sum' FROM $table WHERE user = $user AND groups = $group GROUP BY MONTH( $y_axis ), YEAR( $y_axis )" );
		} elseif ( 'created_on_day' === $y_axis ) {
			$y_axis = 'created_on';
			$request['results'] = $wpdb->get_results( "SELECT DAY( $y_axis ) as 'label', SUM($x_axis) as 'sum' FROM $table WHERE user = $user AND groups = $group GROUP BY DAY( $y_axis )" );
		} else {
			$request['results'] = $wpdb->get_results( "SELECT $y_axis as 'label', SUM($x_axis) as 'sum' FROM $table WHERE user = $user AND groups = $group GROUP BY $y_axis" );
		}

		if( $request['group'] ) {
			$table = $wpdb->prefix . 'ez_groups';
			$group_id = $request['group'];
			$group_id = $request['group']['id'];
			$request['group_data'] = $wpdb->get_results( "SELECT * FROM $table WHERE `id` = $group_id" );
			if( ! empty( $request['group_data'] ) ) {
				$request['group_data'] = $request['group_data'][0];
			}
		}

		return $request;
	}


    public function get_table( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ez_items';
        $group = $request['group'];
        $user = $request['user'];
        $x_axis = $request['x_axis'];
        $y_axis = $request['y_axis'];

        if( 'created_on_month' === $y_axis ) {
            $y_axis = 'created_on';
            $request['results'] = $wpdb->get_results( "SELECT MONTH( $y_axis ) as 'label', YEAR( $y_axis ) as 'year', SUM($x_axis) as 'sum' FROM $table WHERE user = $user AND groups = $group GROUP BY MONTH( $y_axis ), YEAR( $y_axis )" );
        } elseif ( 'created_on_day' === $y_axis ) {
            $y_axis = 'created_on';
            $request['results'] = $wpdb->get_results( "SELECT DAY( $y_axis ) as 'label', SUM($x_axis) as 'sum' FROM $table WHERE user = $user AND groups = $group GROUP BY DAY( $y_axis )" );
        } else {
            $request['results'] = $wpdb->get_results( "SELECT $y_axis as 'label', SUM($x_axis) as 'sum' FROM $table WHERE user = $user AND groups = $group GROUP BY $y_axis" );
        }

        if( ! empty( $request['results'] ) ) {
            foreach( $request['results'] as $result ) {
                $result->sum = (float) $result->sum;
                $result->sum = round( $result->sum, 3 );
            }
        }

        if( $request['group'] ) {
            $table = $wpdb->prefix . 'ez_groups';
            $group_id = $request['group'];
            $request['group_data'] = $wpdb->get_results( "SELECT * FROM $table WHERE `id` = $group_id" );
            if( ! empty( $request['group_data'] ) ) {
                $request['group_data'] = $request['group_data'][0];
            }
        }

        return $request;
    }

	public function group_delete_callback( $request ) {
	    global $wpdb;

        $table = $wpdb->prefix . 'ez_items';
        $deleted_items = $wpdb->delete( $table, [ 'groups' => absint( trim( $request->params->id ) ) ], array( '%s') );
        $request->deleted_items = $deleted_items;

        $table = $wpdb->prefix . 'ez_graph';
        $deleted_graphs = $wpdb->delete( $table, [ 'group' => absint( trim( $request->params->id ) ) ], array( '%s') );
        $request->deleted_graphs = $deleted_graphs;

        $table = $wpdb->prefix . 'ez_groups';
        $deleted_group = $wpdb->delete( $table, [ 'id' => absint( trim( $request->params->id ) ) ], array( '%s') );
        $request->deleted_group = $deleted_group;

        return $request;
    }

}

function ez_data_war_init() {

	if ( class_exists( 'war_api' ) ) :
		$war_api = new War_Api();
		$ez_api = new ez_data_war();

		// Add Config
		$war_api->add_config( $ez_api->ez_data_config() );
		$war_api->add_models( $ez_api->add_data_models() );
		$war_api->add_endpoints( $ez_api->add_custom_endpoint() );
		$war_api->init();

	endif;
}

add_action( 'plugins_loaded', 'ez_data_war_init' );


add_filter( 'war_object', function( $object ) {
    global $wpdb;
	$object['user'] = false;
	$object['user'] = wp_get_current_user();
	if( isset( $object['user']->data->user_pass ) ) {
		$object['user']->data->user_login = false;
		$object['user']->data->user_pass = false;
	}
	$object['subscription'] = rcp_get_subscription_id( $object['user']->ID );

	$table = $wpdb->prefix . 'ez_groups';
    $user_id = $object['user']->ID;
    $groups = $wpdb->get_results( "SELECT * FROM $table WHERE `user` = $user_id" );
    $object['current_total_groups'] = count( $groups );

	return $object;
}, 99, 1);