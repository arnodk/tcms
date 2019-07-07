<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 06.07.2019
 * Time: 15:27
 */

namespace tcms;


use tcms\tools\Tools;

class VerifyToken
{

    public function __construct()
    {
        // start session, if it wasn't already running
        @session_start();
    }

    public function new() {
        do {
            $sToken = $this->generateRndStr();
        } while($this->check($sToken));
        $this->add($sToken);

        return $sToken;
    }

    private function generateRndStr() {
        // url safe version of base64 (from php.net):
        return rtrim( strtr( base64_encode( random_bytes(20)), '+/', '-_'), '=');
    }

    public function check($s) {
        if (empty($_SESSION['tcms_verify_tokens']) || !is_array($_SESSION['tcms_verify_tokens'])) return false;
        return in_array($s,$_SESSION['tcms_verify_tokens']);
    }

    public function add($s) {
        if (empty($_SESSION['tcms_verify_tokens']) || !is_array($_SESSION['tcms_verify_tokens'])) $_SESSION['tcms_verify_tokens'] = array();
        $_SESSION['tcms_verify_tokens'][] = $s;
    }

    public function remove($s) {
        $a = $_SESSION['tcms_verify_tokens'];
        // no tokens there? can't delete:
        if (empty($a) || !is_array($a)) return false;
        $i = array_search($s,$a);
        // token not found, can't delete
        if ($i===false) return false;
        // delete found token
        unset($a[$i]);
        $_SESSION['tcms_verify_tokens'] = $a;
        return true;
    }

    public static function apiTokenCheck() {
        $bResult = false;
        $sToken = Tools::request('_apitoken','string','');

        if (!empty($sToken)) {
            $verifyToken = new VerifyToken();
            $bResult = $verifyToken->check($sToken);
        }

        return $bResult;
    }
}