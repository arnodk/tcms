<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 05.07.2019
 * Time: 13:12
 */
namespace tcms\controllers;

use tcms\Page;
use tcms\tools\Tools;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run() {
        $sToOutput = "";

        $sSystem = strtolower(Tools::get('system','string',''));
        $sAction = strtolower(Tools::get('action','string',''));

        if ($sSystem==="login") {
            switch($sAction) {
                case 'login':
                default:
                    $page = new Page($this->context);
                    $page->load("login",Page::PAGE_ADMIN);
                    $sToOutput=$page->run();
            }
        }

        if (!empty($sToOutput)) {
            $this->output->push($sToOutput);
        }
    }
}