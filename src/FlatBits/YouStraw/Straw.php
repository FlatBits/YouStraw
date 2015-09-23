<?php

namespace FlatBits\YouStraw;

use FlatBits\YouStraw\Format\Mp4;
use FlatBits\CurlUtil;

class Straw
{
    /**
     * Id of the video it represents
     * @var string
     */
    private $videoId;
    /**
     * Title of the video
     * @var string
     */
    private $videoTitle;
    /**
     * Array of video sources
     * @var array
     */
    private $sources;

    /**
     * Index from the playlist for numbering the download
     * @var int
     */
    private $playlistIndex;

    /**
     * @param string $videoId
     * @param int|null $playlistIndex
     */
    public function __construct($videoId, $playlistIndex = null){
        $this->videoId = $videoId;
        $this->playlistIndex = $playlistIndex;
    }

    /**
     * Actually fetch and parse the video info
     */
    public function loadVideo(){
        if($this->sources === null) {
            $videoInfo = CurlUtil::fetch("http://youtube.com/get_video_info?video_id=$this->videoId");
            $videoData = array();
            parse_str($videoInfo, $videoData);

            // echo json_encode($videoData);die();

            $this->videoTitle = $videoData['title'];
            $this->sources = array();
            $ref = explode(',', $videoData['url_encoded_fmt_stream_map']);

            foreach ($ref as $source) {
                $stream = array();
                parse_str($source, $stream);
                $type = explode(';', $stream['type'])[0];
                $quality = explode(',', $stream['quality'])[0];

                if (!array_key_exists($type, $this->sources) || !is_array($this->sources[$type])) {
                    $this->sources[$type] = array();
                }
                $this->sources[$type][$quality] = $stream;
            }
        }
    }

    /**
     * Returns the parser video source object
     * @param string $type
     * @param string $quality
     * @return string|null The source url
     */
    public function getVideoSource($type, $quality){
        $this->loadVideo();

        $videoSource = null;

        if(array_key_exists($type, $this->sources) && array_key_exists($quality, $this->sources[$type])){
            $videoSource = $this->sources[$type][$quality];
        }

        return $videoSource;
    }

    /**
     * Lists the available types and their available quality
     * @return array
     */
    public function listTypes(){
        $this->loadVideo();
        return array_map(function($o){return array_keys($o);}, $this->sources);
    }

    /**
     * @param string $folderPath
     * @param Format $format
     * @return bool
     */
    public function download($folderPath='../cache/videos/', $format=null){
        $success = false;

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Format default checking
        if($format == null){
            $format = new Mp4();
        }
        $type = $format->getTypeString();

        $videoSource = $this->getVideoSource($type, $format->getQuality());
        if($videoSource !== null){
            $vidUrl = $videoSource['url'];

            // Get extension from type
            $explodedType = explode('/', $type);
            $ext = array_pop($explodedType);

            // Add trailing slash if missing
            $folderPath .= (substr($folderPath, -1) == '/' ? '' : '/');

            // Add playlist numbering if present
            $prefix = $this->playlistIndex !== null ? "$this->playlistIndex. " : '';

            // Glue the filename together
            $filename = $folderPath.$prefix.$this->videoTitle.".$ext";

            // Download
            file_put_contents($filename, fopen($vidUrl, 'r'));

            // Invoke the format post download
            $format->postDownload($filename);

            $success = true;
        }

        return $success;
    }
}