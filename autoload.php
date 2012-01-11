<?php
namespace devmx\Ts3Shell;
use Symfony\Component\ClassLoader\UniversalClassLoader;
require_once('vendor/.composer/autoload.php');
$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
   'devmx\Ts3Shell' => 'src' 
));
$loader->register();
?>
