<?php
namespace tcms\commands;

use tcms\Render;

/**
 * Class CommandRenderSection
 * @package tcms\commands
 *
 * render the contents of the section referenced to in the token.
 */
class CommandRenderSection extends Command
{
    public function render() {
        // set page:section variable with rendered content of this command:
        return $this->context->vars->getValue("page:section:".$this->token->getArg(0),"");
    }
}