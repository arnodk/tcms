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
    private $token = NULL;
    private $sHtml = "";
    private $sContent = "";
    private $sName = "";
    private $aSections = array("default"=>"");
    private $sCurrentSection = "default";
    private $iPageType = 0;

    public const PAGE_CONTENT   = 1;
    public const PAGE_ADMIN     = 2;

    private $context = false;

    /**
     * @var Config
     */
    private $config = NULL;
    /**
     * @var FileSystem
     */
    private $fs = NULL;

    public function __construct(Context $context,$iPageType = self::PAGE_CONTENT)
    {
        $this->iPageType = $iPageType;

        $this->context = $context;

        $this->fs = new FileSystem($this->context);
        $this->template = new Template($this->context);
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setName($sName) {
        $this->sName = $sName;
    }

    public function getName() {
        return $this->sName;
    }

    public function getErrors() {

    }

    public function setTemplateName($s) {
        $this->template->setName($s);
        if ($this->iPageType==self::PAGE_CONTENT) {
            $iTemplateType = Template::TEMPLATE_CONTENT;
        } elseif ($this->iPageType==self::PAGE_ADMIN) {
            $iTemplateType = Template::TEMPLATE_ADMIN;
        }
        if (!empty($iTemplateType) && !empty($s)) $this->template->load($iTemplateType);
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

    public function getCategory() {
        $sCategory = '';
        // load and parse the page:
        if ($this->iPageType==self::PAGE_CONTENT) {
            $sCategory = "page";
        } elseif ( $this->iPageType==self::PAGE_ADMIN) {
            $sCategory = "page_admin";
        }
        return $sCategory;
    }

    public function load($sPage) {
        $this->context->log->add("Loading page with name: ".$sPage,Log::TYPE_INFO);
        if (empty($sPage)) {
            $this->context->log->add("Load page called with an empty page name",Log::TYPE_ERROR);
            return false;
        }
        $this->setName($sPage);
        $sCategory = $this->getCategory();
        if (!empty($sCategory)) {
            $this->sContent = $this->fs->load($sCategory,$sPage);
            $token = Parser::parse($this->sContent);
            if (!empty($token)) {
                // render the parsed content:
                $this->setToken($token);
                return true;
            }
        }
        return false;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getContent() {
        return $this->sContent;
    }

    public function setContent($sContent) {
        $this->sContent = $sContent;
    }

    public function save() {
        $this->context->log->add("Saving page: ".$this->getName(),Log::TYPE_INFO);

        if (empty($this->getName())) {
            $this->context->log->add("Save page called with an empty page name",Log::TYPE_ERROR);
            return false;
        }
        $fs = new FileSystem($this->context);
        // we only update content pages:
        return $fs->save('page',$this->getName(),$this->getContent());
    }

    public function list() {

        // run through all the pages, and retrieve basic info about them::
        $fs = new FileSystem($this->context);
        $aResult = array();
        foreach($fs->list('page') as $sFilename) {
            // assume we are not listing admin pages:
            $sSummary = trim(Token::removeTokens($this->fs->load('page', $sFilename)));
            $aPage = array(
                "name"          => $sFilename,
                "nameSafe"      => htmlspecialchars($sFilename,ENT_QUOTES),
                "summary"       => $sSummary
            );
            $aResult[] = $aPage;
        }
        return $aResult;
    }
}