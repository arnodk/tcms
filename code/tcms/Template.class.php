<?php
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
    private $token = NULL;
    private $aSections = array();
    private $context = NULL;
    private $sHtml = '';

    public const TEMPLATE_CONTENT   = 1;
    public const TEMPLATE_ADMIN     = 2;

    public function __construct($context)
    {
        $this->aLabels = array();
        $this->context = $context;
        $this->fs = new FileSystem($context);
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setName($s)
    {
        $this->sName = $s;
    }

    public function getName() {
        return $this->sName;
    }

    public function save() {
        $this->context->log->add("Saving template: ".$this->getName(),Log::TYPE_INFO);
        if (empty($this->getName())) {
            $this->context->log->add("Save template called with an empty page name",Log::TYPE_ERROR);
            return false;
        }
        $fs = new FileSystem($this->context);

        return $fs->save('template',$this->getName(),$this->getContent());
    }

    public function delete() {
        $this->context->log->add("Deleting template: ".$this->getName(),Log::TYPE_INFO);

        if (empty($this->getName())) {
            $this->context->log->add("Delete template called with an empty template name",Log::TYPE_ERROR);
            return false;
        }

        // we only delete content blocks, so no category determination necessary:
        $fs = new FileSystem($this->context);
        if (!$fs->bExists('template',$this->getName())) {
            $this->context->log->add("Could not find template name: " .$this->getName(),Log::TYPE_ERROR);
            return false;
        }

        return $fs->delete('template',$this->getName());
    }

    public function load($iTemplateType=self::TEMPLATE_CONTENT) {
        $this->context->log->add("Loading template with name: ".$this->getName(),Log::TYPE_INFO);
        if (empty($this->getName())) {
            $this->context->log->add("Load template called with an empty page name",Log::TYPE_INFO);
            return false;
        }

        if ($iTemplateType==self::TEMPLATE_CONTENT) {
            $sCategory = "template";
        } elseif ($iTemplateType==self::TEMPLATE_ADMIN) {
            $sCategory = "template_admin";
        }

        if (!empty($sCategory)) {
            // load and parse the page:
            $this->setContent($this->fs->load($sCategory,$this->sName));
            $token = Parser::parse($this->sContent);
            if (!empty($token)) {
                // render the parsed content:
                $this->setToken($token);
            }
            return true;
        }

        return false;
    }

    public function render() {
        $this->renderToken();
        return $this->sHtml;
    }

    public function getHtml()
    {
        return $this->sHtml;
    }

    private function renderToken() {
        if (empty($this->token)) {
            $this->sHtml = "";
        } else {
            $this->sHtml=Render::render($this->token,$this->context);
        }
    }

    public function getContent() {
        return $this->sContent;
    }

    public function setContent($s) {
        $this->sContent = $s;
    }

    public function list() {

        // run through all the pages, and retrieve basic info about them::
        $fs = new FileSystem($this->context);
        $aResult = array();
        foreach($fs->list('template') as $sFilename) {
            // assume we are not listing admin pages:
            $sSummary = trim(htmlspecialchars(Token::removeTokens($this->fs->load('template', $sFilename))));
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