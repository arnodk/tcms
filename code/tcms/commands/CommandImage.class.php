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

class CommandImage extends Command
{
    private $sEndPoint = '';
    private $sClasses = '';

    private function parseArguments() {
        $this->sEndPoint    = $this->token->getArg(0,"");
        $this->sClasses     = $this->token->getArg(1,"");
    }

    private function sConstructImageUrl() {
        // check if endpoint is a tcms asset, i.e., does it have a protocol or not?
        $sUrl = "";
        $sLinkProtocol = Tools::getURLProtocol($this->sEndPoint);
        // no link protocol, so this could be a reference to a tcms page:
        if (empty($sLinkProtocol)) {
            // check if this page exists:
            $fs = new FileSystem($this->context);
            if ($fs->bExists('asset',$this->sEndPoint)) {
                $router = new Router($this->context);
                $sUrl = $router->sConstructLinkForAsset($this->sEndPoint);
            } else {
                $this->context->log->add("Unknown asset requested: [".$this->sEndPoint."]");
            }
        }
        return htmlspecialchars($sUrl);
    }

    private function sConstructClasses() {
        return htmlspecialchars($this->sClasses);
    }

    public function render() {
        $this->parseArguments();
        $sImageUrl=$this->sConstructImageUrl();
        $sClasses=$this->sConstructClasses();

        return "<img src=\"".$sImageUrl."\" class=\"".$sClasses."\" />";
    }
}