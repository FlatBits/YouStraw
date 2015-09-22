<?php

namespace FlatBits\BitsOfVideo;

use FlatBits\CurlUtil;
use Sunra\PhpSimple\HtmlDomParser;

class VideoCollection
{
    private $videoIds = array();
    private $videoBitsCache = array();

    /**
     * @param string $videoId
     */
    public function addVideo($videoId){
        if(is_string($videoId) && !in_array($videoId, $this->videoIds)){
            $this->videoIds[] = $videoId;
        }
    }

    /**
     * @param array $listOfIds
     */
    public function addVideos($listOfIds){
        if(is_array($listOfIds)){
            foreach($listOfIds as $videoId){
                $this->addVideo($videoId);
            }
        }
    }

    /**
     * @param int $index
     * @return VideoBits|null
     */
    public function getVideoBits($index){
        $videoBits = null;

        if(array_key_exists($index, $this->videoIds)){
            $videoId = $this->videoIds[$index];

            if(array_key_exists($videoId, $this->videoBitsCache)){
                $videoBits = $this->videoBitsCache[$videoId];
            } else {
                $this->videoBitsCache[$videoId] = $videoBits = new VideoBits($videoId, $index);
            }
        }

        return $videoBits;
    }

    /**
     * @param string $folderPath
     * @param Format $format
     * @return bool
     */
    public function downloadAll($folderPath='../cache/videos/', $format=null){
        $success = true;
        foreach($this->videoIds as $index=>$videoId){
            $success = $success && $this->getVideoBits($index)->download($folderPath, $format);
            if(!$success){
                break;
            }
        }
        return $success;
    }

    /**
     * @param string $playlistId
     * @return VideoCollection|null A VideoCollection representation from the playlist
     */
    public static function fromPlaylist($playlistId){
        $videoCollection = null;

        $playListHtmlString = CurlUtil::fetch("https://www.youtube.com/playlist?list=$playlistId");

        $playListHtml = HtmlDomParser::str_get_html($playListHtmlString);
        $videosHtml = $playListHtml->find('tr.pl-video');

        $videoIds = array();
        foreach($videosHtml as $vidHtml){
            $videoIds[] = $vidHtml->getAttribute('data-video-id');
        }

        if(!empty($videoIds)){
            $videoCollection = new VideoCollection();
            $videoCollection->addVideos($videoIds);
        }

        return $videoCollection;
    }
}