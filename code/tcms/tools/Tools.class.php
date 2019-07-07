<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 20:23
 */
namespace tcms\tools;

class Tools
{
    public static function dump($m) {
        echo "<pre>";
        var_dump($m);
        echo "</pre>";
    }

    public static function getURLProtocol($sURL) {
        $sProtocol = "";

        $sNormalized = trim(strtolower($sURL));
        if (strpos($sNormalized,"://")!==false) {
            $sPart = substr($sNormalized,0,strpos($sNormalized,"://"));
            // only return known protocols:
            switch($sPart) {
                case "http":
                    $sProtocol = "http";
                    break;
                case "https";
                    $sProtocol = "https";
                    break;
                case "mailto";
                    $sProtocol = "mailto";
                    break;
                case "ws";
                    $sProtocol = "websocket";
                    break;
            }
        }

        return $sProtocol;
    }

    public static function get($sKey,$sType="string",$sIfEmpty="") {
        if (!empty($_POST[$sKey])) return self::post($sKey,$sType);
        if (!empty($_REQUEST[$sKey])) return self::request($sKey,$sType);

        return $sIfEmpty;
    }

    public static function request($sKey,$sType="string",$sIfEmpty="") {
        if (!empty($_GET[$sKey])) return self::sanitizeInput($_GET[$sKey],$sType);

        return $sIfEmpty;
    }

    public static function post($sKey,$sType="string",$sIfEmpty="") {
        if (!empty($_GET[$sKey])) return self::sanitizeInput($_GET[$sKey],$sType);

        return $sIfEmpty;
    }

    public static function sanitizeInput($sValue,$sType="string") {
        switch(strtolower($sType)) {
            case "num":
            case "numeric":
            case "int":
                $m = intval($sValue);
                break;
            case "raw":
                $m = $sValue;   // caution, no filter applied
                break;
            case "entities":
                $m = htmlentities($sValue,ENT_QUOTES);
                break;
            case "phpvar":
                // only allow a-zA-Z0-9
                $m = preg_replace("/[^A-Za-z0-9]/", '', $sValue);
                break;
            case "string":
            default:
                $m = htmlspecialchars($sValue, ENT_QUOTES);
                break;
        }
        return $m;
    }

    public static function getURLDelimiter($sUrl)
    {
        return ((strpos($sUrl,"?")===false)?"?":"&");
    }

    public static function getExtensionFromFileName($sFileName)
    {
        if (strpos($sFileName,".")===false) return "";
        return substr($sFileName,strrpos($sFileName,".")+1);
    }

    public static function jsonPost($bAsAnArray=true) {
        $payload = file_get_contents('php://input');

        $data = json_decode($payload,$bAsAnArray);
        if (empty($data) && $bAsAnArray) $data = array();

        return $data;
    }
}