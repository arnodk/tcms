<?php
namespace tcms;

use tcms\tools\Tools;

/**
 * Class Login
 * @package tcms
 *
 * wraps around user info, and can be attached to a session, to represent a logged-in user.
 *
 */
class Login {
    /**
     * @var null|Context
     */
    private $context = null;
    private $sUser = "";
    private $sHash = "";
    private $aGroups = array();

    public function __construct(Context $c)
    {
        $this->context = $c;
    }

    public static function hasGroup($s)
    {
        @session_start();

        // get user from session, if present:
        if (empty($_SESSION['tcms_login'])) return false; // not logged in, we are done.

        $login = unserialize($_SESSION['tcms_login']);
        if (!($login instanceof Login)) return false; // unexpected content of tcms_login.

        return $login->inGroups($s);
    }

    public function inGroups($s) {
        $s=trim(strtolower($s));
        return in_array($s,$this->aGroups);
    }

    private function init() {
        $this->sUser = "";
        $this->sHash = "";
        $this->aGroups = array();
    }

    private function populateFromJson($sJson) {
        $this->init();
        $a = json_decode($sJson,true);
        if (is_null($a)) return false;
        if (!is_array($a)) return false;

        if (!empty($a['user'])) {
            $this->sUser=$a['user'];
        }
        if (!empty($a['hash'])) {
            $this->sHash=$a['hash'];
        }
        if (!empty($a['groups'])) $this->aGroups = explode(",",$a['groups']);

        return true;
    }

    public function loadForUser($s) {
        $fs = new FileSystem($this->context);
        $sUserJson=$fs->load('user',$s);
        if (!empty($sUserJson)) {
            return $this->populateFromJson($sUserJson);
        }
        return false;
    }

    public function pw($s) {
        if (empty($this->sHash)) return false;

        // todo: use dynamic salts, not a static one:
        if (hash('sha512',$this->context->config->getStaticSalt()."||".$s) === $this->sHash) return true;
        return false;
    }

    public function attachToSession() {
        @session_start();
        // this effectively makes the current user to be considered "logged in".
        $_SESSION['tcms_login'] = serialize($this);
    }
}