<?php
namespace tcms;

/**
 * Class Output
 * @package tcms
 *
 * layer responsible for pushing output to the browser
 * in its most simple form, it just echos the content, but the idea is to extend this class with
 * some statistic collection, logging, etc, about what actually is pushed out to the user.
 */
class Output
{
    public function push($sContent) {
        echo $sContent;
    }

    public function json($a) {
        $a = json_encode($a);
        if (!empty($a)) $this->push($a);
    }
}