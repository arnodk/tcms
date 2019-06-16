<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 12:45
 */

namespace tcms;


use tcms\tools\Tools;

class Page
{
    private $template = NULL;
    private $sTitle = "";
    private $aLabels = array();
    private $sHtml = "";
    private $aSections = array("default"=>"");
    private $sCurrentSection = "default";

    /**
     * @var Config
     */
    private $config = NULL;
    /**
     * @var FileSystem
     */
    private $fs = NULL;

    public function __construct(Config $config, FileSystem $fs)
    {
        $this->config = $config;
        $this->fs = $fs;
        $this->template = new Template($this->fs);
    }

    public function setInput($aLabels) {
        $this->aLabels = $aLabels;
    }

    public function getErrors() {

    }

    public function run() {
        // go through all the labels,
        // to set sections and get the html for each section:
        foreach($this->aLabels as $lbl) {
            if ($lbl instanceof Label) {
                $this->renderLabel($lbl);
            }
        }

        // done taking care of the labels, fill up the template with the found sections:
        $this->template->setSections($this->aSections);

        return $this->getHtml();
    }


    private function renderLabel(Label $lbl) {
        switch ($lbl->getName()) {
            case "template":
                $this->template->setName($lbl->getArg(0,""));
                $this->template->load();
                break;
            case "title":
                $this->sTitle = $lbl->getArg(0,"");
                break;
            case "section":
                $this->sCurrentSection = $lbl->getArg(0,"");
                $this->addToSection($lbl->getContent());
                break;
            case "header":
                $this->addToSection("<h1>".htmlspecialchars($lbl->getContent())."</h1>");
                break;
            case "text":
                $this->addToSection("<p>".htmlspecialchars($lbl->getContent())."</p>");
                break;
            case "html":
                $this->addToSection($lbl->getContent());
                break;
        }
    }

    public function addToSection($s) {
        if (!isset($this->aSections[$this->sCurrentSection])) $this->aSections[$this->sCurrentSection]="";

        $this->aSections[$this->sCurrentSection].=$s;
    }

    public function getHtml() {
        $this->sHtml = $this->template->getHtml();
        return $this->sHtml;
    }
}