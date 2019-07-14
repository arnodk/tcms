<?php
namespace tcms\commands;
use tcms\Context;
use tcms\Label;
use tcms\Token;

/**
 * Class Command
 * @package tcms\commands
 *
 * base class for all commands, contains the token for this command, and the context holding e.g. the active configuration.
 */
class Command
{
    protected $token = false;
    protected $context = false;

    public function __construct(Token $token, Context $context)
    {
        $this->token = $token;
        $this->context = $context;
    }
}