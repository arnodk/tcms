<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

class CommandItem extends Command
{
    public function render() {
        return "<li>".$this->token->getContent()."</li>";
    }
}