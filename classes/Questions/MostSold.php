<?php
/**
 * Most Sold: Which Product has sold the most dollar value
 *
 */

namespace EZDataSite\Questions;

use EZDataSite\Questions\QuestionObject as Question;

class MostSold extends Question {


    public function __construct() {
        $config = array(
            'id'    => 'MostSold',
            'title' => __( 'Which Product Has Sold the Most (Dollar Value)', 'ezdata_site' ),
            'type'  => 'ecomm',
            'data'  => array(
                array(
                    'title' => 'Choose Field That Represents Purchase Total',
                    'desc'  => 'Purchase Total - Should be a number',
                    'type'  => array(
                        'value',
                        'misc_one',
                        'misc_two'
                    )
                ),
                array(
                    // Cannot Be array - this is single product
                    'title' => 'Choose Field that Represents A Single Product Sold',
                    'desc'  => 'Product Sold - Should be a number',
                    'type'  => array(
                        'value',
                        'misc_one',
                        'misc_two',
                    )
                )
            ),
        );
        parent::__construct( $config );
    }

    public function get_data( \WP_REST_Request $request ){

        $data = $this->config;
        unset( $data['data'] );

        $data['testing'] = 'hi roy';

        return $data;
    }

    public function permission_callback(\WP_REST_Request $request) {
        return true;
    }

}
