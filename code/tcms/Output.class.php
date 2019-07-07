<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 20:19
 */

namespace tcms;


class Output
{
    public function push($sContent) {
        echo $sContent;
    }

    public function json($a) {
        $a = json_encode($a);
        if (!empty($a)) $this->push($a);
    }
}