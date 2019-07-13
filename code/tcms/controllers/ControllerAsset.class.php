<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 05.07.2019
 * Time: 13:12
 */
namespace tcms\controllers;

use tcms\FileSystem;
use tcms\Login;
use tcms\Page;
use tcms\tools\Tools;

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
                $this->output->json($this->form());
                break;
            case "upload":
                $this->output->json($this->upload());
                break;

        }
    }

    private function form() {
        $a = array();
        if (Login::hasGroup("admin")) {
            $fs = new FileSystem($this->context);
            $sFileName = "asset-upload-form";
            $sForm = $fs->load('block_admin', $sFileName);
            if (!empty($sForm)) {
                $a['status'] = 'OK';
                $a['form'] = $sForm;
            }
        }
        return $a;
    }

    private function upload() {
        if (Login::hasGroup("admin")) {
            $payload = file_get_contents('php://input');
            $fs = new FileSystem($this->context);
            if (!$fs->bExists('asset','mytest.jpg')) {
                $fs->save('asset', 'mytest.jpg', $payload);
            } else {
                $fs->append('asset', 'mytest.jpg', $payload);
            }
            $this->context->log->add("saving chunk");
        }
    }
}