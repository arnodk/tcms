<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 16.06.2019
 * Time: 21:42
 */
namespace tcms\commands;

class CommandHeader extends Command
{
    public function render() {
        return "<h1>".htmlspecialchars($this->lbl->getContent())."</h1>";
    }
}