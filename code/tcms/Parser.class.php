<?php
namespace tcms;

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
 * ipsum lorem
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
 *      "content" => "ipsum lorem"
 * [3]
 *      "label" => "section"
 *      "args"  => "pq"
 *      "content" => "lorem ipsum"
 *
 *
 *
 * @package tcms
 */

class Parser {
    private $aParsed = array();

    /**
     * @var Label
     */
    private $lblCurrent = NULL;

    public function __construct()
    {
        // initialize default label, so we have something to put the first lines of content into,
        // if the input does not start with a label:
        $this->lblCurrent = new Label("default");
    }

    public function parse($sInput) {

        // divide input into lines, and process per line
        $aLines = explode("\n",$sInput);
        foreach($aLines as $sLine) {
            $this->parseLine($sLine);
        }

        // we are done, store what ever is still active:
        $this->aParsed[] = $this->lblCurrent;

        return $this->aParsed;
    }

    // check if this line is a label, or just content
    // if it turns out to be a label, store currently active label and configure the new label
    // if it this is just a content line, add it as content to the current label.
    private function parseLine($sLine) {
        // normalize:
        $sLineToParse = trim(strtolower($sLine));

        // match everything between a line that starts at "[" and a line that ends at "]".
        $aMatches = array();
        preg_match('/^\[([^\]]+)\]$/',$sLineToParse,$aMatches);
        if (count($aMatches) > 0 ) {
            // found a new label, initialize it
            // after storing the current one:
            $this->aParsed[] = $this->lblCurrent;
            $this->lblCurrent = $this->parseLabel($aMatches[1]);
        } else {
            // just content, add to current label:
            $this->lblCurrent->addLine($sLine);
        }
    }

    // retrieve label name and label arguments,
    // and return a label object load up with the parsed values.
    private function parseLabel($sLabel)
    {
        $a = explode(":",$sLabel);

        // first is label name:
        $sLabel = $a[0];

        // rest are arguments:
        array_splice($a,0,1);

        return new Label($sLabel,$a);
    }
}

