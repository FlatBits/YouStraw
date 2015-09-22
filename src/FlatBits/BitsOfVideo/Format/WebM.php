<?php

namespace FlatBits\BitsOfVideo\Format;

use FlatBits\BitsOfVideo\Format;

class WebM extends Format{
    /**
     * @return array
     */
    function getQualities(){
        return array(self::QUALITY_MEDIUM);
    }

    /**
     * @return string
     */
    function getTypeString(){
        return 'video/webm';
    }
}