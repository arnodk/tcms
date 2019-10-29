<?php
namespace tcms;

use tcms\tools\Tools;

/**
 * Class FileSystem
 * @package tcms
 *
 * handles all interactions of tcms with its persistence layer,
 * for the moment it only deals with a filesystem, but the idea is to
 * extend this to other storage methods as well, at which point this class will be refactored
 * as a layer between tcms and the actually storage system.
 *
 */
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
            case "config":
                $sContentDir = "/config";
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
            case "config":
                $sExtension="yaml";
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

    private function getFullPath($sCategory,$sFileName,$bAddExtension=true) {
        // sanity check:
        if (empty($sCategory) || empty($sFileName)) return "";

        $sFileName = $this->sanitize($sFileName);
        $sCategory = strtolower($sCategory);

        $sContentDir = $this->getCategoryDir($sCategory);
        if ($bAddExtension) $sFileName = $this->addFileNameExtension($sFileName,$sCategory);

        // recheck, if an error occured, these might be set to an empty string
        if (empty($sContentDir) || empty($sFileName)) return "";

        return $sContentDir . "/" . $sFileName;
    }

    /**
     * @param $sCategory
     * @param $sFileName
     * @return bool|string
     */
    public function load($sCategory, $sFileName, $bAddExtenstion=true) {

        $sFullPath = $this->getFullPath($sCategory,$sFileName,$bAddExtenstion);
        if (empty($sFullPath)) return "";

        if (!file_exists($sFullPath)) {
            $this->context->log->add("Could not load file [".$sFullPath."] for category [".$sCategory."] and file name [".$sFileName."]",Log::TYPE_ERROR);
            return "";
        }

        return file_get_contents($sFullPath);
    }

    public function save($sCategory, $sName, $sData) {
        $sFullPath = $this->getFullPath($sCategory,$sName);
        if (empty($sFullPath)) {
            $this->context->log->add("Could not save file [".$sName."] for category [".$sCategory."], because its path could not be determined",Log::TYPE_ERROR);
            return false;
        }

        return file_put_contents($sFullPath,$sData);
    }

    public function append($sCategory,$sFileName,$sData) {
        $sFullPath = $this->getFullPath($sCategory,$sFileName);
        if (empty($sFullPath)) {
            $this->context->log->add("Could not append file [".$sFileName."] for category [".$sCategory."], because its path could not be determined",Log::TYPE_ERROR);
            return false;
        }

        return file_put_contents($sFullPath,$sData,FILE_APPEND);
    }

    public function delete($sCategory, $sName) {
        $sFullPath = $this->getFullPath($sCategory,$sName);
        if (empty($sFullPath)) {
            $this->context->log->add("Could not delete file [".$sName."] for category [".$sCategory."], because its path could not be determined",Log::TYPE_ERROR);
            return false;
        }

        if (!file_exists($sFullPath)) {
            $this->context->log->add("Could not delete file [".$sFullPath."] for category [".$sCategory."] and file name [".$sName."], because the file did not exist.",Log::TYPE_ERROR);
            return false;
        }

        return unlink($sFullPath);
    }

    public function bExists($sCategory,$sFileName) {

        $sFullPath = $this->getFullPath($sCategory,$sFileName);

        $this->context->log->add("Looking for: ".$sFullPath);

        return file_exists($sFullPath);
    }

    public function list($sCategory,$bRemoveExtension=true) {
        $sDir = $this->getCategoryDir($sCategory);
        $a = array();

        foreach (glob($sDir."/*") as $sFilename) {
            if ($sFilename!="." && $sFilename!="..") {
                $sFilename = substr($sFilename,strrpos($sFilename, "/") + 1);
                if ($bRemoveExtension && strpos($sFilename,".")!==false) $sFilename = substr($sFilename,0,strpos($sFilename, "."));
                $a[] = $sFilename;
            }
        }

        return $a;
    }

    public function captionFileSize($sCategory, $sFileName)
    {
        $sResult = "0";
        $a=['B','Kb','Mb','Gb','Tb','Pb'];
        $sFullPath = $this->getFullPath($sCategory,$sFileName);

        if (!empty($sFullPath)) {
            $iBytes = filesize($sFullPath);
            if (!empty($iBytes)) {
                $iO = floor(log($iBytes,1024));
                if ($iO < count($a)) {
                    $sResult = strval(round($iBytes / pow(1024,$iO),2) . $a[$iO]);
                }
            }
        }

        return $sResult;

    }
}