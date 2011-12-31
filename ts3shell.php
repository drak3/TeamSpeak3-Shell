#!/usr/bin/php
<?php
namespace devmx\Ts3Shell;
use devmx\Teamspeak3\Query\QueryTransport;
use devmx\Transmission\TCP;
use devmx\Teamspeak3\Query\Transport\Common\CommandTranslator;
use devmx\Teamspeak3\Query\Transport\Common\ResponseHandler;
use devmx\Ts3Shell\ShellJob\Ts3EventWatcher;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output\ConsoleOutput;

$welcome = <<<'EOF'
Welcome to the devmx TeamSpeak3-Shell
You can use shell commands as well as teamspeak3 query command
EOF;
if(PHP_SAPI !== 'cli') {
    die('You must run this script from command line'.PHP_EOL);
}
require_once(__DIR__.'/autoload.php');

$def = new Input\InputDefinition();
$def->addArgument(new Input\InputArgument( 'host'));
$def->addArgument(new Input\InputArgument('port', Input\InputArgument::OPTIONAL , '', 10011));
$def->addOption(new Input\InputOption('history_file', null , Input\InputOption::VALUE_OPTIONAL, '', getenv('HOME').'/.ts3shell_history'));
$def->addOption(new Input\InputOption('disable-history', 'd', Input\InputOption::VALUE_NONE));

$input = new Input\ArgvInput();
$input->bind($def);
$input->validate();

$transmission = new \devmx\Transmission\TCP($input->getArgument('host') , $input->getArgument('port'));
$query = new QueryTransport($transmission , new CommandTranslator() , new ResponseHandler());

$shell = new Shell('ts3shell','0.1', $input->getOption('history_file'));
$shell->setUseHistory(!$input->getOption('disable-history'));

$shell->addCommandHandler(new CommandHandler\Teamspeak3Handler( $query ));
$shell->addCommandHandler(new CommandHandler\ShellHandler);

$shell->addJob(new Ts3EventWatcher($transmission));

$shell->runShell($welcome, '$ ');
if($query->isConnected()) {
    $query->disconnect();
}
?>
