<?php
namespace devmx\Ts3Shell\CommandHandler;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 *
 * @author drak3
 */
class ShellHandler implements CommandHandlerInterface
{
    
    protected $shell;
    
    public function canHandle($name) {
        $proc = new ProcessBuilder();
        $proc = $proc->add('type')->add($name)->setTimeout('10')->getProcess();
        $proc->run();
        return $proc->isSuccessful();
    }
    
    public function handle(\devmx\Ts3Shell\CommandCall $cmd) {
        if($cmd->getName() == 'exit') {
            $this->shell->exitShell();
            return;
        }
        $proc = new Process($cmd->getRaw());
        $proc->setStdin($cmd->getStdin());
        $proc->run();
        $resp = new \devmx\Ts3Shell\CommandResponse();
        $resp->write($proc->getOutput());
        $resp->setExitCode($proc->getExitCode());
        $out = trim($proc->getErrorOutput());
        if($out !== '') {
           $resp->getErrorOutput()->writeln('<error>'.$out.'</error>'); 
        }
        if($cmd->getName() == 'cd' && $proc->isSuccessful()){
            $args = $cmd->getArguments();
            if(!isset($args[1])) {
                chdir(getenv('HOME'));
            }
            else {
                $dir = trim($args[1]);
                if(isset($dir[0]) && $dir[0] == '~') {
                    $dir = preg_replace('/~/',getenv('HOME'),$dir,1);
                }
                chdir($dir);
            }
        } 
        return $resp;
    }
    
    public function setShell(\devmx\Ts3Shell\Shell $s) {
        $this->shell = $s;
    }
     
}

?>
