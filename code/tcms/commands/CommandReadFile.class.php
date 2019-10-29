<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

use tcms\FileSystem;
use tcms\Parser;
use tcms\Render;
use tcms\tools\Tools;

class CommandReadFile extends Command
{
    private $sContent = "";

    public function render() {
        if ($this->token->getArgCount() < 2) {
            $this->context->log->add("Missing argument for read file command");
            return "";
        }

        $sCategory = $this->token->getArg(0);
        if (empty($sCategory)) {
            $this->context->log->add("Empty category for read file command");
            return "";
        }
        $sFileName = $this->token->getArg(1);
        if (empty($sFileName)) {
            $this->context->log->add("Empty file names for read file command");
            return "";
        }

        $fs = new FileSystem($this->context);
        // wrap the content in a out command, so that we can parse it:
        $token = Parser::parse('[out]'.$fs->load($sCategory,$sFileName,false).'[/out]');
        return Render::render($token,$this->context);
    }
}