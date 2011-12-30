<?php
namespace devmx\Ts3Shell;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 *
 * @author drak3
 */
class CommandResponse implements \Symfony\Component\Console\Output\ConsoleOutputInterface, CommandResponseInterface
{
    const EXIT_SUCCESS = 0;
    const EXIT_FAILURE = 1;
    
    protected $out;
    protected $err;
    protected $exitCode;
    protected $isErrorOutput;
    
    public function __construct() {
        $this->out = new BufferingOutput(OutputInterface::VERBOSITY_NORMAL, TRUE);
        $this->err = new BufferingOutput(OutputInterface::VERBOSITY_NORMAL, TRUE);
    }
    
    public function setExitCode($code) {
        $this->exitCode = (int) $code;
    }
    
    public function getExitCode() {
        return $this->exitCode;
    }
    
    public function getStandardError()
    {
        return $this->err->getOutput();
    }

    public function getStandardOutput()
    {
        return $this->out->getOutput();
    }

    public function getErrorOutput()
    {
        return $this->err;
    }

    public function setErrorOutput( OutputInterface $error )
    {
        
    }

    public function getFormatter()
    {
        return $this->out->getFormatter();
    }

    public function getVerbosity()
    {
        return $this->out->getVerbosity();
    }

    public function isDecorated()
    {
        return $this->out->isDecorated();
    }

    public function setDecorated( $decorated )
    {
        return $this->out->setDecorated($decorated);
    }

    public function setFormatter( OutputFormatterInterface $formatter )
    {
        return $this->out->setFormatter($formatter);
    }

    public function setVerbosity( $level )
    {
        return $this->out->setVerbosity($level);
    }

    public function write( $messages , $newline = false , $type = 0 )
    {
        return $this->out->write($messages , $newline , $type);
    }

    public function writeln( $messages , $type = 0 )
    {
        return $this->out->writeln($messages , $type);
    }


    

    
    
}

?>
