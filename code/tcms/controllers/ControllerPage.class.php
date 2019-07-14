<?php
namespace tcms\controllers;

use tcms\Login;
use tcms\Page;
use tcms\tools\Tools;
use tcms\VerifyToken;

/**
 * Class ControllerPage
 * responsible for displaying and editing of "normal" tcms pages found in content/pages directory.
 *
 * @package tcms\controllers
 */
class ControllerPage extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction="view") {
        if (empty($sAction)) $sAction="view";
        switch($sAction) {
            case "view":
                $sToOutput=$this->view();
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
            case "list":
                $this->output->json($this->list());
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
            $sPage = $aParam['page'];
            $sContent = $aParam['content'];
            if (!empty($sPage)) {
                $page = new Page($this->context);
                $page->setContent($sContent);
                $page->setName($sPage);
                if ($page->save()) {
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
            $sPage = $aParam['page'];
            if (!empty($sPage)) {
                $page = new Page($this->context);
                if ($page->load($sPage)) {
                    $aResult['status'] = "OK";
                    $aResult['name'] = $sPage;
                    $aResult['content'] = $page->getContent();
                    $aResult['html'] = $page->getHtml();
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
            $aParam = Tools::jsonPost();
            $sPage = $aParam['page'];

            $aResult['status'] = "OK";
            $aResult['name'] = $sPage;
            $aResult['content'] = '';
            $aResult['html'] = '';
        }
        return $aResult;
    }

    /**
     * calls the router to figure out which page is being requested, and try to display it
     *
     * @return string
     */
    private function view() {
        // which page are we on?
        $sPage = $this->router->determinePage();
        $page = new Page($this->context);
        $page->load($sPage);

        return $page->run();
    }

    /**
     * return an array of all pages
     *
     * @return array
     */
    private function list() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $page = new Page($this->context);

            // tell caller everything turned out well:
            $aResult['status'] = "OK";

            $aResult['pages'] = $page->list();
        }
        return $aResult;
    }
}