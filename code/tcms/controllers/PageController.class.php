<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 05.07.2019
 * Time: 13:12
 */
namespace tcms\controllers;

use tcms\Page;

class PageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction="view") {
        $sToOutput = "";

        switch($sAction) {
            case "view":
                $sToOutput.=$this->view();
                break;
        }

        if (!empty($sToOutput)) {
            $this->output->push($sToOutput);
        }
    }

    private function view() {
        // which page are we on?
        $sPage = $this->router->determinePage();
        $page = new Page($this->context);
        $page->load($sPage);

        return $page->run();
    }
}