<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 05.07.2019
 * Time: 13:12
 */
namespace tcms\controllers;

use tcms\Login;
use tcms\Page;
use tcms\tools\Tools;

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

    private function save() {
        $aResult = array();
        if (Login::hasGroup("admin")) {
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

    private function edit() {
        $aResult = array();
        if (Login::hasGroup("admin")) {
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

    private function add() {
        $aResult = array();
        if (Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sPage = $aParam['page'];

            $aResult['status'] = "OK";
            $aResult['name'] = $sPage;
            $aResult['content'] = '';
            $aResult['html'] = '';
        }
        return $aResult;
    }

    private function view() {
        // which page are we on?
        $sPage = $this->router->determinePage();
        $page = new Page($this->context);
        $page->load($sPage);

        return $page->run();
    }

    private function list() {
        $aResult = array();
        if (Login::hasGroup("admin")) {
            $page = new Page($this->context);

            // tell caller everything turned out well:
            $aResult['status'] = "OK";

            $aResult['pages'] = $page->list();
        }
        return $aResult;
    }
}