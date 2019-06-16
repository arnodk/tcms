<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 20:12
 */

namespace tcms;


class Render
{
    private $aLabels = array();
    private $aSections = array();

    private $sResult = "";
    /**
     * @var Page
     */
    private $page = NULL;

    public function setInput($aLabels) {
        $this->aLabels = $aLabels;
    }

    public function run() {
        foreach($this->aLabels as $lbl) {
            if ($lbl instanceof Label) {

            }
        }
    }

    public function getErrors() {

    }

    public function getResult() {
        return $this->sResult;
    }
}