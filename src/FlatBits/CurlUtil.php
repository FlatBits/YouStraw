<?php

namespace FlatBits;


class CurlUtil
{
    /**
     * @param string $url
     * @return string
     */
    public static function fetch($url){
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