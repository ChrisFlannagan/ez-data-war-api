<?php
/**
 * Most Sold: Which Product has sold the most dollar value
 *
 */

namespace EZDataSite\Questions;

use EZDataSite\Questions\QuestionObject as Question;

class MostSoldProduct extends Question {


    public function __construct() {
        $config = array(
            'id'    => 'MostSoldProduct',
            'title' => __( 'Which Product Has Sold the Most', 'ezdata_site' ),
            'type'  => 'support',
            'data'  => array(
                array(
                    'title' => 'Choose Field that Represents Product(s)',
                    'desc'  => 'Product(s) Sold - Should be a number or an array of numbers',
                    'type'  => array(
                        'value',
                        'misc_one',
                        'misc_two',
                        'misc_five',
                    )
                )
            ),
        );
        parent::__construct( $config );
    }

    public function get_data( \WP_REST_Request $request ){
        global $wpdb;
        $user = wp_get_current_user();
        $data = $request->get_params();

        return $data;
    }

}
