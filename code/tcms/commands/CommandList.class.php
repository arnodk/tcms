<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

class CommandList extends Command
{
    public function render() {
        return "<ul>".$this->token->getContent()."</ul>";
    }
}