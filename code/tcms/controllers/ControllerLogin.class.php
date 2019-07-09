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
use tcms\VerifyToken;

class ControllerLogin extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction='') {
        switch($sAction) {
            case 'login':
                $bStatus = false;
                if (VerifyToken::apiTokenCheck()) {
                    $aData = Tools::jsonPost();
                    // sanity checks
                    if (!empty($aData['login']) && !empty($aData['password'])) {
                        $login = new Login($this->context);
                        $login->loadForUser($aData['login']);
                        if ($login->pw($aData['password'])) {
                            $login->attachToSession();
                            $bStatus = true;
                        }
                    }
                }
                // return status
                $a = array("status"=>($bStatus)?'OK':'ERROR');
                $this->output->json($a);
                break;
            default:
                $page = new Page($this->context,Page::PAGE_ADMIN);
                $page->load("login");
                $sToOutput=$page->run();
                if (!empty($sToOutput)) {
                    $this->output->push($sToOutput);
                }
        }
    }
}