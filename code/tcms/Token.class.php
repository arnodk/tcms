<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 12:46
 */

namespace tcms;


// helper class for storing labels:
use tcms\tools\Tools;

class Token {
    private $sName="";
    private $sOriginalName="";
    private $aArgs = array();
    private $aTokens = array(); // subtree, referred to in content with keys ala {{key}}
    private $sContent = "";
    private $sId = "";
    private $iTokenIndex = 0;
    private $bHasClosingTag = false;

    public function __construct($sName="",$aArgs="", $sContent="", $bHasClosingTag=false)
    {
        if (!empty($sName)) {
            $this->sName = strtolower($sName);
        }
        if (is_array($aArgs)) $this->aArgs=$aArgs;

        $this->sOriginalName = $sName;
        $this->bHasClosingTag = $bHasClosingTag;
        $this->sContent = $sContent;

        // get an id:
        $this->sId = bin2hex(random_bytes(12));
    }

    public function hasNextToken() {
        // transform id=>token to an array with numeric index:
        $a = array_values($this->aTokens);

        if (isset($a[$this->iTokenIndex]) && $a[$this->iTokenIndex] instanceof Token) return true;
    }

    public function getNextToken() {
        $a = array_values($this->aTokens);
        $token = $a[$this->iTokenIndex];
        $this->iTokenIndex++;
        return $token;
    }

    public function addToken(Token $t)
    {
        $this->aTokens[$t->getId()] = $t;
    }

    public function getName() {
        return $this->sName;
    }

    public function hasClosingTag() {
        return $this->bHasClosingTag;
    }

    public function getOriginalName() {
        return $this->sOriginalName;
    }

    public function getId() {
        return $this->sId;
    }

    public function getArg($iIndex, $mIfEmpty=false) {
        if (empty($this->aArgs[$iIndex])) return $mIfEmpty;

        return $this->aArgs[$iIndex];
    }

    public function getContent() {
        return $this->sContent;
    }

    public function getTokens() {
        return $this->aTokens;
    }

    static function normalizeLabel($sLabel) {
        $sLabel = strtolower($sLabel);
        $sLabel = str_replace("[","",$sLabel);
        $sLabel = str_replace("]","",$sLabel);

        return $sLabel;
    }

    // find position of closing label, taking into consideration potential recursive calls.
    // e.g. [a]def[a]ghi[/a]jkl[/a] -> first a should match last a.
    private function aFindClosingMatch($iStartPos,$sLabel,$sContent) {

        $sLabel = self::normalizeLabel($sLabel);
        $iDepth=0;

        $aMatches = array();

        // do we find a match beyond the start position (as to not match with previous matches)
        preg_match_all('/\[([^\]]+)\]/', $sContent, $aMatches, PREG_OFFSET_CAPTURE);
        if (empty($aMatches)) return array();
        $aMatches = $aMatches[0];
        foreach($aMatches as $aMatch) {
            if (!empty($aMatch) && is_array($aMatch) && count($aMatch) >= 2 && $aMatch[1] > $iStartPos) {

                $sCurrentLabel = self::normalizeLabel($aMatch[0]);
                $a = explode(":",$sCurrentLabel);
                $sCurrentLabel = $a[0];

                if ($sCurrentLabel === $sLabel) {
                    // start label, increase depth:
                    $iDepth++;
                } elseif ($sCurrentLabel === "/" . $sLabel) {
                    // found closing label,
                    // if depth = 0, there was no recursive call, or we have descended to the first level... i.e., we found the match:
                    if ($iDepth === 0) return $aMatch;
                    // apparently we are not done yet, decrease level
                    $iDepth--;
                    if ($iDepth < 0) throw new \Exception("Too many closing tags for label " . $sLabel . " found");
                }
            }
        }

        // no closing tag found, treat this as an individual label:
        return array();
    }

    /**
     * converts a string
     *
     * @param $sMatch
     * @return array
     */
    public static function aParseArgs($sMatch) {
        $sMatch = self::normalizeLabel($sMatch);
        $a = explode(":",$sMatch);
        // check for escaped delimiters:
        foreach($a as $i=>$sPart) {
            if (substr($sPart,strlen($sPart)-1)==='\\') {
                // escaped parameter, add current part next value, without escape sign
                $a[$i+1] = substr($sPart,0,-1).":".$a[$i+1];
                // remove this part
                array_splice($a,$i,1);
            }
        }

        return $a;
    }

    // search for [pq]text[/pq] type of labels, replace them with a placeholder {{x}}, and create a label for them.
    public function parse() {
        $aMatch = array();
        $i=0;
        while(preg_match('/\[([^\]]+)\]/', $this->sContent, $aMatch, PREG_OFFSET_CAPTURE)) {
            if (!empty($aMatch) && is_array($aMatch) && count($aMatch) >= 2) {
                $bClosingTagFound = false;

                $aMatch=$aMatch[0];
                $sMatch = $aMatch[0];

                $aTokenArgs = self::aParseArgs($sMatch);

                // first is label name:
                $sTokenName = $aTokenArgs[0];

                $iPosMatch = $aMatch[1] + strlen($sMatch);

                // find recursively sound matching close label and create a single label out of it:
                try {
                    $aPosClosingMatch = $this->aFindClosingMatch($iPosMatch, $sTokenName, $this->sContent);
                } catch (\Exception $e) {
                    echo "Parse failure: [" . $e->getMessage() . "]";
                    return false;
                }

                // create token with content from current start position to position of closing match
                if (count($aPosClosingMatch) > 0) {
                    $iClosingPos = $aPosClosingMatch[1] + strlen($aPosClosingMatch[0]);

                    $sContent = substr($this->sContent, $iPosMatch, $aPosClosingMatch[1] - $iPosMatch);
                    $bClosingTagFound = true;
                } else {
                    // no end tag found, so no content to load it up with.
                    $iClosingPos = $iPosMatch;
                    $sContent = "";
                }

                // rest are arguments:
                array_splice($aTokenArgs, 0, 1);

                // enough information collected to create the token:
                $token = new Token($sTokenName, $aTokenArgs, $sContent, $bClosingTagFound);

                // store token to current token's subtree:
                $this->addToken($token);

                // replace match with a placeholder referring to the newly created token:
                $this->sContent = substr($this->sContent, 0, $aMatch[1] ) . "{{" . $token->getId() . "}}" . substr($this->sContent, $iClosingPos );

                // recursively parse the content of the label:
               $token->parse();

                $i++;
            }

        }

        return true;
    }

    /**
     * do not parse text, just remove all tokens / labels:
     * @param $sText
     * @return null|string|string[]
     */
    public static function removeTokens($sText) {
        return preg_replace('/\[([^\]]+)\]/', '', $sText);
    }
}