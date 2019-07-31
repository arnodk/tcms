<?php

use PHPUnit\Framework\TestCase;
use tcms\Context;
use tcms\Login;
use tcms\Startup;

use Symfony\Component\BrowserKit\Client as HttpClient;
use Symfony\Component\BrowserKit\Response;
use Goutte\Client as WebClient;

class DashboardTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        require_once("../Startup.class.php");

        Startup::load();

        parent::__construct($name, $data, $dataName);
    }

    public function testDashboard() {
        $context = new Context();
        $client = new WebClient();
        $crawler = $client->request('GET', $context->config->getBaseURL().'/index.php?system=dashboard');
        // pre-login:
        $sResult = $crawler->html();
        $this->assertStringContainsString("input type=\"password\"",$sResult, 'Expected redirect to login page, when loading dashboard for a non-logged-in user.');
        $iStartPosToken = strpos($sResult,"tcms.setToken(\"");
        $this->assertGreaterThan(0,$iStartPosToken, "Could not find token on login page");
        $iStartPosToken = $iStartPosToken + 15;
        $iEndPosToken = strpos($sResult,"\"",$iStartPosToken + 1) - $iStartPosToken;
        $this->assertGreaterThan(0,$iStartPosToken, "Could not find token end");

        $sToken = substr($sResult, $iStartPosToken , $iEndPosToken);

        // login api call, with incorrect credentials:
        $sLoginUrl = $context->config->getBaseURL().'/index.php?system=login&action=login&_apitoken='.$sToken;
        $crawler = $client->request(
            'POST',
            $sLoginUrl,
            array(),
            array(),
            array('HTTP_CONTENT_TYPE' => 'application/json'),
            json_encode(array("login"=>$context->config->getTestUser(),"password"=>"typo".$context->config->getTestPassword()))
        );
        $sResult = $crawler->html();
        $this->assertStringContainsString('{"status":"ERROR","reason":"invalid credentials"}',$sResult, 'Expected failed login.');

        // login api call, with correct credentials:
        $sLoginUrl = $context->config->getBaseURL().'/index.php?system=login&action=login&_apitoken='.$sToken;
        $crawler = $client->request(
            'POST',
            $sLoginUrl,
            array(),
            array(),
            array('HTTP_CONTENT_TYPE' => 'application/json'),
            json_encode(array("login"=>$context->config->getTestUser(),"password"=>$context->config->getTestPassword()))
        );
        $sResult = $crawler->html();
        $this->assertStringContainsString('{"status":"OK"}',$sResult, 'Expected successful login.');

        // post-login, should show dashboard page:
        $crawler = $client->request('GET', $context->config->getBaseURL().'/index.php?system=dashboard');
        $sResult = $crawler->html();
        $this->assertStringContainsString("dashboardFireEvent(this)",$sResult, 'No dashboard page after logging in, perhaps invalid credentials?');
    }
}