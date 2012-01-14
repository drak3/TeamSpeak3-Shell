<?php
namespace devmx\Ts3Shell\CommandHandler;

/**
 *
 * @author drak3
 */
interface CommandHandlerInterface
{
    public function canHandle($name);
    /**
     *@return devmx\Ts3Shell\CommandResponse
     */
    public function handle( \devmx\Ts3Shell\CommandCall $call);
    public function setShell(  \devmx\Ts3Shell\Shell\AbstractShell $s);
}

?>
