<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 19:21
 */

namespace tcms;

use tcms\tools\Tools;

class FileSystem
{
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function sanitize($s) {
        $s = preg_replace("([^\w\d\-_])", '', $s);
        $s = str_replace("..","",$s);
        return $s;
    }

    /**
     * @param $sCategory
     * @param $sFileName
     * @return bool|string
     */
    public function load($sCategory, $sFileName) {
        $sFileName = $this->sanitize($sFileName);
        $sCategory = strtolower($sCategory);
        $sContentDir = "";

        switch($sCategory) {
            case "page":
                    $sContentDir = "pages";
                    $sFileName.=".txt";
                    break;
            case "template":
                $sContentDir = "templates";
                $sFileName.=".txt";
                break;
        }

        $sFullPath = $this->context->config->getBaseFileSystemPath()."/".$sContentDir."/".$sFileName;

        return file_get_contents($sFullPath);
    }
}