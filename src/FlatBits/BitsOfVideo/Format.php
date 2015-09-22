<?php

namespace FlatBits\BitsOfVideo;


abstract class Format
{
    // Format Qualities
    const QUALITY_HIGH = 'hd720';
    const QUALITY_MEDIUM = 'medium';
    const QUALITY_LOW = 'small';

    private $quality;

    function __construct($quality = null){
        if($quality === null){
            $quality = $this->getLowestQuality();
        }

        $this->quality = $quality;
    }

    /**
     * @return array
     */
    abstract function getQualities();

    /**
     * @return string
     */
    abstract function getTypeString();

    /**
     * Meant to be overridden by child classes who have special process after download (like audio format)
     * @param string $filePath
     */
    public function postDownload($filePath){}

    /**
     * @return string
     */
    function getQuality(){
        return $this->quality;
    }

    /**
     * Ensure the qualities are ordered highest to lowest
     * @return array
     */
    private function _getQualities(){
        $qualities = $this->getQualities();
        if(!is_array($qualities)){
            $qualities = array();
        }

        $allowedQualities = [self::QUALITY_HIGH, self::QUALITY_MEDIUM, self::QUALITY_LOW];
        return array_intersect($allowedQualities, $qualities);
    }

    /**
     * Returns the lowest quality for the requested format, or null if format is not registered here.
     * @return string|null
     */
    function getLowestQuality(){
        $quality = null;

        $qualities = $this->_getQualities();
        $count = count($qualities);
        if($count > 0){
            $quality = $qualities[--$count];
        }

        return $quality;
    }

    /**
     * Returns the highest quality for the requested format, or null if format is not registered here.
     * @return string|null
     */
    function getHighestQuality(){
        $quality = null;

        $qualities = $this->_getQualities();
        if(count($qualities) > 0){
            $quality = $qualities[0];
        }

        return $quality;
    }
}