#!/usr/bin/php
<?php
use devmx\Ts3Shell\Ts3Shell;
require_once(__DIR__.'/autoload.php');

$welcome = <<<'EOF'
Welcome to the devmx TeamSpeak3-Shell
You can use shell commands as well as teamspeak3 query command
EOF;
if(PHP_SAPI !== 'cli') {
    die('You must run this script from command line'.PHP_EOL);
}
$shell = new TS3Shell('ts3shell', '0.2', $welcome);
$shell->run();
?>
