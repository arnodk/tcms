<?php
namespace tcms;

use tcms\tools\Tools;

/**
 * Class Parser
 * convert a text ala
 * "
 * hallo
 * daar
 * [xyz]
 * lorem ipsum
 *
 * [text]
 * ipsum [link:ref]caption[/link] lorem
 *
 * [section:pq]
 * lorem ipsum
 * "
 *
 * into an array:
 * [0] =>
 *      "label" => default
 *      "content" => "hallo daar"
 * [1] =>
 *      "label" => xyz
 *      "content" => "lorem ipsum"
 * [2] =>
 *      "label"   => "text"
 *      "content" => "ipsum {{4}} lorem"
 * [3]
 *      "label" => "section"
 *      "args"  => "pq"
 *      "content" => "lorem ipsum"
 * [4]
 *     "label" => "link"
 *     "args"  => "ref"
 *     "content" => "caption"
 *     "called-from" => 2
 *
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

