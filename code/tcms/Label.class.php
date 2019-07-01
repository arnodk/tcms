<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 12:46
 */

namespace tcms;


// helper class for storing labels:
class Label {
    private $sName="";
    private $sContent="";
    private $aArgs = array();

    public function __construct($sName="",$aArgs="")
    {
        if (!empty($sName)) $this->sName = strtolower($sName);
        if (is_array($aArgs)) $this->aArgs=$aArgs;
    }

    public function addLine($sLine)
    {
        $this->sContent.=$sLine."\n";
    }

    public function getName() {
        return $this->sName;
    }

    public function getArg($iIndex, $mIfEmpty=false) {
        if (empty($this->aArgs[$iIndex])) return $mIfEmpty;

        return $this->aArgs[$iIndex];
    }

    public function getContent() {
        return $this->sContent;
    }
    
}