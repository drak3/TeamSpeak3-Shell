<?php
namespace devmx\Ts3Shell;
use devmx\Teamspeak3\Query\QueryTransport;
use devmx\Transmission\TCP;
use devmx\Teamspeak3\Query\Transport\Common\CommandTranslator;
use devmx\Teamspeak3\Query\Transport\Common\ResponseHandler;
use devmx\Ts3Shell\ShellJob\Ts3EventWatcher;

$welcome = <<<'EOF'
Welcome to the devmx TeamSpeak3-Shell
You can get help for builtins with the shelp command
You can use shell commands as well as teamspeak3 query command
EOF;

$help = <<<'EOF'

EOF;

if(PHP_SAPI !== 'cli') {
    die('You must run this script from command line'.PHP_EOL);
}
if($_SERVER['argc'] < 3) {
    die(sprintf('Usage: %s host queryport', $_SERVER['argv'][0]).PHP_EOL);
}

require_once(__DIR__.'/autoload.php');


$transmission = new \devmx\Transmission\TCP($_SERVER['argv'][1] , $_SERVER['argv'][2]);
$query = new QueryTransport($transmission , new CommandTranslator() , new ResponseHandler());

$shell = new Shell('ts3shell','0.1');

$shell->addCommandHandler(new CommandHandler\Teamspeak3Handler( $query ));
$shell->addCommandHandler(new CommandHandler\ShellHandler);

$shell->addJob(new Ts3EventWatcher($transmission));

$shell->runShell($welcome, '$ ');
if($query->isConnected()) {
    $query->disconnect();
}
?>
