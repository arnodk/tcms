<?php
namespace tcms;

use tcms\tools\Tools;

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

    private function init() {
        $this->sUser = "";
        $this->sHash = "";
        $this->aGroups = array();
    }

    private function populateFromJson($sJson) {
        $this->init();
        $a = json_decode($sJson,true);

        if (!empty($a['user'])) {
            $this->sUser=$a['user'];
        }
        if (!empty($a['hash'])) {
            $this->sHash=$a['hash'];
        }
        if (!empty($a['groups']) && is_array($a['groups'])) $this->aGroups = $a['groups'];
    }

    public function loadForUser($s) {
        $fs = new FileSystem($this->context);
        $sUserJson=$fs->load('user',$s);
        if (!empty($sUserJson)) {
            $this->populateFromJson($sUserJson);
        }
    }

    public function pw($s) {
        if (empty($this->sHash)) return false;

        if (hash('sha512',$this->context->config->getStaticSalt()."||".$s) === $this->sHash) return true;
        return false;
    }

    public function attachToSession() {
        @session_start();
        // this effectively makes the current user to be considered "logged in".
        $_SESSION['tcms_login'] = serialize($this);
    }
}