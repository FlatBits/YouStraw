<?php
/**
 * Created by PhpStorm.
 * User: titoeuf
 * Date: 10/4/15
 * Time: 8:34 PM
 */

namespace FlatBits\YouStraw;

use V8Js;
use FlatBits\CurlUtil;

class YTParser{
   /* private $qua = array(
        '141' => '256k AAC',
        '140' => '128k AAC',
        '251' => '160k Opus',
        '250' => '70k Opus',
        '249' => '50k Opus',
        '171' => '128k Vorbis',
        '22 ' => '720p H.264 192k AAC',
        '84 ' => '720p 3D 192k AAC',
        '18 ' => '360p H.264 96k AAC',
        '82 ' => '360p 3D 96k AAC',
        '36 ' => '240p MPEG-4 36k AAC',
        '17 ' => '144p MPEG-4 24k AAC',
        '43 ' => '360p VP8 128k Vorbis',
        '100' => '360p 3D 128k Vorbis',
        '5  ' => '240p H.263 64k MP3',
        '138' => '1440p 4400k H.264',
        '264' => '1440p 3700k H.264',
        '137' => '1080p H.264',
        '136' => '720p H.264',
        '135' => '480p H.264',
        '134' => '360p H.264',
        '133' => '240p H.264',
        '160' => '144p H.264',
        '271' => '1440p VP9',
        '248' => '1080p VP9',
        '247' => '720p VP9',
        '244' => '480p VP9',
        '243' => '360p VP9',
        '242' => '240p VP9',
        '278' => '144p VP9'
    );*/

    public static function loadVideo($videoId){
        $matches = array();
        $ytHtml = CurlUtil::fetch("https://www.youtube.com/watch?v=$videoId");

        preg_match('/ytplayer.config = {.*?};/', $ytHtml, $matches);
        $ytConfig = json_decode(substr($matches[0],18,-1), true);

        $adaptiveFmts = $ytConfig['args']['adaptive_fmts'];
        $urlEncodedFmtStreamMap = $ytConfig['args']['url_encoded_fmt_stream_map'];

/*
        preg_match('/url_encoded_fmt_stream_map":".*?"/', $ytHtml, $matches);
        $urlEncodedFmtStreamMap = substr($matches[0],29,-1);

        preg_match('/adaptive_fmts":".*?"/', $ytHtml, $matches);
        $adaptiveFmts = substr($matches[0],16,-1);
*/
        $args = explode(',', implode(',',[$adaptiveFmts, $urlEncodedFmtStreamMap]));

        foreach($args as $frt){
            parse_str($frt, $qst);

            $hrf = urldecode($qst['url']);
            if(array_key_exists('sig', $qst)){
                $hrf .= '&signature' . $qst['sig'];
            }
            if(array_key_exists('s', $qst)){
                // Hacky stuff here... we need to get the js algorithm that generate the signature
                $js = CurlUtil::fetch('https:' . $ytConfig['assets']['js']);
                $cleanJs = preg_replace('/}\)\(\);\n$/', '', preg_replace('/^\(function\(\){/', '', $js));
                preg_match('/signature\W+(\w+)/', $cleanJs, $matches);
                if(count($matches) > 1){
                    $fn = $matches[1];

                    preg_match('/var \w{1,3}={.*}};function '.$fn.'\(.*?\){.*?};/', $js, $matches);
                    $fnPart = $matches[0];

                    // Then execute that javascript... open to ideas here...
                    $v8 = new V8Js();
                    $sig = $v8->executeString($fnPart.$fn.'("'.$qst['s'].'");');

                    $hrf .= "&signature=$sig";
                }
            }
            $qst['hrf'] = $hrf;
            var_dump($qst);
        }
        die();
    }
}