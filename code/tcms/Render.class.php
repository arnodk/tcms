<?php


namespace tcms;

use tcms\commands\Command;

/**
 * Class Render
 * @package tcms
 *
 * traverse a token tree, and match found tokens with commands.
 */
class Render
{
    /**
     * takes a token, retrieves its name, and tries to locate matching command class.
     * if none was found, false is returned, otherwise the command class is returned.
     *
     * @param Token $token
     * @param $context
     * @return bool | Token
     */
    public static function getCommand(Token $token, $context) {
        if (!empty($token->getName())) {
            $sName = '\tcms\commands\Command'.$token->getName();
            if (class_exists($sName)) {
                return new $sName($token,$context);
            }
        }
        return false;
    }

    /**
     * traverse token tree, and replace the token place holders with the result of executed commands.
     *
     * @param Token $token
     * @param $context
     * @param bool $bRenderSelf
     * @return mixed|string
     */
    public static function render(Token $token, $context, $bRenderSelf=true) {
        $sContent = "";

        if ($bRenderSelf) {
            $command = self::getCommand($token, $context);
            if ($command instanceof Command) {
                $sContent .= $command->render();
            } else {
                // not a command, give it back as is:
                $sContent .= "[".$token->getOriginalName()."]".$token->getContent();
                if ($token->hasClosingTag()) {
                    $sContent .= "{/".$token->getOriginalName()."]";
                }
            }
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