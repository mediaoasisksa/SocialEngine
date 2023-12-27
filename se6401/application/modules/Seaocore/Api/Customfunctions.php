<?php

class Seaocore_Api_Customfunctions extends Core_Api_Abstract {


    public function convertFileArrayToNormal( $file ) {

        if ( !is_array($file) ) {
            return array();
        }
        $newFileArray = array();
        foreach ($file as $index => $value) {
            $newFileArray[$index] = is_array( $value ) ? array_shift($value) : $value;
        }
        return $newFileArray;

    }

}