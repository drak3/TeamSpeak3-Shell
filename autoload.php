<?php
use \Symfony\Component\ClassLoader\UniversalClassLoader;
require_once(__DIR__.'/lib/Symfony/Component/ClassLoader/UniversalClassLoader.php');

$loader = new UniversalClassLoader();
$loader->registerNamespaces(Array(
    'Symfony' => __DIR__.'/lib',
    'devmx\Ts3Shell' => __DIR__.'/lib',
    'devmx' => __DIR__.'/lib/devmx/Teamspeak3',
));
$loader->register();
?>
