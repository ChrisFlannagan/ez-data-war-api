<?php

namespace EZDataSite\Questions;


interface QuestionInterface {

    public function get_data( \WP_REST_Request $request );

}