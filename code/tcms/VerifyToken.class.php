<?php
namespace tcms;

use tcms\tools\Tools;

/**
 * Class VerifyToken
 * @package tcms
 *
 * generate and check tokens used during api communication
 */
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

    /**
     * checks if $s is a registered token.
     *
     * @param $s
     * @return bool
     */
    public function check($s) {
        if (empty($_SESSION['tcms_verify_tokens']) || !is_array($_SESSION['tcms_verify_tokens'])) return false;
        return in_array($s,$_SESSION['tcms_verify_tokens']);
    }

    /**
     * add $s as a registered token,
     *
     * @param $s
     */
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

    /**
     * takes the _apitoken parameter, and check if its value is a registered token.
     *
     * @return bool
     */
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