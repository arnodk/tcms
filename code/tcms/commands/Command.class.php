<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;
use tcms\Context;
use tcms\Label;
use tcms\Token;

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