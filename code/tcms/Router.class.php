<?php
namespace tcms;

use tcms\controllers\Controller;
use tcms\tools\Tools;

class Router {
    /**
     * @var null|Context
     */
    private $context = null;

    public function __construct(Context $c)
    {
        $this->context = $c;
    }

    public function sConstructLinkForPage($sPage)
    {
        // pages do not include "." symbols
        $sPage = str_replace(".","",$sPage);
        $sPage = urlencode($sPage);

        $sUrl = "";

        if ($this->context->config->getURLMode()=== $this->context->config::FRIENDLY_URL_MODE_PARAM) {
            $sUrl=$this->context->config->getBaseURL()."/index.php?page=".$sPage;
        } elseif ($this->context->config->getURLMode()===$this->context->config::FRIENDLY_URL_MODE_SEO) {
            $sUrl=$this->context->config->getBaseURL()."/".$sPage;
        }

        if ($this->context->config->getDebugLevel() >= $this->context->config::DEBUG_LEVEL_DEV) {
            $sUrl=$sUrl.Tools::getURLDelimiter($sUrl)."_ijt=".Tools::get('_ijt','string','');
        }

        return $sUrl;
    }

    /**
     * @return null | Controller
     */
    public static function getController() {

        $sSystem = Tools::get('system','phpvar','page');

        $sName = '\tcms\controllers\Controller'.$sSystem;
        if (class_exists($sName)) {
            return new $sName();
        } else {
            // we are contextless here (they are usually initiated in the controller), so need a new context:
            $context = new Context();
            $context->log->add('could not load controller ['.$sName.']', $context->log::TYPE_ERROR);
        }

        return null;
    }

    public function determinePage() {
        $sPage = Tools::get('page','string','start');

        $fs = new FileSystem($this->context);
        if (!$fs->bExists('page',$sPage)) {
            $this->context->log->add("page [" . $sPage . "] not found ", Log::TYPE_WARNING);
        }

        return $sPage;
    }

    public function sConstructLinkForAsset($sEndPoint)
    {
        $sEndPoint = urlencode($sEndPoint);
        $sUrl=$this->context->config->getBaseURL()."/assets/".$sEndPoint;
        return $sUrl;
    }

}