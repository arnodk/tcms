<?php
namespace tcms\controllers;

use tcms\Log;
use tcms\Login;
use tcms\tools\PageFilter;
use tcms\tools\Tools;
use tcms\VerifyToken;

/**
 * Class ControllerLog
 * responsible for displaying log files.
 *
 * @package tcms\controllers
 */
class ControllerLog extends Controller
{
    const LOG_PAGE_SIZE = 100;

    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction="",$aOptions = false) {
        switch($sAction) {
            case "list":
                $page = intval(Tools::jsonPostKey('page',1));
                $this->output->json($this->list($page));
                break;
        }
    }



    /**
     * return an array of all log lines
     *
     * @return array
     */
    private function list($iPage = 1) {
        $aResult = array();

        if (VerifyToken::apiTokenCheck() && Login::hasGroup("admin")) {

            $log = new Log($this->context);

            $pf = new PageFilter(['page_size'=>self::LOG_PAGE_SIZE]);
            $pf->setData($log->list());
            $pf->setPage($iPage);

            $aResult['list_data'] = $pf->dataForPage();

            // tell caller everything turned out well:
            $aResult['status'] = "OK";
        } else {
            $aResult['status'] = "FAILED";
        }

        return $aResult;
    }
}