<?php

use PHPUnit\Framework\TestCase;
use tcms\Context;
use tcms\Login;
use tcms\Startup;

class LoginTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        require_once("../Startup.class.php");

        Startup::load();

        parent::__construct($name, $data, $dataName);
    }

    public function testLogin() {
        $context = new Context();
        $login = new Login($context);
        $this->assertEquals(true,$login->loadForUser('root'), 'expected root user, but no root user found');
        $this->assertEquals(true,$login->inGroups("admin"), 'expected root to be assigned to admin group');
    }
}