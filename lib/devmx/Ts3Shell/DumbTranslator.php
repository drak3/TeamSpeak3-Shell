<?php
namespace devmx\Ts3Shell;
use devmx\Teamspeak3\Query\Command;

/**
 *
 * @author drak3
 */
class DumbTranslator implements \devmx\Teamspeak3\Query\Transport\CommandTranslatorInterface
{
    public function isValid( Command $cmd )
    {
        return strstr($cmd->getName(),"\n") === FALSE;
    }

    public function translate( Command $cmd )
    {
        return $cmd->getName()."\n";
    }

}

?>
