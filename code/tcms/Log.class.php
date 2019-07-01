<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 01.07.2019
 * Time: 11:40
 */

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
        $sPutToFile = date('Y-m-d H:i:s')."\t";

        $sRemoteAddr = (!empty($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:"[unknown]";

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

        $sPutToFile.="\t".$sRemoteAddr."\t".$sMessage."\n";

        $fs = new FileSystem($this->context);
        $fs->append("log","log",$sPutToFile);
    }
}