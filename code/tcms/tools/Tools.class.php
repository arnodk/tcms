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
}