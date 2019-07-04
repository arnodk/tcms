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
    private $token = NULL;
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

    public function setToken($token) {
        $this->token = $token;
    }

    public function getErrors() {

    }

    public function setTemplateName($s) {
        $this->template->setName($s);
        $this->template->load();
    }

    public function run() {
        // render page content:
        // this should fill up all sections with the relevant content
        $this->renderToken();

        // retrieve global template name, if none was previously set:
        if (empty($this->template->getName())) $this->setTemplateName($this->context->vars->getValue('page:template',''));

        if (!empty($this->template->getName())) {
            $this->setTemplateName($this->template->getName());
        } else {
            // if we could not find any template, just use default:
            $this->setTemplateName('default');
        }

        // render template, this will insert the content of the sections at the appropriate place in the template:
        $this->sHtml = $this->template->render();

        return $this->getHtml();
    }


    private function renderToken() {
        $this->sHtml=Render::render($this->token,$this->context);
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

    public function load($sPage) {
        // load and parse the page:
        $token = Parser::parse($this->fs->load("page",$sPage));
        if (!empty($token)) {
            // render the parsed content:
            $this->setToken($token);
        }
    }
}