<?php
namespace tcms\controllers;

use tcms\Asset;
use tcms\FileSystem;
use tcms\Login;
use tcms\Page;
use tcms\tools\PageFilter;
use tcms\tools\Tools;
use tcms\VerifyToken;

class ControllerAsset extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction="list") {
        if (empty($sAction)) $sAction="list";
        switch($sAction) {
            case "list":
                $page = intval(Tools::jsonPostKey('page',1));
                $this->output->json($this->list($page));
                break;
            case "delete":
                $this->output->json($this->delete());
                break;
            case "upload":
                $this->output->json($this->upload());
                break;

        }
    }

    private function delete() {
        $aResult = array();
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $aParam = Tools::jsonPost();
            $sAsset = $aParam['asset'];
            if (!empty($sAsset)) {
                $asset = new Asset($this->context);
                $asset->setName($sAsset);
                if ($asset->delete()) {
                    $aResult['status'] = "OK";
                }
            }
        }
        return $aResult;
    }

    private function form() {
        $sResult = "";
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $fs = new FileSystem($this->context);
            $sResult = $fs->load('block_admin', "asset-upload-form");
        }
        return $sResult;
    }

    private function list($iPage=1) {
        $a = array();

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $sForm = $this->form();
            if (!empty($sForm)) {
                $a['form'] = $sForm;
            }

            $asset = new Asset($this->context);

            $pf = new PageFilter();
            $pf->setData($asset->list());
            $pf->setPage($iPage);
            $a['list_data'] = $pf->dataForPage();

            // tell caller everything turned out well:
            $a['status'] = "OK";
        } else {
            $a['status'] = "FAILED";
        }

        return $a;
    }

    private function upload() {
        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {
            $payload = file_get_contents('php://input');
            $sName = Tools::request('name','filename','');
            $iStart = intval(Tools::request('start','num','0'));

            $fs = new FileSystem($this->context);
            $bFileExists = $fs->bExists('asset',$sName);

            // start of new upload, but file already exists:
            if ($bFileExists && $iStart === 0) throw new \Exception("File already exists.");

            if (!$bFileExists) {
                $this->context->log->add("saving chunk");
                if (!$fs->save('asset', $sName, $payload)) throw new \Exception("Could not save file.");
            } else {
                $this->context->log->add("appending chunk");
                if (!$fs->append('asset', $sName, $payload)) throw new \Exception("Could not append to file.");
            }
        }
    }
}