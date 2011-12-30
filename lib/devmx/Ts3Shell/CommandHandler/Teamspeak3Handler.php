<?php
namespace devmx\Ts3Shell\CommandHandler;
use devmx\Teamspeak3\Query\QueryTransport;
use devmx\Teamspeak3\Query\CommandResponse as Ts3CommandResponse;
use devmx\Ts3Shell\CommandCall;
use devmx\Ts3Shell\CommandResponse as ShellCommandResponse;
use devmx\Ts3Shell\Shell;
use devmx\Ts3Shell\DumbTranslator;

/**
 *
 * @author drak3
 */
class Teamspeak3Handler implements CommandHandlerInterface
{
    /**
     *
     * @var Shell 
     */
    protected $shell;
    
    protected $query;
    //for testing only
    protected $canHandle = Array (
      'use',
      'login',
      'clientlist',
      'channellist',
      'servernotifyregister',
      'help',
      'quit',
    );
    
    public function __construct(QueryTransport $query) {
        $this->query = $query;
        $this->query->setTranslator(new DumbTranslator());
        if(!$this->query->isConnected()) {
            $this->query->connect();
        }
    }
    
    public function setShell(Shell $s) {
        $this->shell = $s;
    }
    
    public function canHandle($name) {
        return in_array($name, $this->canHandle);
    }
    
    public function handle(CommandCall $cmd) {
        if($cmd->getName() === 'quit') {
            $this->query->disconnect();
            $this->shell->exitShell();
            return;
        }
        $response = $this->query->query($cmd->raw);
        $cResponse = new ShellCommandResponse();
        $cResponse->setExitCode($response->getErrorID());
        if($response->errorOccured()) {
            $errorMessage = sprintf("<error>%s. Error ID: %d</error>", $response->getErrorMessage(), $response->getErrorID());
            $cResponse->getErrorOutput()->writeln($errorMessage);
        }
        else {
            $output = trim(str_replace('error id=0 msg=ok','',$response->getRawResponse()));
            if($output !== '') {
               $cResponse->writeln($output); 
            }
        }
        return $cResponse;
    }
    
}

?>
