<?php
/**
 * Created by PhpStorm.
 * User: roysivan
 * Date: 10/13/17
 * Time: 11:37 PM
 */

namespace EZDataSite\OutputTransformer;
use EZDataSite\OutputTransformer\TransformerInterface;

abstract class TransformerObject implements TransformerInterface {

    public function init_transform( $data, $options ) {
        return $data;
    }

}