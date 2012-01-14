<?php
namespace devmx\Ts3Shell\CommandHandler;
use devmx\Ts3Shell\Shell\AbstractShell;
use devmx\Ts3Shell\CommandResponse;
use devmx\Ts3Shell\BufferingOutput;
use devmx\Ts3Shell\CommandCall;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Command\HelpCommand;
use devmx\Ts3Shell\Commands\StdinAwareInterface;
/**
 *
 * @author drak3
 */
class CommandHandler extends Application implements CommandHandlerInterface
{
    protected $shell;
    protected $help;
    
    
    public function setShell(AbstractShell $s) {
        $this->setAutoExit(FALSE);
        $this->shell = $s;
    }
    
    public function canHandle($name) {
        return $this->has($name);
    }
    
    public function handle(CommandCall $c) {
        $out = new CommandResponse();
        $command = $this->find($c->getName());
        if($command instanceof StdinAwareInterface) {
            $command->setStdin($c->getStdin());
        }
        $code = $this->run(new StringInput( $c->getRaw()), $out);
        $out->setExitCode($code);
        return $out;
    }
    
    protected function getDefaultCommands()
    {
        return Array();
    }


}

?>
