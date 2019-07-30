<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 30.07.2019
 * Time: 11:38
 */

use PHPUnit\Framework\TestCase;

class StartupTest extends TestCase
{
    public function testRequires() {
        require_once("../Startup.class.php");

        // check if all required classes are loaded:
        $this->assertTrue(class_exists('\tcms\Config'));
        $this->assertTrue(class_exists('\tcms\Context'));
        $this->assertTrue(class_exists('\tcms\FileSystem'));
        $this->assertTrue(class_exists('\tcms\Log'));
        $this->assertTrue(class_exists('\tcms\Login'));
        $this->assertTrue(class_exists('\tcms\Output'));
        $this->assertTrue(class_exists('\tcms\Page'));
        $this->assertTrue(class_exists('\tcms\Render'));
        $this->assertTrue(class_exists('\tcms\Router'));
        $this->assertTrue(class_exists('\tcms\Startup'));
        $this->assertTrue(class_exists('\tcms\Template'));
        $this->assertTrue(class_exists('\tcms\VerifyToken'));
    }
}