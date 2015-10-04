<?php

require_once('../../vendor/autoload.php');

use FlatBits\YouStraw\Format;
use FlatBits\YouStraw\Format\Mp4;
use FlatBits\YouStraw\StrawCollection;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

$videoId = $_GET['v'];

$collection = StrawCollection::fromPlaylist($videoId);
if($collection === null){
    $collection = new StrawCollection();
    $collection->addVideo($videoId);
}


//echo json_encode($straw->getVideoSource('video/mp4', 'medium'));die();

$collection->downloadAll('../../cache/video', new Mp4(Format::QUALITY_MEDIUM));
