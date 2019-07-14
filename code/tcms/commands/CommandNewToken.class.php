<?php
namespace tcms\commands;

use tcms\FileSystem;
use tcms\Page;
use tcms\Router;
use tcms\tools\Tools;
use tcms\VerifyToken;

/**
 * Class CommandNewToken
 * @package tcms\commands
 *
 * generate a new verify-token to be used in, e.g., api calls.
 */
class CommandNewToken extends Command
{

    public function render() {
        $verifyToken = new VerifyToken();

        // use json encode for javascript escaping:
        return json_encode($verifyToken->new());
    }
}