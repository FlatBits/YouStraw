<?php

namespace FlatBits;


use Sunra\PhpSimple\HtmlDomParser;

class VideoCollection
{
    private $videoIds = array();
    private $bitsOfVideoCache = array();

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
     * @return BitsOfVideo|null
     */
    public function getBitsOfVideo($index){
        $bitsOfVideo = null;

        if(array_key_exists($index, $this->videoIds)){
            $videoId = $this->videoIds[$index];

            if(array_key_exists($videoId, $this->bitsOfVideoCache)){
                $bitsOfVideo = $this->bitsOfVideoCache[$videoId];
            } else {
                $this->bitsOfVideoCache[$videoId] = $bitsOfVideo = new BitsOfVideo($videoId, $index);
            }
        }

        return $bitsOfVideo;
    }

    /**
     * @param string $type
     * @param string $quality
     * @param string $folderPath
     * @return bool
     */
    public function downloadAll($type='video/mp4', $quality='medium', $folderPath='../cache/videos/'){
        $success = true;
        foreach($this->videoIds as $index=>$videoId){
            $success = $success && $this->getBitsOfVideo($index)->download($type, $quality, $folderPath);
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