<?php
namespace devmx\Ts3Shell\ShellJob;
use devmx\Ts3Shell\Shell;
/**
 *
 * @author drak3
 */
interface ShellJobInterface
{
    const CHANGED_TYPE_INPUT = 'r';
    const CHANGED_TYPE_OUTPUT = 'w';
    const CHANGED_TYPE_EXCEPTIONAL = 'e';
    public function getInputStreams();
    public function getOutputStreams();
    public function getExceptionalStreams();
    public function handleChange($stream,$type);
}

?>
