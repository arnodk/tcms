<?php
namespace tcms;

// load up necessities:
require_once("Config.class.php");
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
require_once("Render.class.php");
require_once("Output.class.php");
require_once("Controller.class.php");


class Startup {
    public static function boot() {

        // include all command classes in command dir, but perhaps, it is better to include a command
        // on demand in the renderer?
        foreach (glob(__DIR__."/commands/Command*.class.php") as $sFileName)
        {
            include_once $sFileName;
        }

        // basically initiate a controller, and let it go at it:
        $controller = new Controller();

        // todo: perhaps transer here some server, get, post and cookie parameters as context?
        $controller->run();
    }
}