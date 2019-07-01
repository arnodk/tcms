<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

class CommandB extends Command
{
    public function render() {
        return "<b>".$this->token->getContent()."</b>";
    }
}