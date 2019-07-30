<?php
namespace tcms;

class Context
{
    /**
     * @var Config
     */
    public $config = NULL;

    /**
     * @var Variables
     */
    public $vars = NULL;

    /**
     * @var Log
     */
    public $log = NULL;

    public function __construct()
    {
        $this->config = new Config();
        $this->vars = new Variables();
        $this->log = new Log($this);    // caution: circular reference...
    }
}