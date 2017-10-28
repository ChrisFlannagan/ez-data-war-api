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
            'title' => __( 'How many of each product have I sold?', 'ezdata_site' ),
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
        $data['user_id'] = $user->ID;
        $output = $data['output'];
        $output_object = $this->get_output_object( $output );

        if( 'misc_five' !== $question_data[0] ) {
            // Run Query
            $query_results = $this->run_query( $data, $user->ID );
            $data['query_results'] = $query_results;
        } else {
            $query_results = $this->run_array_query( $data, $user->ID );
            $data['query_results'] = $query_results;
        }

        $data['output_results'] = $output_object->init_transform( $query_results, $data['output_options'] );

        return $data;
    }

    public function run_query( $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ez_items';
        $group = $data['group'];
        $product_col = $data['question_data'][0];

        $query = "SELECT $product_col as `product`, COUNT( $product_col ) as `count` FROM $table WHERE `groups` = $group AND $product_col IS NOT NULL";

        if (is_array( $data['output_options'] )) {
            $date_range = $data['output_options'][0];
            if ('range_month_03' === $date_range) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
            }
            if ('range_month_06' === $date_range) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            }
            if ('range_month_12' === $date_range) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
            }
        }

        $query .= " GROUP BY $product_col";
        return $wpdb->get_results( $query );

    }

    public function run_array_query( $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ez_items';
        $group = $data['group'];
        $product_col = $data['question_data'][0];


        $query = "SELECT $product_col as `products` FROM $table WHERE `groups` = $group AND $product_col IS NOT NULL";

        if (is_array( $data['output_options'] )) {
            $date_range = $data['output_options'][0];
            if ('range_month_03' === $date_range) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
            }
            if ('range_month_06' === $date_range) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            }
            if ('range_month_12' === $date_range) {
                $query .= " AND created_on >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
            }
        }

        $results = $wpdb->get_results( $query );

        $tally = array();
        foreach ( $results as $result ) {
            $products = explode( ',', $result->products );
            if ( ! empty( $products ) ) {
                foreach ( $products as $product ) {
                    if ( $tally[$product] ) {
                        $tally[$product]->count++;
                    } else {
                        $tally[$product] = (object) array(
                            'product' => $product,
                            'count'   => 1
                        );
                    }
                }
            }
        }

        return $tally;
    }
}
