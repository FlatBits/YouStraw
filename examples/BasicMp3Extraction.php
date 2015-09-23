<?php

require_once('../vendor/autoload.php');

use FlatBits\YouStraw\Format\Mp3;
use FlatBits\YouStraw\Straw;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

$videoId = 'e_zVCF3qicQ';

$video = new Straw($videoId);
$video->download('../cache/music', new Mp3());

