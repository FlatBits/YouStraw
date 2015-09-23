<?php

namespace FlatBits\YouStraw\Format;

use FlatBits\YouStraw\Format;

class Mp4 extends Format{
    /**
     * @return array
     */
    function getQualities(){
        return array(self::QUALITY_HIGH, self::QUALITY_MEDIUM);
    }

    /**
     * @return string
     */
    function getTypeString(){
        return 'video/mp4';
    }
}