<?php
namespace devmx\Ts3Shell\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;

/**
 *
 * @author drak3
 */
class GetItemCommand extends Command implements StdinAwareInterface
{
    
    protected $stdin;
    
    public function setStdin($stdin) {
        $this->stdin = $stdin;
    }
    
    protected function configure()
    {
        $this->setDefinition(array(
           new InputArgument('number',  InputArgument::REQUIRED, 'The item to get') 
        ));
    }

    protected function execute( InputInterface $input , OutputInterface $output )
    {
        $items = explode('|', $this->stdin);
        $no = $input->getArgument( 'number' );
        if(isset($items[$no])) {
            $output->writeln(ltrim($items[$no]));
            return 0;
        }
        else {
            if($output instanceof ConsoleOutputInterface) {
                $err = $output->getErrorOutput();
            }
            else {
                $err = $output;
            }
            $err->writeln(sprintf("<error>Item with number %s does not exist",$no));
            return 1;
        }
    }

}

?>
