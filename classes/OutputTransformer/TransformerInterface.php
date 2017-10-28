<?php
/**
 * Created by PhpStorm.
 * User: roysivan
 * Date: 10/13/17
 * Time: 11:36 PM
 */

namespace EZDataSite\OutputTransformer;


interface TransformerInterface {

    public function init_transform( $data, $options );

}