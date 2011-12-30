<?php
namespace devmx\Ts3Shell;

/**
 *
 * @author drak3
 */
interface CommandResponseInterface
{
    public function getExitCode();
    public function getStandardOutput();
    public function getStandardError();
}

?>
