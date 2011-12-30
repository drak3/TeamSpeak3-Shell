<?php
namespace devmx\Ts3Shell;
use devmx\Teamspeak3\Query\QueryTransport;

if(PHP_SAPI !== 'cli') {
    die('You must run this script from command line'.PHP_EOL);
}
if($_SERVER['argc'] < 3) {
    die(sprintf('Usage: %s host queryport', $_SERVER['argv'][0]).PHP_EOL);
}

require_once(__DIR__.'/autoload.php');

$shell = new Shell();
$query = QueryTransport::getCommon( $_SERVER['argv'][1] , $_SERVER['argv'][2] );
$shell->addCommandHandler(new CommandHandler\Teamspeak3Handler( $query ));
$shell->addCommandHandler(new CommandHandler\ShellHandler);
$shell->runShell('$ ');
if($query->isConnected()) {
    $query->disconnect();
}
?>
