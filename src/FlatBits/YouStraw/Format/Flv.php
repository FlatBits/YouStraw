<?php

namespace FlatBits\YouStraw\Format;

use FlatBits\YouStraw\Format;

class Flv extends Format{
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
        return 'video/x-flv';
    }
}