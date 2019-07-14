<?php
namespace tcms;

use tcms\tools\Tools;

/**
 * Class Parser
 *
 * wraps around the token class, for a more practical interface with it.
 *
 * @package tcms
 */
class Parser {

    public static function parse($sInput) {

        // wrap input around a virtual "out" token
        $token = new Token("out","",$sInput);

        // and parse:
        $token->parse();

        // token will now have the content and the complete token tree for this input, so return it
        return $token;
    }


}

