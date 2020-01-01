<?php
namespace tcms;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Config {
    public const FRIENDLY_URL_MODE_PARAM    = 1;
    public const FRIENDLY_URL_MODE_SEO      = 2;

    public const DEBUG_LEVEL_PROD           = 0;
    public const DEBUG_LEVEL_QA             = 1;
    public const DEBUG_LEVEL_DEV            = 3;

    private $aConfig = array();

    public function __construct()
    {
        $this->loadConfigFile();
    }

    public function getBaseFileSystemPath() {
        return __DIR__ . "/../..";
    }

    public function getBaseURL() {
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $sProtocol = (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https://' : 'http://') ;
            $iPort = intval($_SERVER['SERVER_PORT']);
            $sPort=(($sProtocol==="https://" && $iPort != 443) || ($sProtocol==="http://" && $iPort != 80))?(":".strval($iPort)):"";

            return $sProtocol. $_SERVER['SERVER_NAME']. $sPort . dirname($_SERVER['SCRIPT_NAME']);
        } else {
            // cli call:
            return $this->getFromSetup('general','cli-host');
        }
    }

    public function getURLMode() {
        return self::FRIENDLY_URL_MODE_PARAM;
    }

    public function getDebugLevel() {
        return self::DEBUG_LEVEL_DEV;
    }

    public function getStaticSalt() {
        // TODO: use dynamic salts
        return $this->getFromSetup('general','salt');
    }

    public function getTestUser() {
        return $this->getFromSetup('tests','dashboard-test-login');
    }

    public function getTestPassword() {
        return $this->getFromSetup('tests','dashboard-test-password');
    }

    public function getFromSetup($sCategory, $sItem) {
        if (isset($this->aConfig[$sCategory]) && isset($this->aConfig[$sCategory][$sItem])) return $this->aConfig[$sCategory][$sItem];
        return false;
    }

    public function loadConfigFile() {
        // we can't use the filesystem class here, as it needs a fully booted up instance of a config class itself.
        // so, do this the old way:
        $sContent = file_get_contents(__DIR__."/../../config/setup.yaml");
        try {
            if (empty($sContent)) throw new \Exception('empty or invalid setup file');
            $this->aConfig = Yaml::parse($sContent);
        } catch (\Exception $e) {
            // TODO: an error handle more reasonable than just nope-ing out.
            echo "Setup could not be parsed.";
            die();
        }
    }

    public function getAllowedGroups() {
        $result = $this->getFromSetup('general','usergroups');
        if (empty($result))  {
            $result = [];
        } else {
            $result = explode(",", $result);
        }

        return $result;
    }
}