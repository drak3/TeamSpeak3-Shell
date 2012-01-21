<?php
namespace devmx\Ts3Shell\CommandHandler;
use devmx\Teamspeak3\Query\QueryTransport;
use devmx\Teamspeak3\Query\CommandResponse as Ts3CommandResponse;
use devmx\Ts3Shell\CommandCall;
use devmx\Ts3Shell\CommandResponse as ShellCommandResponse;
use devmx\Ts3Shell\Shell;
use devmx\Ts3Shell\DumbTranslator;

/**
 *
 * @author drak3
 */
class Teamspeak3Handler implements CommandHandlerInterface
{
    /**
     *
     * @var Shell 
     */
    protected $shell;
    
    protected $query;
    
    
    public function __construct(QueryTransport $query) {
        $this->query = $query;
        $this->query->setTranslator(new DumbTranslator());
        if(!$this->query->isConnected()) {
            $this->query->connect();
        }
    }
    
    public function setShell(Shell\AbstractShell $s) {
        $this->shell = $s;
    }
    
    public function canHandle($name) {
        return in_array($name, $this->canHandle);
    }
    
    public function handle(CommandCall $cmd) {
        if($cmd->getName() === 'quit') {
            $this->query->disconnect();
            $this->shell->exitShell();
            return;
        }
        $response = $this->query->query($cmd->raw);
        $events = $this->query->getAllEvents(TRUE);
        $cResponse = new ShellCommandResponse();
        $cResponse->setExitCode($response->getErrorID());
        foreach($events as $event) {
            $cResponse->write($event->getRawResponse());
        }        
        if($response->errorOccured()) {
            $errorMessage = sprintf("<error>%s. Error ID: %d</error>", $response->getErrorMessage(), $response->getErrorID());
            $cResponse->getErrorOutput()->writeln($errorMessage);
        }
        else {
            $output = trim(str_replace('error id=0 msg=ok','',$response->getRawResponse()));
            $output = str_replace('|', "|\n", $output);
            if($output !== '') {
               $cResponse->writeln($output); 
            }
        }
        return $cResponse;
    }
    
    protected $canHandle = Array (
'login',
'logout',                     
'quit',                      
'use',                         
'banadd',                      
'banclient',
'bandelall',
'bandel',
'banlist',
'bindinglist',
'channeladdperm',
'channelclientaddperm',
'channelclientdelperm',
'channelclientpermlist',
'channelcreate',
'channeldelete',
'channeldelperm',
'channeledit',
'channelfind',
'channelgroupadd',
'channelgroupaddperm',
'channelgroupclientlist',
'channelgroupdel',
'channelgroupdelperm',
'channelgrouplist',
'channelgrouppermlist',
'channelgrouprename',
'channelinfo',
'channellist',
'channelmove',
'channelpermlist',
'clientaddperm',
'clientdbdelete',
'clientdbedit',
'clientdbfind',
'clientdbinfo',
'clientdblist',
'clientdelperm',
'clientedit',
'clientfind',
'clientgetdbidfromuid',
'clientgetids',
'clientgetnamefromdbid',
'clientgetnamefromuid',
'clientinfo',
'clientkick',
'clientlist',
'clientmove',
'clientpermlist',
'clientpoke',
'clientsetserverquerylogin',
'clientupdate',
'complainadd',
'complaindelall',
'complaindel',
'complainlist',
'custominfo',
'customsearch',
'ftcreatedir',
'ftdeletefile',
'ftgetfileinfo',
'ftgetfilelist',
'ftinitdownload',
'ftinitupload',
'ftlist',
'ftrenamefile',
'ftstop',
'gm',
'hostinfo',
'instanceedit',
'instanceinfo',
'logadd',
'logview',
'messageadd',
'messagedel',
'messageget',
'messagelist',
'messageupdateflag',
'permfind',
'permget',
'permidgetbyname',
'permissionlist',
'permoverview',
'sendtextmessage',
'servercreate',
'serverdelete',
'serveredit',
'servergroupaddclient',
'servergroupadd',
'servergroupaddperm',
'servergroupclientlist',
'servergroupdelclient',
'servergroupdel',
'servergroupdelperm',
'servergrouplist',
'servergrouppermlist',
'servergrouprename',
'servergroupsbyclientid',
'serveridgetbyport',
'serverinfo',
'serverlist',
'servernotifyregister',
'servernotifyunregister',
'serverprocessstop',
'serverrequestconnectioninfo',
'serversnapshotcreate',
'serversnapshotdeploy',
'serverstart',
'serverstop',
'setclientchannelgroup',
'tokenadd',
'tokendelete',
'tokenlist',
'tokenuse',
'version',
'whoami',                     
    );
    
}

?>
