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

class CommandBlock extends Command
{
    private $sContent = "";

    private function loadData() {
        $fs = new FileSystem($this->context);
        $sFileName = $this->lbl->getArg(0);
        $this->sContent = $fs->load('block',$sFileName);
    }

    public function render() {
        // block content is allowed to have commands too,
        // render these:
        $this->loadData();

        $parser = new Parser();
        $aLabels = $parser->parse($this->sContent);
        $page = new Page($this->context);
        $page->setInput($aLabels);
        $page->setTemplateName('block');

        return $page->run();
    }
}