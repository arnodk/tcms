<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

use tcms\FileSystem;
use tcms\Page;
use tcms\Router;
use tcms\tools\Tools;
use tcms\VerifyToken;

class CommandNewToken extends Command
{

    public function render() {
        $verifyToken = new VerifyToken();

        // use json encode for javascript escaping:
        return json_encode($verifyToken->new());
    }
}