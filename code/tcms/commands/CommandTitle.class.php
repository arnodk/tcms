<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

class CommandTitle extends Command
{
    public function render() {
        $this->context->vars->set("page:title",$this->token->getArg(0));
        return "";
    }
}