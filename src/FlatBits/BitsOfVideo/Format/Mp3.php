<?php

namespace FlatBits\BitsOfVideo\Format;

use FFMpeg\FFMpeg;
use FlatBits\BitsOfVideo\Format;

class Mp3 extends Format{
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
        // We return this type because we want to download the mp4 video to then convert it to an mp3.
        // Youtube does not offer direct audio download
        return 'video/mp4';
    }

    /**
     * Takes the downloaded mp4 file, convert it to an mp3 and delete the video file.
     * @param string $filePath
     */
    public function postDownload($filePath){
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($filePath);

        // Generate the mp3 filepath from the mp4 one
        $mp3FilePath = substr($filePath, 0, -1).'3';

        // Configure the format
        $audioFormat = new \FFMpeg\Format\Audio\Mp3();
        if($this->getQuality() == self::QUALITY_HIGH){
            $bitrate = 320;
        } else {
            $bitrate = 192;
        }
        $audioFormat->setAudioKiloBitrate($bitrate);

        // Do the conversion
        $video->save($audioFormat, $mp3FilePath);

        // Delete the video file
        unlink($filePath);
    }
}