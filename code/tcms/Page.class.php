<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 12:45
 */

namespace tcms;


use tcms\commands\Command;
use tcms\tools\Tools;

class Page
{
    private $template = NULL;
    private $sTitle = "";
    private $aLabels = array();
    private $sHtml = "";
    private $aSections = array("default"=>"");
    private $sCurrentSection = "default";

    private $context = false;

    /**
     * @var Config
     */
    private $config = NULL;
    /**
     * @var FileSystem
     */
    private $fs = NULL;

    public function __construct(Context $context)
    {
        $this->context = $context;

        $this->fs = new FileSystem($this->context);
        $this->template = new Template($this->context);
    }

    public function setInput($aLabels) {
        $this->aLabels = $aLabels;
    }

    public function getErrors() {

    }

    public function setTemplateName($s) {
        $this->template->setName($s);
        $this->template->load();
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

    private function getCommand(Label $lbl) {
        $sName = '\tcms\commands\Command'.$lbl->getName();
        if (class_exists($sName)) {
            return new $sName($lbl,$this->context);
        }
        return false;
    }

    private function renderLabel(Label $lbl) {
        switch ($lbl->getName()) {
            // deal with commands used for internal page operations:
            case "template":
                $this->setTemplateName($lbl->getArg(0,""));
                break;
            case "title":
                $this->sTitle = $lbl->getArg(0,"");
                break;
            case "section":
                $this->sCurrentSection = $lbl->getArg(0,"");
                $this->addToSection($lbl->getContent());
                break;
            // and deal with other commands in a plug-in like nature:
            default:
                $command = $this->getCommand($lbl);
                if ($command instanceof Command) $this->addToSection($command->render());
        }
    }

    public function addToSection($s) {
        if (!isset($this->aSections[$this->sCurrentSection])) $this->aSections[$this->sCurrentSection]="";

        $this->aSections[$this->sCurrentSection].=$s;
    }

    public function getSections() {
        return $this->aSections;
    }

    public function getHtml() {
        $this->sHtml = $this->template->getHtml();
        return $this->sHtml;
    }
}