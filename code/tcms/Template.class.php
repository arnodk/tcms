<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 15:29
 */

namespace tcms;


use tcms\tools\Tools;

class Template
{
    private $sName = "";
    /**
    * @var FileSystem
    */
    private $fs = NULL;
    private $sContent = "";
    private $aLabels = NULL;
    private $aSections = array();
    private $context = NULL;

    public function __construct($context)
    {
        $this->context = $context;
        $this->fs = new FileSystem($context);
    }

    public function setName($s)
    {
        $this->sName = $s;
    }

    public function load()
    {
        $this->sContent = $this->fs->load("template",$this->sName);

        // fill up available sections, by re-using the parser:
        $parser = new Parser();
        $this->aLabels = $parser->parse($this->sContent);

    }

    public function setSections($aPageSections) {
        $this->aSections = $aPageSections;
    }

    public function getHtml()
    {
        $sHtml = "";
        foreach($this->aLabels as $label) {
            if ($label->getName()==="render-section") {
                // if this is a section, fill it up with the content supplied in the aSections array:
                if (isset($this->aSections[$label->getArg(0,'')])) $sHtml.= $this->aSections[$label->getArg(0,'')];
            } else {
                $sHtml.= $label->getContent();
            }
        }

        return $sHtml;
    }
}