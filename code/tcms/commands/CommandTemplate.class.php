<?php
namespace tcms\commands;

/**
 * Class CommandTemplate
 * @package tcms\commands
 *
 * set the template for this page.
 */
class CommandTemplate extends Command
{
    public function render() {
        $this->context->vars->set("page:template",$this->token->getArg(0));
        return "";
    }
}