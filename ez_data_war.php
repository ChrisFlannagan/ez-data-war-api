<?php
/*
Plugin Name: EZ Data API
Description:  EZ Data Extension of WAR API
Version: 1.0
Author: ezdata
License: GPL
*/


class ez_data_war{

	public function ez_data_config() {
		return [
			'api_name' => 'ez',
			'version' => 1,
			'limit' => 100,
			'war_jwt_expire' => false,
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
		];
	}

	public function return_request( $request ) {
		return $request;
	}

	public function add_data_models() {
		return [
			[
				'name' => 'items',
				'access' => true,
				'params' => [
					'groups' => [
						'type' => 'string',
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

				],
				'assoc' => [
					'groups' => [
						'assoc' => 'one',
						'bind'  => 'name'
					]
				]
			],
			[
				'name' => 'groups',
				'access' => true,
				'params' => [
					'name'        => [
						'type' => 'string',
						'required' => true
					],
					'description' => 'string',
				],
				'assoc' => [
					'items' => [
						'assoc' => 'many',
						'bind' => 'name'
					]
				]
			]
		];
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