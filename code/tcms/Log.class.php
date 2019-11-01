<?php
namespace tcms;


class Log
{
    public const TYPE_INFO      = 0;
    public const TYPE_WARNING   = 1;
    public const TYPE_ERROR     = 2;

    private $context = NULL;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function add($sMessage,$iType=self::TYPE_INFO) {
        // escape tab symbols from message, to facilitate parsing of log file:
        $sMessage = str_replace("\t","[tab]",$sMessage);

        $sPutToFile = date('Y-m-d H:i:s')."\t\t";

        $sRemoteAddr = str_replace("\t","[tab]",(!empty($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:"[unknown]");

        switch ($iType) {
            case self::TYPE_INFO:
                $sPutToFile.="[INFO]";
                break;
            case self::TYPE_WARNING:
                $sPutToFile.="[WARNING]";
                break;
            case self::TYPE_ERROR:
                $sPutToFile.="[WARNING]";
                break;
            default:
                $sPutToFile.="[UNKNOWN]";
                break;
        }

        $sPutToFile.="\t\t".$sRemoteAddr."\t\t".$sMessage."\n";

        $fs = new FileSystem($this->context);
        return $fs->append("log","log",$sPutToFile);
    }

    public function list($bReverseOrder=true) {
        $fs = new FileSystem($this->context);
        $aResult = array();
        $aLines = explode("\n",$fs->load("log","log"));

        if ($bReverseOrder) $aLines = array_reverse($aLines);

        foreach($aLines as $sLine) {
            if (trim($sLine) != "") {
                // parse line.
                $aParts = explode("\t\t",$sLine);

                $sDateTime = trim((!empty($aParts[0])) ? $aParts[0] : '');
                $sType = trim((!empty($aParts[1])) ? $aParts[1] : '');
                $sAddr = htmlspecialchars(trim((!empty($aParts[2])) ? $aParts[2] : ''), ENT_QUOTES);
                $sMessage = htmlspecialchars(trim((!empty($aParts[3])) ? $aParts[3] : ''), ENT_QUOTES);

                // assume we are not listing admin pages:
                $aResult[] = [
                    "datetime" => $sDateTime,
                    "type" => $sType,
                    "addr" => $sAddr,
                    "message" => $sMessage
                ];
            }
        }

        return $aResult;
    }
}