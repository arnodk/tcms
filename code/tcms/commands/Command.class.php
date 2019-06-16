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

class Command
{
    protected $lbl = false;
    protected $context = false;

    public function __construct(Label $lbl, Context $context)
    {
        $this->lbl = $lbl;
        $this->context = $context;
    }
}