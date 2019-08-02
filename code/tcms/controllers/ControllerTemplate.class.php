<?php
namespace tcms\controllers;

use tcms\Block;
use tcms\Login;
use tcms\Page;
use tcms\Template;
use tcms\tools\Tools;
use tcms\VerifyToken;

/**
 * Class ControllerTemplate
 * responsible for displaying and editing of blocks found in content/templates directory.
 *
 * @package tcms\controllers
 */
class ControllerTemplate extends Controller
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
                $this->output->json($this->list());
                break;
        }
    }

    /**
     * takes a posted template, saves it and returns a json object with 'status'=>'OK' when successful.
     *
     * @return array
     */
    private function save() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sTemplate = $aParam['template'];
            $sContent = $aParam['content'];
            if (!empty($sTemplate)) {
                $template = new Template($this->context);
                $template->setContent($sContent);
                $template->setName($sTemplate);
                if ($template->save()) {
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
            $sTemplate = $aParam['template'];
            if (!empty($sTemplate)) {
                $template = new Template($this->context);
                $template->setName($sTemplate);
                if ($template->delete()) {
                    $aResult['status'] = "OK";
                }
            }
        }
        return $aResult;
    }

    /**
     * takes a posted template name, and, if found, returns a json object with page info and content.
     * an empty json object is returned, if the page could not be returned
     *
     * @return array
     */
    private function edit() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sTemplate = $aParam['template'];
            if (!empty($sTemplate)) {
                $template = new Template($this->context);
                $template->setName($sTemplate);
                if ($template->load()) {
                    $aResult['status'] = "OK";
                    $aResult['name'] = $sTemplate;
                    $aResult['content'] = $template->getContent();
                    $aResult['html'] = $template->getContent();
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
            $sTemplate = $aParam['template'];

            $aResult['status'] = "OK";
            $aResult['name'] = $sTemplate;
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
    private function list() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $template = new Template($this->context);

            // tell caller everything turned out well:
            $aResult['status'] = "OK";

            $aResult['templates'] = $template->list();
        }
        return $aResult;
    }
}