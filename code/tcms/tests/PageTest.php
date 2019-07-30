<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 30.07.2019
 * Time: 11:38
 */

use PHPUnit\Framework\TestCase;
use tcms\Log;

class PageTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        require_once("../Startup.class.php");
        \tcms\Startup::load();

        parent::__construct($name, $data, $dataName);
    }

    public function testHomepage() {

        $page = new \tcms\controllers\ControllerPage();
        ob_start();
        $page->run('view',['page'=>'start']);
        $sResult = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('<html>',$sResult);
        $this->assertStringContainsString('</html>',$sResult);
    }


}