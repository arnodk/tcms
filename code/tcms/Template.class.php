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

    public function load($iTemplateType=self::TEMPLATE_CONTENT) {
        if ($iTemplateType==self::TEMPLATE_CONTENT) {
            $sCategory = "template";
        } elseif ($iTemplateType==self::TEMPLATE_ADMIN) {
            $sCategory = "template_admin";
        }

        if (!empty($sCategory)) {
            // load and parse the page:
            $token = Parser::parse($this->fs->load($sCategory,$this->sName));
            if (!empty($token)) {
                // render the parsed content:
                $this->setToken($token);
            }
        }
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

}