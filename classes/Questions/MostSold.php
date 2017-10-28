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
            'title' => __( 'How much money by product have I sold?', 'ezdata_site' ),
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
                    'desc'  => 'Product ID - Should be a number',
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
        global $wpdb;
        $user = wp_get_current_user();
        $data = $request->get_params();
        $data['user_id'] = $user->ID;
        $output = $data['output'];
        $output_object = $this->get_output_object( $output );

        // Run Query
        $query_results = $this->run_query( $data, $user->ID );
        $data['query_results'] = $query_results;

        $data['output_results'] = $output_object->init_transform( $query_results, $data['output_options'] );

        return $data;
    }

    public function permission_callback(\WP_REST_Request $request) {
        return true;
    }

    public function run_query( $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ez_items';
        $group = $data['group'];
        $purchase_total = $data['question_data'][0];
        $product_col = $data['question_data'][1];

        $query = "SELECT $product_col as `product`, SUM( $purchase_total ) as `count` FROM $table WHERE `groups` = $group";

        if ( is_array( $data['output_options'] ) ) {
            $date_range = $options[0];
            if ( 'range_month_03' === $date_range ) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
            }
            if ( 'range_month_06' === $date_range ) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            }
            if ( 'range_month_12' === $date_range ) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
            }
        }

        $query .= " GROUP BY $product_col";

        return $wpdb->get_results( $query );
    }

}
