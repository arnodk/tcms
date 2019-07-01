<?php
namespace tcms;

class Config {
    public const FRIENDLY_URL_MODE_PARAM    = 1;
    public const FRIENDLY_URL_MODE_SEO      = 2;

    public const DEBUG_LEVEL_PROD           = 0;
    public const DEBUG_LEVEL_QA             = 1;
    public const DEBUG_LEVEL_DEV            = 3;

    public function getBaseFileSystemPath() {
        return __DIR__ . "/../..";
    }

    public function getBaseURL() {
        return ".";
    }

    public function getURLMode() {
        return self::FRIENDLY_URL_MODE_PARAM;
    }

    public function getDebugLevel() {
        return self::DEBUG_LEVEL_DEV;
    }
}