<?php
/**
 * Created by PhpStorm.
 * User: roysivan
 * Date: 10/11/17
 * Time: 6:33 AM
 */

namespace EZDataSite\Questions;

use EZDataSite\Questions\QuestionInterface;


class QuestionObject implements QuestionInterface {

    public $config;

    public function __construct( $config ) {
        $this->config = $config;
        $this->config['_links'] = array(
            'self' => get_site_url() . '/wp-json/ez/v1/questionsCollection/' . $this->config['id']
        );
    }

    public function permission_callback( \WP_REST_Request $request ) {
        $user = wp_get_current_user();
        $data = $request->get_params();
        if ( ! $user->ID || ! isset( $data['group'] ) ) {
            return false;
        }
        return $this->group_user_check( $user->ID, $data['group'] );
    }

    public function get_data_init( \WP_REST_Request $request ) {
        $data = $request->get_params();

        if ( isset( $data['question_data'] ) ) {
            $request->set_param( 'question_data', explode( ',', $data['question_data'] ) );
        }

        return $this->get_data( $request );
    }

    public function get_data( \WP_REST_Request $request ) {
        $data = $request->get_params();
        return new \WP_REST_Response( $data );
    }

    public function group_user_check( $user_id, $group ) {
        global $wpdb;

        $table = $wpdb->prefix . 'ez_groups';
        $group_id = (int) $group;
        $group_data = $wpdb->get_results( "SELECT * FROM $table WHERE `id` = $group_id" );

        if ( empty( $group_data ) ) {
            return false;
        }

        if ( $user_id !== (int) $group_data[0]->user ) {
            return false;
        }

        return true;
    }

}