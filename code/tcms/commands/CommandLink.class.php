<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

use tcms\FileSystem;
use tcms\Page;
use tcms\Router;
use tcms\tools\Tools;

class CommandLink extends Command
{
    private $sEndPoint = '';
    private $sCaption = '';
    private $sTarget = '';
    private $sClasses = '';
    private $sId = '';

    private function parseArguments() {
        $this->sEndPoint    = $this->token->getArg(0,"");
        $this->sTarget      = $this->token->getArg(1,"");
        $this->sClasses     = $this->token->getArg(2,"");
        $this->sCaption     = $this->token->getContent();
    }

    private function sConstructHref() {
        // check if endpoint is a tcms page, i.e., does it have a protocol or not?
        $sUrl = "";
        $sLinkProtocol = Tools::getURLProtocol($this->sEndPoint);
        // no link protocol, so this could be a reference to a tcms page:
        if (empty($sLinkProtocol)) {
            // check if this page exists:
            $fs = new FileSystem($this->context);
            if ($fs->bExists('page',$this->sEndPoint)) {
                $router = new Router($this->context);
                $sUrl = $router->sConstructLinkForPage($this->sEndPoint);
            } else {
                $this->context->log->add("Unknown link requested: [".$this->sEndPoint."]");
            }
        }
        return htmlspecialchars($sUrl);
    }

    private function sConstructCaption() {
        return htmlspecialchars($this->sCaption);
    }

    private function sConstructClasses() {
        return htmlspecialchars($this->sClasses);
    }

    private function sConstructId() {
        return htmlspecialchars($this->sId);
    }

    public function render() {
        $this->parseArguments();
        $sHref=$this->sConstructHref();
        $sCaption=$this->sConstructCaption();
        $sClasses=$this->sConstructClasses();
        $sId=$this->sConstructId();


        return "<a id=\"".$sId."\" href=\"".$sHref."\" class=\"".$sClasses."\">".$sCaption."</a>";
    }
}