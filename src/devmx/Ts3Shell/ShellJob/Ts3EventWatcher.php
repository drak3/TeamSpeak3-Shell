<?php
namespace devmx\Ts3Shell\ShellJob;
use devmx\Transmission\TCP;
use devmx\Ts3Shell\Shell;

/**
 *
 * @author drak3
 */
class Ts3EventWatcher implements ShellJobInterface
{
    protected $transmission;
    
    public function __construct( TCP $t) {
        $this->transmission = $t;
    }

    public function getInputStreams()
    {
        return Array($this->transmission->getStream());
    }
    
    public function getOutputStreams() {
        return Array();
    }
    
    public function getExceptionalStreams() {
        return Array();
    }

    public function handleChange( $stream , $type )
    {
        $data = trim($this->transmission->getAll());
        if($data !== '') {
            return $data.PHP_EOL;
        }
        return '';
    }

}

?>
