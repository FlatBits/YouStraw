<?php

namespace FlatBits\BitsOfVideo;

/**
 * Utility class to easily get the most common format and quality. Do not consider it as exhaustive!
 * @package FlatBits\BitsOfVideo
 */
class Format{
    // Format Types
    const TYPE_MP4 = 'video/mp4';
    const TYPE_WEBM = 'video/webm';
    const TYPE_FLV = 'video/x-flv';
    // 3GP, but php doesn't allow variable starting with number
    const TYPE_THREEGP = 'video/3gpp';

    // Format Qualities
    const QUALITY_HD720 = 'hd720';
    const QUALITY_MEDIUM = 'medium';
    const QUALITY_SMALL = 'small';

    /**
     * These are all the possible relations between qualities and types I found
     * If you have more to add, please open an issue and provide the video id
     * on which you found it so I can test an add it here.
     * @var array
     */
    static $relations = [
        self::TYPE_MP4       => [self::QUALITY_HD720, self::QUALITY_MEDIUM],
        self::TYPE_WEBM      => [self::QUALITY_MEDIUM],
        self::TYPE_FLV       => [self::QUALITY_SMALL],
        self::TYPE_THREEGP   => [self::QUALITY_SMALL]
    ];


    /**
     * Returns the default (lowest) quality for the requested format, or null if format is not registered here.
     * @param string $type
     * @return string|null
     */
    static function getDefaultQuality($type){
        $quality = null;

        if(array_key_exists($type, self::$relations)){
            $qualities = self::$relations[$type];
            $quality = $qualities[count($qualities)-1];
        }

        return $quality;
    }
}