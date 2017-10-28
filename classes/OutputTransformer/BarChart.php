<?php

namespace EZDataSite\OutputTransformer;
use EZDataSite\OutputTransformer\TransformerObject;

class BarChart extends TransformerObject {

    public function init_transform( $data, $options ) {
        $return_data = [];
        foreach ( $data as $key => $value ) {
            foreach ( $value as $value_value ) {
                if( ! $value_value ) {
                    unset( $data[$key] );
                }
            }
        }
        $return_data['labels'] = $this->setup_labels( $data, $options );
        $return_data['datasets'] = $this->setup_datasets( $data, $options );

        return $return_data;
    }

    private function setup_labels( $data, $options ) {
        return array( 'Products' );
    }

    private function setup_datasets( $data, $options ) {
        $i = 0;
        foreach ( $data as $key => $value ) {
            if ( 0 === $i ) {
                $backgroundColor = '#14B694';
            } else {
                $backgroundColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            }
            $product_array[] = array(
                'label' => 'Product ' . $value->product,
                'data'  => [ (int) $value->count ],
                'backgroundColor' => $backgroundColor,
                'borderColor' => '#000',
            );
            $i++;
        }
        return $product_array;
    }

}