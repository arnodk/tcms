<?php
namespace tcms\controllers;

use tcms\Login;
use tcms\Page;
use tcms\tools\Tools;
use tcms\VerifyToken;

/**
 * Class ControllerLogin
 * @package tcms\controllers
 *
 * controller responsible for updating and logging in of users
 */
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
                $sReason = "";
                if (VerifyToken::apiTokenCheck()) {
                    $aData = Tools::jsonPost();
                    // sanity checks
                    if (!empty($aData['login']) && !empty($aData['password'])) {
                        $login = new Login($this->context);
                        $login->loadForUser($aData['login']);
                        if ($login->pw($aData['password'])) {
                            $login->attachToSession();
                            $bStatus = true;
                        } else {
                            $sReason="invalid credentials";
                        }
                    }
                } else {
                    $sReason = "invalid session";
                }
                // return status
                $a = array("status"=>($bStatus)?'OK':'ERROR');
                if (!empty($sReason)) {
                    $a['reason'] = $sReason;
                }
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