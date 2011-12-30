<?php
namespace devmx\Ts3Shell;
use devmx\Ts3Shell\ShellJob\ShellJobInterface;
use devmx\Ts3Shell\CommandHandler\CommandHandlerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author drak3
 */
class Shell {
    
    protected $handler;
    protected $exit=FALSE;
    protected $out;
    protected $jobs = Array();
    protected $commandHandler;
    protected $name;
    protected $version;
    protected $prompt;
    
    public function __construct($name, $version) {
        if(!function_exists('readline')) {
            throw new \RuntimeException('You must have readline extension installed');
        }
        $this->commandHandler = new CommandHandler\CommandHandler($name,$version);
        $this->addCommandHandler($this->commandHandler);
        $this->out = new \Symfony\Component\Console\Output\ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, TRUE);
        $this->name = $name;
        $this->version = $version;
        
    }
    
    public function addCommandHandler( CommandHandlerInterface $h) {
        $h->setShell($this);
        $this->handler[] = $h;
    }
    
    public function addCommand(Command $c) {
        $this->commandHandler->add($c);
    }
    
    public function addJob(  ShellJobInterface $j) {
        $this->jobs[] = $j;
    }
    
    public function removeJob(  ShellJobInterface $j) {
        foreach($this->jobs as $key=>$job) {
            if($job === $j) {
                unset($this->jobs[$key]);
            }
        } 
    }
    
    public function removeCommandHandler(  CommandHandlerInterface $h) {
        foreach($this->handler as $key=>$handler) {
            if($handler === $h) {
                unset($this->handler[$key]);
            }
        } 
    }
    
    public function runShell($headline,$prompt) {
        $this->out->writeln($headline);
        $this->prompt = $prompt;
        readline_callback_handler_install($prompt , array($this,'handleLine'));
        while(!$this->exit) {
            $read = $this->buildReadArray();
            $write = $this->buildWriteArray();
            $exceptional = $this->buildExceptionalArray();
            $numberOfChanged = stream_select($read,$write,$exceptional,null);
            if($numberOfChanged > 0) {
                $notifydata = trim($this->notifyJobs($read,$write,$exceptional));
                if($notifydata != '') {
                    $this->out->writeln('');
                    $this->out->writeln($notifydata);
                    readline_callback_handler_install($prompt , array($this,'handleLine'));
                }
                $this->out->write($this->notifyJobs($read,$write,$exceptional));
                if(in_array(STDIN,$read)) {
                    readline_callback_read_char(); 
                }
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
        $this->out->getErrorOutput()->writeln(sprintf('%s<error>Could not find command %s</error>',$command->getName()));
    }
    
    protected function buildReadArray() {
        $r = Array(STDIN);
        foreach($this->jobs as $job) {
            $r = array_merge($r, $job->getInputStreams());
        }
        return $r;
    }
    
    protected function buildWriteArray() {
        $w = Array();
        foreach($this->jobs as $job) {
            $w = array_merge($w, $job->getOutputStreams());
        }
        return $w;
    }
    
    protected function buildExceptionalArray() {
        $e = Array();
        foreach($this->jobs as $job) {
            $e = array_merge($e, $job->getExceptionalStreams());
        }
        return $e;
    }
    
    protected function notifyJobs($r, $w, $e) {
        $ret = '';
        //register input streams
        foreach($r as $stream) {
            foreach($this->jobs as $job) {
                if(in_array($stream, $job->getInputStreams())) {
                    $ret .= (String) $job->handleChange($stream,  ShellJobInterface::CHANGED_TYPE_INPUT);
                }
            }
        }
        
        foreach($w as $stream) {
            foreach($this->jobs as $job) {
                if(in_array($stream, $job->getOutputStreams())) {
                    $ret .= (String) $job->handleChange($stream, ShellJobInterface::CHANGED_TYPE_OUTPUT);
                }
            }
        }
        
        foreach($e as $stream) {
            foreach($this->jobs as $job) {
                if(in_array($stream, $job->getExceptionalStreams())) {
                    $ret .= (String) $job->handleChange($stream, ShellJobInterface::CHANGED_TYPE_EXCEPTIONAL);
                }
            }
        }
        return $ret;
    }
    
}

?>
