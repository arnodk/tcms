<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 19:03
 */
namespace tcms;

use tcms\tools\Tools;
use tcms\tools\Variables;

class Controller
{
    private $config = false;
    private $router = false;
    private $fs = false;
    private $parser = false;
    private $page = false;
    private $output = false;
    private $context = false;

    public function __construct()
    {
        $this->context = new Context();
        $this->context->config = new Config();
        $this->context->vars = new Variables();

        $this->router = new Router();
        $this->parser = new Parser();
        $this->output = new Output();

        $this->fs = new FileSystem($this->context);
        $this->page = new Page($this->context);

    }

    public function run($sAction="view") {
        $sToOutput = "";

        switch($sAction) {
            case "view":
                $sToOutput.=$this->view();
                break;
        }

        if (!empty($sToOutput)) {
            $this->output->push($sToOutput);
        }
    }

    private function view() {
        // which page are we on?
        $sPage = $this->router->determinePage();

        $this->page->load($sPage);

        return $this->page->run();
    }
}