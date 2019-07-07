<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 19:03
 */
namespace tcms\controllers;

use tcms;
use tcms\Config;
use tcms\Context;
use tcms\FileSystem;
use tcms\Log;
use tcms\Output;
use tcms\Page;
use tcms\Parser;
use tcms\Router;
use tcms\Variables;

class Controller
{
    protected $config = false;
    protected $router = false;
    protected $fs = false;
    protected $parser = false;
    protected $output = false;
    protected $context = false;

    public function __construct()
    {
        $this->context = new Context();

        $this->router = new Router($this->context);
        $this->parser = new Parser();
        $this->output = new Output();

        $this->fs = new FileSystem($this->context);
    }
}