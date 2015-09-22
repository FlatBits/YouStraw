<?php

namespace FlatBits\BitsOfVideo\Format;


use FlatBits\BitsOfVideo\Format;

// 3GP, but php doesn't allow class name starting with number
class ThreeGP extends Format{
    /**
     * @return array
     */
    function getQualities(){
        return array(self::QUALITY_LOW);
    }

    /**
     * @return string
     */
    function getTypeString(){
        return 'video/3gpp';
    }
}