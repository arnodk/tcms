<?php
namespace tcms\controllers;

use tcms\Block;
use tcms\Login;
use tcms\Page;
use tcms\tools\PageFilter;
use tcms\tools\Tools;
use tcms\VerifyToken;

/**
 * Class ControllerBlock
 * responsible for displaying and editing of blocks found in content/blocks directory.
 *
 * @package tcms\controllers
 */
class ControllerBlock extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction="view",$aOptions = false) {
        if (empty($sAction)) $sAction="view";
        switch($sAction) {
            case "view":
                $sBlock = false;
                if (is_array($aOptions) && !empty($aOptions['block'])) $sBlock = $aOptions['block'];
                $sToOutput=$this->view($sBlock);
                if (!empty($sToOutput)) {
                    $this->output->push($sToOutput);
                }
                break;
            case "edit":
                $this->output->json($this->edit());
                break;
            case "add":
                $this->output->json($this->add());
                break;
            case "save":
                $this->output->json($this->save());
                break;
            case "delete":
                $this->output->json($this->delete());
                break;
            case "list":
                $page = intval(Tools::jsonPostKey('page',1));
                $this->output->json($this->list($page));
                break;
        }


    }

    /**
     * takes a posted page, saves it and returns a json object with 'status'=>'OK' when successful.
     *
     * @return array
     */
    private function save() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sBlock = $aParam['block'];
            $sContent = $aParam['content'];
            if (!empty($sBlock)) {
                $block = new Block($this->context);
                $block->setContent($sContent);
                $block->setName($sBlock);
                if ($block->save()) {
                    $aResult['status'] = "OK";
                }
            }
        }
        return $aResult;
    }

    /**
     * delete block with the name supplied in a jsonPost.
     *
     * @return array
     */
    private function delete() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sBlock = $aParam['block'];
            if (!empty($sBlock)) {
                $block = new Block($this->context);
                $block->setName($sBlock);
                if ($block->delete()) {
                    $aResult['status'] = "OK";
                }
            }
        }
        return $aResult;
    }

    /**
     * takes a posted page name, and, if found, returns a json object with page info and content.
     * an empty json object is returned, if the page could not be returned
     *
     * @return array
     */
    private function edit() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sBlock = $aParam['block'];
            if (!empty($sBlock)) {
                $block = new Block($this->context);
                if ($block->load($sBlock)) {
                    $aResult['status'] = "OK";
                    $aResult['name'] = $sBlock;
                    $aResult['content'] = $block->getContent();
                    $aResult['html'] = $block->getContent();
                }
            }
        }
        return $aResult;
    }

    /**
     * returns a json object with empty page content.
     * an entirely empty json object is returned, if the user has no rights to view this page.
     *
     * @return array
     */
    private function add() {
        $aResult = array();

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            // TODO: check if this name does not already exist, and if not, use some kind of locking mechanism to reserve it for this user.

            $aParam = Tools::jsonPost();
            $sBlock = $aParam['block'];

            $aResult['status'] = "OK";
            $aResult['name'] = $sBlock;
            $aResult['content'] = '';
            $aResult['html'] = '';
        }

        return $aResult;
    }


    /**
     * return an array of all pages
     *
     * @return array
     */
    private function list($iPage = 1) {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $block = new Block($this->context);

            // tell caller everything turned out well:
            $aResult['status'] = "OK";

            $pf = new PageFilter();
            $pf->setData($block->list());
            $pf->setPage($iPage);

            $aResult['list_data'] = $pf->dataForPage();

        } else {
            $aResult['status'] = 'FAILED';
        }
        return $aResult;
    }
}