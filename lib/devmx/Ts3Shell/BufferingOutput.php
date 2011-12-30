<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace devmx\Ts3Shell;

/**
 *
 * @author drak3
 */
class BufferingOutput extends \Symfony\Component\Console\Output\Output
{
    protected $output;
    public function doWrite($message, $newline) {
        if($newline) {
            $message .= PHP_EOL;
        }
        $this->output .= $message;
    }
    
    public function getOutput() {
        return $this->output;
    }
}

?>
