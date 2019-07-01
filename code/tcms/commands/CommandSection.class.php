<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

use tcms\Render;

class CommandSection extends Command
{
    public function render() {
        // set page:section variable with rendered content of this command:
        $this->context->vars->set("page:section:".$this->token->getArg(0), Render::render($this->token,$this->context,false));
        return "";
    }
}