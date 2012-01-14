<?php
namespace devmx\Ts3Shell\Shell;
use devmx\Ts3Shell\CommandHandler\CommandHandlerInterface;
use devmx\Ts3Shell\CommandHandler;
use devmx\Ts3Shell\CommandCall;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author drak3
 */
abstract class AbstractShell
{
    protected $handler;
    protected $exit=FALSE;
    protected $out;
    protected $commandHandler;
    protected $name;
    protected $version;
    protected $prompt;
    
    public function __construct($name, $version) {
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
        $this->initShell();
        while(!$this->exit) {
            $line = $this->readLine();
            if($this->exit) {
                break;
            }
            if(!$line) {
                $this->out->writeln('');
            }
            $this->handleLine($line);
        }
        $this->shutDownShell();
        $this->out->writeln('');
    }
    
    abstract protected function readLine();
    protected function initShell() {
        
    }
    protected function shutDownShell() {
        
    }
      
    public function exitShell() {
        $this->exit = TRUE;
    }

        
    public function handleLine($line) {
        readline_add_history($line);
        $commands = explode('|',$line);
        $this->executeCommands($commands);
    }
    
    protected function executeCommands(array $commands) {
        $stdin = '';
        foreach($commands as $command) {
            $resp = $this->executeCommand($command,$stdin);
            if(!$resp) {
                return;
            }
            $stdin = $resp->getStandardOutput();
            $this->out->getErrorOutput()->write($resp->getStandardError());
        }
        $this->out->write($stdin);
    }
    
    /**
     *
     * @param string $command
     * @return CommandResponse
     */
    protected function executeCommand($command,$stdin) {
        $args = explode(' ',$command);
        $args= array_map('trim',$args);
        foreach($args as $key=>$arg) {
            if($arg === '') {
                unset($args[$key]);
            }
        }
        $args = array_values($args);
        $call = new CommandCall($args[0], $args , $command, $stdin);
        foreach($this->handler as $handler) {
            if($handler->canHandle($call->getName())) {
                $r = $handler->handle($call);
                if($this->exit) {
                    return FALSE;
                }
                return $r;
            }
        }
        $this->out->getErrorOutput()->writeln(sprintf('<error>%s: Could not find command %s</error>',$this->name, $call->getName()));
        return FALSE;
    }
        
}

?>
