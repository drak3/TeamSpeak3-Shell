<?php
namespace devmx\Ts3Shell;

/**
 *
 * @author drak3
 */
class Shell {
    
    protected $handler;
    protected $exit=FALSE;
    protected $out;
    
    public function __construct() {
        if(!function_exists('readline')) {
            throw new \RuntimeException('You must have readline extension installed');
        }
        $this->out = new \Symfony\Component\Console\Output\ConsoleOutput();    
    }
    
    public function addCommandHandler($h) {
        $h->setShell($this);
        $this->handler[] = $h;
    }
    
    public function addJob($j) {
        
    }
    
    public function removeJob($j) {
        
    }
    
    public function removeCommandHandler($h) {
        foreach($this->handler as $key=>$handler) {
            if($handler === $h) {
                unset($this->handler[$key]);
            }
        } 
    }
    
    public function runShell($prompt) {
     readline_callback_handler_install($prompt , array($this,'handleLine'));
        while(!$this->exit) {
            $read = array(STDIN);
            $write = NULL;
            $exceptional = NULL;
            $numberOfChanged = stream_select($read,$write,$exceptional,null);
            if($numberOfChanged > 0) {
                readline_callback_read_char();
            }
        }
    }
    
    public function exitShell() {
        $this->exit = TRUE;
    }
        
    public function handleLine($line) {
        readline_add_history($line);
        $command = explode(' ',$line);
        $command = new CommandCall($command[0],$command,$line);
        foreach($this->handler as $handler) {
            if($handler->canHandle($command->getName())) {
                $r = $handler->handle($command);
                if($this->exit) {
                    return;
                }
                $this->out->write($r->getStandardOutput());
                $this->out->getErrorOutput()->write($r->getStandardError());
                return;
            }
        }
        $this->out->getErrorOutput()->writeln(sprintf('Could not find command %s',$command->getName()));
    }
    
}

?>
