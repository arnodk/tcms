<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

class CommandText extends Command
{
    public function render() {
        return "<p>".htmlspecialchars($this->lbl->getContent())."</p>";
    }
}