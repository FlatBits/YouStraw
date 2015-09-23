<?php

namespace FlatBits\YouStraw;

use FlatBits\CurlUtil;
use Sunra\PhpSimple\HtmlDomParser;

class StrawCollection
{
    private $videoIds = array();
    private $strawCache = array();

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
     * @return Straw|null
     */
    public function getVideoStraw($index){
        $straw = null;

        if(array_key_exists($index, $this->videoIds)){
            $videoId = $this->videoIds[$index];

            if(array_key_exists($videoId, $this->strawCache)){
                $straw = $this->strawCache[$videoId];
            } else {
                $this->strawCache[$videoId] = $straw = new Straw($videoId, $index);
            }
        }

        return $straw;
    }

    /**
     * @param string $folderPath
     * @param Format $format
     * @return bool
     */
    public function downloadAll($folderPath='../cache/videos/', $format=null){
        $success = true;
        foreach($this->videoIds as $index=>$videoId){
            $success = $success && $this->getVideoStraw($index)->download($folderPath, $format);
            if(!$success){
                break;
            }
        }
        return $success;
    }

    /**
     * @param string $playlistId
     * @return StrawCollection|null A StrawCollection representation from the playlist
     */
    public static function fromPlaylist($playlistId){
        $strawCollection = null;

        $playListHtmlString = CurlUtil::fetch("https://www.youtube.com/playlist?list=$playlistId");

        $playListHtml = HtmlDomParser::str_get_html($playListHtmlString);
        $videosHtml = $playListHtml->find('tr.pl-video');

        $videoIds = array();
        foreach($videosHtml as $vidHtml){
            $videoIds[] = $vidHtml->getAttribute('data-video-id');
        }

        if(!empty($videoIds)){
            $strawCollection = new StrawCollection();
            $strawCollection->addVideos($videoIds);
        }

        return $strawCollection;
    }
}