<?php
namespace tcms\commands;

use tcms\Render;

/**
 * Class CommandSection
 * @package tcms\commands
 *
 * renders the content between the start and end section tag, and store it in a context bound variable.
 *
 */
class CommandSection extends Command
{
    public function render() {
        // set page:section variable with rendered content of this command:
        $this->context->vars->set("page:section:".$this->token->getArg(0), Render::render($this->token,$this->context,false));
        return "";
    }
}