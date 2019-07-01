<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

use tcms\Render;

class CommandRenderSection extends Command
{
    public function render() {
        // set page:section variable with rendered content of this command:
        return $this->context->vars->getValue("page:section:".$this->token->getArg(0),"");
    }
}