<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 23.06.2019
 * Time: 19:24
 */

namespace tcms\tools;


class Variables
{
    const TYPE_STRING = 0;
    const TYPE_NUM = 1;

    private $aVars = array();

    public function set($sName,$sValue,$iType=self::TYPE_STRING) {
        $this->aVars[$sName] = array(
            "value"=>$sValue,
            "type"=>$iType
        );
    }

    public function getValue($sName,$sIfNotSet="") {
        if (!isset($this->aVars[$sName]) || !isset($this->aVars[$sName]["value"])) return $sIfNotSet;
        return $this->aVars[$sName]["value"];
    }
}