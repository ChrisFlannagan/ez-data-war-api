<?php

namespace EZDataSite\Questions;


interface QuestionInterface {

    public function permission_callback( \WP_REST_Request $request );

    public function group_user_check( $user_id, $group );

    public function get_data_init( \WP_REST_Request $request );

    public function get_data( \WP_REST_Request $request );

    public function run_query( $data, $user_id );

    public function run_array_query( $data, $user_id );

    public function get_output_object( $output );

}