<?php
namespace devmx\Ts3Shell\ShellJob;

/**
 *
 * @author drak3
 */
interface ShellJobInterface
{
    public function getInputStreams();
    public function getOutputStreams();
    public function getExceptionalStreams();
    public function handleChange($stream);
}

?>
