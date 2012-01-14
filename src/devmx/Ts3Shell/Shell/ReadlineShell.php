<?php
namespace devmx\Ts3Shell\Shell;

/**
 *
 * @author drak3
 */
class ReadlineShell extends AbstractShell
{
    protected $useHistory  = false;
    protected $historyFile = '';
    
    public function setUseHistoryFile($use) {
        $this->useHistory = $use;
    }
    
    public function setHistoryFilePath($file) {
        $this->historyFile = $file;
    }
    
    protected function initShell() {
        parent::initShell();
        if($this->useHistory) {
            try {
                $this->loadHistory();
            } catch(\Exception $e) {
                 $this->out->getErrorOutput()->writeln(sprintf('<error> cannot open history file %s</error>',$this->histFile));
            }
       }
    }
    
    protected function loadHistory() {
        if(is_file($this->historyFile)) {
            if( is_readable( $this->historyFile)) {
                readline_read_history($this->historyFile);
            }
            else {
                throw new \RuntimeException(sprintf("cannot load history file %s", $this->historyFile));
            } 
        }
    }
    
    protected function shutDownShell() {
        parent::shutDownShell();
        if($this->useHistory) {
                try {
                    $this->saveHistory($this->historyFile);
                }
                catch(\Exception $e) {
                    $this->out->getErrorOutput()->writeln(sprintf('<error>Cannot write history file %s</error>',$this->historyFile));
                }
        }
    }
    
    protected function saveHistory() {
        if(!is_file($this->historyFile)) {
            touch($this->historyFile);
        }
        if(is_writeable( $this->historyFile )) {
            readline_write_history($this->historyFile);
        }
        else {
            throw new \RuntimeException(sprintf("cannot save history file %s", $this->historyFile));
        }
    }
    
    protected function readLine() {
        $line = readline($this->prompt);
        readline_add_history($line);
        return $line;
    }
    
}

?>
