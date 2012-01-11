<?php
namespace devmx\Ts3Shell;

/**
 *
 * @author drak3
 */
class CommandCall
{
    protected $name;
    protected $argv;
    protected $stdin;
    
    public function __construct($name, $args, $raw, $stdin="") {
        $this->name = $name;
        $this->argv = $args;
        $this->raw = $raw;
        $this->stdin = $stdin;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setArguments($args) {
        $this->argv = $args; 
    }
    
    public function getArguments() {
        return $this->argv;
    }
    
    public function setStdin($stdin) {
        $this->stdin = $stdin;
    }
    
    public function getStdin() {
        return $this->stdin;
    }
    
    public function getRaw() {
        return $this->raw;
    }
    
    public function setRaw($raw) {
        $this->raw = $raw;
    }
    
}

?>
