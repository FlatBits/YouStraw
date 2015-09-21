<?php

namespace FlatBits;

use Sunra\PhpSimple\HtmlDomParser;

class BitsOfVideo
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
     */
    public function __construct($videoId, $playlistIndex = null){
        $this->videoId = $videoId;
        $this->playlistIndex = $playlistIndex;
    }

    /**
     * @param string $videoId
     */
    public function loadVideo(){
        if($this->sources === null) {
            $videoInfo = self::fetch("http://youtube.com/get_video_info?video_id=$this->videoId");
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

    public function download($type, $quality, $folderPath){
        $success = false;

        $videoSource = $this->getVideoSource($type, $quality);
        if($videoSource !== null){
            $vidUrl = $videoSource['url'];

            // Get extension from type
            $explodedType = explode('/', $type);
            $ext = array_pop($explodedType);

            // Add trailing slash if missing
            $folderPath .= (substr($folderPath, -1) == '/' ? '' : '/');

            // Add playlist numbering if present
            $prefix = $this->playlistIndex != null ? "$this->playlistIndex. " : '';

            // Glue the filename together
            $filename = $prefix.$this->videoTitle.".$ext";

            // Download
            file_put_contents($folderPath.$filename, fopen($vidUrl, 'r'));
        }

        return $success;
    }

    /**
     * @param string $playlistId
     * @return array an array of BitsOfVideo from all the video of the playlist
     */
    public static function fromPlaylist($playlistId){
        $videoBits = null;

        $playListHtmlString = self::fetch("https://www.youtube.com/playlist?list=$playlistId");

        $playListHtml = HtmlDomParser::str_get_html($playListHtmlString);
        $videosHtml = $playListHtml->find('tr.pl-video');

        $videoIds = array();
        foreach($videosHtml as $vidHtml){
            $videoIds[] = $vidHtml->getAttribute('data-video-id');
        }

        if(!empty($videoIds)){
            $videoBits = array_map(function($o, $i){return new BitsOfVideo($o, $i);}, $videoIds, array_keys($videoIds));
        }

        return $videoBits;
    }

    /**
     * @param string $url
     * @return string
     */
    private static function fetch($url){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        return $resp;
    }
}