<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

use tcms\FileSystem;
use tcms\Page;
use tcms\Parser;
use tcms\tools\Tools;

class CommandBlockAdmin extends Command
{
    private $sContent = "";

    private function loadData() {
        $fs = new FileSystem($this->context);
        $sFileName = $this->token->getArg(0);
        $this->sContent = $fs->load('block_admin',$sFileName);
    }

    public function render() {
        // block content is allowed to have commands too,
        // render these:
        $this->loadData();

        // wrap the content in a section, so that we can parse it:
        $token = Parser::parse('[section:block]'.$this->sContent.'[/section]');
        $page = new Page($this->context);
        $page->setToken($token);
        $page->setTemplateName('block');
        return $page->run();
    }
}