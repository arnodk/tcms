<?php

use PHPUnit\Framework\TestCase;
use tcms\Log;
use tcms\Startup;

class StartupTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        require_once("../Startup.class.php");

        Startup::load();

        parent::__construct($name, $data, $dataName);
    }

    public function testRequires() {

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

    public function testLog() {
        $sLogMessage = "LOG TEST";
        $context = new \tcms\Context();
        $log = new Log($context);
        $this->assertGreaterThan(strlen($sLogMessage), intval($log->add($sLogMessage,Log::TYPE_INFO)));
        $fs = new \tcms\FileSystem($context);
        $sLog = $fs->load("log","log");
        $aLines = explode("\n",$sLog);
        // we end a log line with "\n", which implies that the last element of the array is empty...
        // so, for the last log line, we need to take the 2nd last:
        $sLastLine = $aLines[count($aLines) - 2];
        $sLastLine = trim($sLastLine);
        $this->assertEquals($sLogMessage,substr($sLastLine,strlen($sLastLine) - strlen($sLogMessage)));
    }
}