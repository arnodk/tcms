<?php
namespace tcms;

// load up necessities:
use tcms\controllers\Controller;
use tcms\tools\Tools;


class Startup {
    public static function load() {
        require __DIR__ . '/../../vendor/autoload.php';
        require_once("Output.class.php");
        require_once("Config.class.php");
        require_once("VerifyToken.class.php");
        require_once("tools/Tools.class.php");
        require_once("Context.class.php");
        require_once("Log.class.php");
        require_once("FileSystem.class.php");
        require_once("Router.class.php");
        require_once("Token.class.php");
        require_once("Variables.class.php");
        require_once("Parser.class.php");
        require_once("Template.class.php");
        require_once("Page.class.php");
        require_once("Block.class.php");
        require_once("Login.class.php");
        require_once("Render.class.php");
        require_once("Output.class.php");
        require_once("Asset.class.php");

        // include all command classes in command dir, but perhaps, it is better to include a command
        // on demand in the renderer?
        foreach (glob(__DIR__."/commands/Command*.class.php") as $sFileName)
        {
            include_once $sFileName;
        }

        // include all controller classes in controller dir, but perhaps, it is better to include a command
        // on demand in the router?
        foreach (glob(__DIR__."/controllers/Controller*.class.php") as $sFileName)
        {
            include_once $sFileName;
        }
    }

    public static function boot() {

        // basically initiate a controller, and let it go at it:
        $controller = Router::getController();

        // todo: perhaps transfer here some server, get, post and cookie parameters as context?
        if ($controller instanceof Controller) $controller->run(Tools::get('action','string',''));
    }
}