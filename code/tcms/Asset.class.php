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

class Asset
{
    private $sName = "";

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
    }


    public function setName($sName) {
        $this->sName = $sName;
    }

    public function getName() {
        return $this->sName;
    }

    public function getErrors() {

    }

    public function list() {
        // run through all the assets, and retrieve basic info about them:
        $fs = new FileSystem($this->context);
        $aResult = array();
        foreach($fs->list('asset',false) as $sFilename) {
            // assume we are not listing admin pages:
            $sSummary = "Filesize: ".$fs->captionFileSize('asset',$sFilename);
            $aPage = array(
                "name"          => $sFilename,
                "nameSafe"      => htmlspecialchars($sFilename,ENT_QUOTES),
                "summary"       => $sSummary
            );
            $aResult[] = $aPage;
        }
        return $aResult;
    }

    public function delete() {
        $this->context->log->add("Deleting asset: ".$this->getName(),Log::TYPE_INFO);

        if (empty($this->getName())) {
            $this->context->log->add("Delete asset called with an empty asset name",Log::TYPE_ERROR);
            return false;
        }

        // we only delete content pages, so no category determination necessary:
        $fs = new FileSystem($this->context);
        if (!$fs->bExists('asset',$this->getName())) {
            $this->context->log->add("Could not find asset name: " .$this->getName(),Log::TYPE_ERROR);
            return false;
        }

        return $fs->delete('asset',$this->getName());

    }
}