<?php
/**
 * Created by PhpStorm.
 * User: roysivan
 * Date: 10/11/17
 * Time: 6:33 AM
 */

namespace EZDataSite\Questions;

use EZDataSite\Questions\QuestionInterface;

// Output Transformers
use EZDataSite\OutputTransformer\BarChart as BarChart;

class QuestionObject implements QuestionInterface {

    public $config;

    public function __construct( $config ) {
        $this->config = $config;
        $this->config['_links'] = array(
            'self' => get_site_url() . '/wp-json/ez/v1/questionsCollection/' . $this->config['id']
        );
    }

    /**
     * Permission Callback
     * @param \WP_REST_Request $request
     * @return bool
     */
    public function permission_callback( \WP_REST_Request $request ) {
        $user = wp_get_current_user();
        $data = $request->get_params();
        if ( ! $user->ID || ! isset( $data['group'] ) ) {
            return false;
        }
        return $this->group_user_check( $user->ID, $data['group'] );
    }

    /**
     * Group User Check
     * Verify group belongs to current user
     * @param $user_id
     * @param $group
     * @return bool
     */
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

    /**
     * Init Data
     * Pre get_data transform
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_data_init( \WP_REST_Request $request ) {
        $data = $request->get_params();

        if ( isset( $data['question_data'] ) ) {
            $request->set_param( 'question_data', explode( ',', $data['question_data'] ) );
        }

        if ( isset( $data['output_options'] ) && 'null' !== $data['output_options'] ) {
            $request->set_param( 'output_options', explode( ',', $data['output_options'] ) );
        } elseif ( 'null' === $data['output_options'] ) {
            $request->set_param( 'output_options', false );
        }

        return $this->get_data( $request );
    }

    /**
     * Get and return data
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_data( \WP_REST_Request $request ) {
        $data = $request->get_params();
        return new \WP_REST_Response( $data );
    }

    /**
     * Run and return $wpdb->get_results();
     *
     * @param $group_id
     * @param $question_data
     * @param $user_id
     */
    public function run_query( $data, $user_id ) {}

    /**
     * Run and return $wpdb->get_results();
     *
     * @param $group_id
     * @param $question_data
     * @param $user_id
     */
    public function run_array_query( $data, $user_id ) {}

    /**
     * Return Output Data Transformer Object
     *
     * @param $output
     * @return bool|BarChart
     */
    public function get_output_object( $output ) {
        switch( $output ) {
            case 'bar':
                return new BarChart();
                break;
        }

        return false;
    }

}