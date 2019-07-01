<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 10.06.2019
 * Time: 20:12
 */

namespace tcms;

use tcms\commands\Command;

class Render
{
    public static function getCommand(Token $token, $context) {
        $sName = '\tcms\commands\Command'.$token->getName();
        if (class_exists($sName)) {
            return new $sName($token,$context);
        }
        return false;
    }

    public static function render(Token $token, $context, $bRenderSelf=true) {
        $sContent = "";

        if ($bRenderSelf) {
            $command = self::getCommand($token, $context);
            if ($command instanceof Command) $sContent .= $command->render();
        } else {
            // do not render self, but do use its content to fill up the placeholders:
            $sContent .= $token->getContent();
        }

        while($token->hasNextToken()) {
            $tokenNext = $token->getNextToken();
            $sSubContent=self::render($tokenNext,$context);
            // replace placeholder with rendered subtoken:
            $sContent = str_replace("{{".$tokenNext->getId()."}}",$sSubContent,$sContent);
        }

        return $sContent;
    }
}