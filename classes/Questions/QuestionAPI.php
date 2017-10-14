<?php

namespace EZDataSite\Questions;


class QuestionAPI {

    private static $questions;

    private $namespace;

    public function __construct() {
        $this->namespace = 'ez/v1';
    }

    public static function init( $questions ) {
        $self = new self();
        foreach ( $questions as $question ) {
            $questionClass = $question['className'];
            self::$questions[$question['id']] = new $questionClass();
        }
        add_action( 'rest_api_init', [ $self, 'ezdata_question_api_routes' ] );
    }

    public function ezdata_question_api_routes() {

        register_rest_route( $this->namespace, '/questionsCollection/', array(
            'methods' => 'GET',
            'callback' => [ $this, 'get_all_questions' ],
        ) );

        register_rest_route( $this->namespace, '/questionsCollection/(?P<id>[A-z]+)', array(
            'methods' => 'GET',
            'callback' => [ $this, 'get_question' ],
            'permission_callback' => [ $this, 'permission_callback' ]
        ) );
    }

    public function permission_callback( \WP_REST_Request $request ) {
        $data = $request->get_params();
        if( ! isset( $data['id'] ) ) {
            return false;
        }
        return self::$questions[$data['id']]->permission_callback( $request );
    }

    public function get_all_questions( \WP_REST_Request $request ) {

        $data = [];
        $questions = self::$questions;

        foreach ( $questions as $question ) {
            $data['questions'][] = $question->config;
        }

        return new \WP_REST_Response( $data );
    }

    public function get_data( $question, \WP_REST_Request $request ) {
        return self::$questions[$question]->get_data_init( $request );
    }

    public function get_question( \WP_REST_Request $request ) {

        $data = $request->get_params();
        if ( isset( $data['id'] ) ) {
            $data = $this->get_data( $data['id'], $request );
        }

        return new \WP_REST_Response( $data );
    }

    public function get_question_config( $question_id ) {
        return self::$questions[$question_id]->config;
    }

}