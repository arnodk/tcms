<?php
namespace tcms\controllers;

use tcms\Login;
use tcms\Page;
use tcms\tools\PageFilter;
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
            case "list":
                $page = intval(Tools::jsonPostKey('page',1));
                $this->output->json($this->list($page));
                break;
            case "add":
                if (VerifyToken::apiTokenCheck()) {
                    $aData = Tools::jsonPost();
                    $this->output->json($this->add($aData));
                }
                break;
            case "save":
                if (VerifyToken::apiTokenCheck()) {
                    $aData = Tools::jsonPost();
                    $this->output->json($this->save($aData));
                }
                break;
            case "get_groups":
                if (VerifyToken::apiTokenCheck()) {
                    $aData = Tools::jsonPost();
                    $this->output->json($this->getGroups($aData));
                }
                break;
            case "set_groups":
                if (VerifyToken::apiTokenCheck()) {
                    $aData = Tools::jsonPost();
                    $this->output->json($this->setGroups($aData));
                }
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

    private function list($iPage = 1) {
        $aResult = array();

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {

            $login = new Login($this->context);

            $pf = new PageFilter();
            $pf->setData($login->list());
            $pf->setPage($iPage);

            $aResult['list_data'] = $pf->dataForPage();

            // tell caller everything turned out well:
            $aResult['status'] = "OK";
        } else {
            $aResult['status'] = "FAILED";
        }

        return $aResult;
    }

    private function add($aData) {
        // return a failure, if user already exists,
        // otherwise, use save function to persist info:
        $aResult = [];
        $aResult['status'] = "FAILED";

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $login = new Login($this->context);
            if ($login->exists($aData['user'])) {
                $aResult['reason'] = "user already exists";
            } else {
                $aResult = $this->save($aData);
            }
        }

        return $aResult;
    }

    private function save($aData) {
        $aResult = array();
        $aResult['status'] = "FAILED";

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin") && !empty($aData["user"])) {
            $login = new Login($this->context);
            $login->setUser($aData['user']);
            $login->setGroupsAsString($aData['groups']);
            $login->determineHash($aData['passw']);

            if ($login->save()) {
                // tell caller everything turned out well:
                $aResult['status'] = "OK";
            } else {
                $aResult['reason'] = "could not save user";
            }
        }

        return $aResult;
    }

    private function getGroups($aData) {
        // warning,  this returns all the groups as keys in an associative array,
        // with the value being YES or NO depending whether the user is a member of that group.
        // so, do not use the result of this method as an in_array kind of check.
        $aResult = [];
        $aResult['status'] = "FAILED";

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $login = new Login($this->context);
            if ($login->exists($aData['user'])) {
                if ($login->loadForUser($aData['user'])) {
                    $aResult['groups'] = $login->getGroupsSelection();
                    $aResult['status'] = "OK";
                } else {
                    $aResult['reason'] = "user info invalid";
                }
            } else {
                $aResult['reason'] = "user does not exist";
            }
        }

        return $aResult;
    }

    private function setGroups($aData) {
        $aResult = [];
        $aResult['status'] = "FAILED";

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $login = new Login($this->context);
            if ($login->exists($aData['user'])) {
                if ($login->loadForUser($aData['user'])) {
                    $login->setGroups($aData['groups']);
                    if ($login->save()) {
                        $aResult['status'] = "OK";
                    } else {
                        $aResult['reason'] = "could not save user";
                    }
                } else {
                    $aResult['reason'] = "user info invalid";
                }
            } else {
                $aResult['reason'] = "user does not exist";
            }
        }

        return $aResult;
    }
}