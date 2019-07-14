<?php
namespace tcms;


/**
 * Class Variables
 * @package tcms
 *
 * used as quasi-global storage for variables used during rendering templates, blocks and pages by creating an instance of this as a context property.
 * e.g., section names will be stored via this class, so that at, a later stage of rendering, they can be referenced to.
 */
class Variables
{
    public const TYPE_STRING = 0;
    public const TYPE_NUM = 1;

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