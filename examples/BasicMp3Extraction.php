<?php

require_once('../vendor/autoload.php');

use FlatBits\BitsOfVideo\Format\Mp3;
use FlatBits\BitsOfVideo\VideoBits;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

$videoId = 'e_zVCF3qicQ';

$video = new VideoBits($videoId);
$video->download('../cache/music', new Mp3());

