<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 20:59
 */

namespace tcms;

class Context
{
    /**
     * @var Config
     */
    public $config = NULL;

    // variables for renderer:
    /**
     * @var Variables
     */
    public $vars = NULL;

    /**
     * @var Log
     */
    public $log = NULL;
}