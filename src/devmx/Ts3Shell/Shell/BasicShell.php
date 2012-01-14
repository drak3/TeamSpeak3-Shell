<?php
namespace devmx\Ts3Shell\Shell;

/**
 * Basic shell for systems without readline
 * @author drak3
 */
class BasicShell extends AbstractShell
{
    protected function readLine() {
        $this->out->write($this->prompt);
        $line = fgets(STDIN, 1024);
        $line = trim($line);
        $line = (!$line && strlen($line) == 0) ? false : $line;
        return $line;
    }  
}

?>
