<?php
namespace devmx\Ts3Shell;
use devmx\Teamspeak3\Query\QueryTransport;
use devmx\Transmission\TCP;
use devmx\Teamspeak3\Query\Transport\Common\CommandTranslator;
use devmx\Teamspeak3\Query\Transport\Common\ResponseHandler;
use devmx\Ts3Shell\ShellJob\Ts3EventWatcher;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 *
 * @author drak3
 */
class Ts3Shell
{
    protected $name;
    protected $version;
    protected $home;
    protected $in;
    protected $defaultHistFile;
    protected $histFile;
    protected $host;
    protected $port;
    protected $enableHistory;
    protected $usage;
    
    public function __construct($name, $version, $welcome='') {
        if(PHP_SAPI !== 'cli') {
            die('You must run this script from command line'.PHP_EOL);
        }
        $this->name = $name;
        $this->version = $version;
        $this->home = getenv('HOME');
        $this->userDataDir = $this->home.'/.ts3shell/';
        $this->defaultHistFile = $this->userDataDir.'.ts3shell_history';
        $this->prompt = '$ ';
        $this->welcome = $welcome;
        $this->usage = sprintf('Usage: %s [options] host [port]', $_SERVER['argv'][0]).PHP_EOL;
    }
        
    public function run(Input\InputInterface $in=NULL) {
        $this->readInput($in);
        if($this->isFirstRun()) {
            $this->install();
        }
        $shell = $this->getShell();
        $shell->runShell($this->welcome, $this->prompt);
    }
    
    protected function readInput(Input\InputInterface $in=NULL) {
        if($in === NULL) {
            $in = $this->getInput();
        }
        $in->bind($this->getInputDefinition());
        try {
            $in->validate();
        }
        catch(\Exception $e) {
            echo $this->usage;
            exit(1);
        }
        $this->in = $in;
        $this->histFile = $this->in->getOption('history_file');
        $this->enableHistory = !$this->in->getOption('disable-history');
        $this->host = $this->in->getArgument('host');
        $this->port = $this->in->getArgument('port');
    }
    
    protected function getInput() {
        $input = new Input\ArgvInput();
        return $input;
    }
    
    protected function getInputDefinition() {
        $def = new Input\InputDefinition();
        $def->addArgument(new Input\InputArgument( 'host', Input\InputArgument::REQUIRED));
        $def->addArgument(new Input\InputArgument('port', Input\InputArgument::OPTIONAL , '', 10011));
        $def->addOption(new Input\InputOption('history_file', null , Input\InputOption::VALUE_OPTIONAL, '', $this->defaultHistFile));
        $def->addOption(new Input\InputOption('disable-history', 'd', Input\InputOption::VALUE_NONE));
        return $def;
    }
    
    protected function isFirstRun() {
        return !is_dir($this->userDataDir);
    }
    
    protected function install() {
        mkdir($this->userDataDir);
    }
    
    protected function getShell() {
        $transmission = new \devmx\Transmission\TCP($this->host , $this->port);
        $query = new QueryTransport($transmission , new CommandTranslator() , new ResponseHandler());

        $shell = new Shell($this->name,$this->version,$this->welcome);
        $shell->setUseHistory($this->enableHistory);

        $shell->addCommandHandler(new CommandHandler\Teamspeak3Handler( $query ));
        $shell->addCommandHandler(new CommandHandler\ShellHandler);

        $shell->addJob(new Ts3EventWatcher($transmission));
        return $shell;
    }
    
     
}

?>
