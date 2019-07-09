<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 05.07.2019
 * Time: 13:12
 */
namespace tcms\controllers;

use tcms\Log;
use tcms\Login;
use tcms\Page;
use tcms\Router;
use tcms\tools\Tools;

class ControllerDashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run($sAction="") {
        if (!Login::hasGroup("admin")) {
            $this->context->log->add('non-admin user called dashboard',Log::TYPE_WARNING);
            $router = new Router($this->context);
            $router->redirect('login');
            return "";
        }

        switch ($sAction) {
            default:
                $page = new Page($this->context, Page::PAGE_ADMIN);
                $page->load("dashboard");
                $sToOutput = $page->run();
                if (!empty($sToOutput)) {
                    $this->output->push($sToOutput);
                }
        }
    }
}