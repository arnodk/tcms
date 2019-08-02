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

class Block
{
    private $token = NULL;
    private $sContent = "";
    private $sName = "";
    private $iBlockType = 0;

    public const BLOCK_CONTENT   = 1;
    public const BLOCK_ADMIN     = 2;

    private $context = false;

    /**
     * @var Config
     */
    private $config = NULL;
    /**
     * @var FileSystem
     */
    private $fs = NULL;

    public function __construct(Context $context,$iBlockType = self::BLOCK_CONTENT)
    {
        $this->iBlockType = $iBlockType;

        $this->context = $context;

        $this->fs = new FileSystem($this->context);
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


    public function getCategory() {
        $sCategory = '';
        // load and parse the page:
        if ($this->iBlockType==self::BLOCK_CONTENT) {
            $sCategory = "block";
        } elseif ( $this->iBlockType==self::BLOCK_ADMIN) {
            $sCategory = "block_admin";
        }
        return $sCategory;
    }

    public function getContent() {
        return $this->sContent;
    }

    public function setContent($sContent) {
        $this->sContent = $sContent;
    }

    public function save() {
        $this->context->log->add("Saving block: ".$this->getName(),Log::TYPE_INFO);
        if (empty($this->getName())) {
            $this->context->log->add("Save block called with an empty page name",Log::TYPE_ERROR);
            return false;
        }
        $fs = new FileSystem($this->context);
        // we only update content pages:
        return $fs->save('block',$this->getName(),$this->getContent());
    }

    public function delete() {
        $this->context->log->add("Deleting block: ".$this->getName(),Log::TYPE_INFO);

        if (empty($this->getName())) {
            $this->context->log->add("Delete block called with an empty block name",Log::TYPE_ERROR);
            return false;
        }

        // we only delete content blocks, so no category determination necessary:
        $fs = new FileSystem($this->context);
        if (!$fs->bExists('block',$this->getName())) {
            $this->context->log->add("Could not find block name: " .$this->getName(),Log::TYPE_ERROR);
            return false;
        }

        return $fs->delete('block',$this->getName());
    }

    public function load($sBlock) {
        $this->context->log->add("Loading block with name: ".$sBlock,Log::TYPE_INFO);
        if (empty($sBlock)) {
            $this->context->log->add("Load block called with an empty page name",Log::TYPE_INFO);
            return false;
        }
        $this->setName($sBlock);
        $sCategory = $this->getCategory();
        if (!empty($sCategory)) {
            $this->sContent = $this->fs->load($sCategory,$sBlock);
            return true;
        }
        return false;
    }

    public function list() {

        // run through all the pages, and retrieve basic info about them::
        $fs = new FileSystem($this->context);
        $aResult = array();
        foreach($fs->list('block') as $sFilename) {
            // assume we are not listing admin pages:
            $sSummary = trim(htmlspecialchars(Token::removeTokens($this->fs->load('block', $sFilename))));
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