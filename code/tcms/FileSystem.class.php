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
    private $context = NULL;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function sanitize($s) {
        $s = preg_replace("([^\w\d\-_\.])", '', $s);
        $s = str_replace("..","",$s);
        return $s;
    }

    private function getCategoryDir($sCategory) {
        $sContentDir="";

        switch($sCategory) {
            case "user":
                $sContentDir = "/content/users";
                break;
            case "page":
                $sContentDir = "/content/pages";
                break;
            case "page_admin":
                $sContentDir = "/code/tcms/admin/pages";
                break;
            case "template_admin":
                $sContentDir = "/code/tcms/admin/templates";
                break;
            case "block":
                $sContentDir = "/content/blocks";
                break;
            case "block_admin":
                $sContentDir = "/code/tcms/admin/blocks";
                break;
            case "template":
                $sContentDir = "/content/templates";
                break;
            case "asset":
                $sContentDir = "/public/assets";
                break;
            case "log":
                // logs is one dir up from content's perspective:
                $sContentDir = "/logs";
                break;
        }

        if (!empty($sContentDir)) $sContentDir = $this->context->config->getBaseFileSystemPath() . $sContentDir;

        return $sContentDir;
    }

    public function isExtensionAllowed($sExtension) {
        $sExtension = strtolower($sExtension);
        // filter out executables:
        return (!in_array($sExtension ,array("com","exe","sh","php","py","bat"))); // TODO: extend this list.
    }

    private function addFileNameExtension($sFileName, $sCategory) {
        $sExtension="";

        switch($sCategory) {
            case "page":
            case "page_admin":
            case "template_admin":
            case "block":
            case "block_admin":
            case "template":
            case "log":
            case "user":
                $sExtension="txt";
                break;
            case "asset":
                // check for allowed extension on the filename itself:
                $sExtension = Tools::getExtensionFromFileName($sFileName);
                if (!empty($sExtension)) $sFileName=substr($sFileName,0,strlen($sFileName) - strlen($sExtension) - 1);
                if (!$this->isExtensionAllowed($sExtension)) $sExtension = "";
                break;
        }

        // only return filenames for known extensions:
        if (empty($sExtension)) return "";

        return $sFileName.".".$sExtension;
    }

    private function getFullPath($sCategory,$sFileName) {
        // sanity check:
        if (empty($sCategory) || empty($sFileName)) return "";

        $sFileName = $this->sanitize($sFileName);
        $sCategory = strtolower($sCategory);

        $sContentDir = $this->getCategoryDir($sCategory);
        $sFileName = $this->addFileNameExtension($sFileName,$sCategory);

        // recheck, if an error occured, these might be set to an empty string
        if (empty($sContentDir) || empty($sFileName)) return "";

        return $sContentDir . "/" . $sFileName;
    }

    /**
     * @param $sCategory
     * @param $sFileName
     * @return bool|string
     */
    public function load($sCategory, $sFileName) {

        $sFullPath = $this->getFullPath($sCategory,$sFileName);

        if (!file_exists($sFullPath)) {
            $this->context->log->add("Could not load file [".$sFullPath."} for category [".$sCategory."] and file name [".$sFileName."]",Log::TYPE_ERROR);
            return "";
        }

        return file_get_contents($sFullPath);
    }

    public function append($sCategory,$sFileName,$sData) {
        $sFullPath = $this->getFullPath($sCategory,$sFileName);

        return file_put_contents($sFullPath,$sData,FILE_APPEND);
    }

    public function bExists($sCategory,$sFileName) {

        $sFullPath = $this->getFullPath($sCategory,$sFileName);

        $this->context->log->add("Looking for: ".$sFullPath);

        return file_exists($sFullPath);
    }
}