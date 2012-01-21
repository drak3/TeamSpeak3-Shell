<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace devmx\Ts3Shell\Shell;
use devmx\Ts3Shell\ShellJob\ShellJobInterface;
/**
 *
 * @author drak3
 */
class JobShell extends ReadlineShell
{
    protected $currentLine = '';
    protected $haveLine;
    protected $registeredCallbackHandler=false;
    
    public function initShell() {
        parent::initShell();
        $this->haveLine = false;
        
    }
    
    public function readLine() {
        readline_callback_handler_install($this->prompt , array($this,'handleReadlineCallback'));
        while(!$this->haveLine) {
            $read = $this->buildReadArray();
            $write = $this->buildWriteArray();
            $exceptional = $this->buildExceptionalArray();
        
            $numberOfChanged = stream_select($read,$write,$exceptional,null);
            if($numberOfChanged > 0) {
                $notifydata = trim($this->notifyJobs($read,$write,$exceptional));
                if($notifydata != '') {
                    $this->out->writeln('');
                    $this->out->writeln($notifydata);
                }
                $this->out->write($this->notifyJobs($read,$write,$exceptional));
                if(in_array(STDIN,$read)) {
                    readline_callback_read_char(); 
                }
            }
        }
        $this->haveLine = false;
        return $this->currentLine;
    }
    
    protected function handleReadlineCallback($line) {
        readline_callback_handler_remove(); //must be done to avoid additional prompts printed
        $this->haveLine = true;
        $this->currentLine = $line;
        readline_add_history($line);
    }
    
    public function addJob(  ShellJobInterface $j) {
        $this->jobs[] = $j;
    }
    
    public function removeJob(  ShellJobInterface $j) {
        foreach($this->jobs as $key=>$job) {
            if($job === $j) {
                unset($this->jobs[$key]);
            }
        } 
    }
    
    protected function buildReadArray() {
        $r = Array(STDIN);
        foreach($this->jobs as $job) {
            $r = array_merge($r, $job->getInputStreams());
        }
        return $r;
    }
    
    protected function buildWriteArray() {
        $w = Array();
        foreach($this->jobs as $job) {
            $w = array_merge($w, $job->getOutputStreams());
        }
        return $w;
    }
    
    protected function buildExceptionalArray() {
        $e = Array();
        foreach($this->jobs as $job) {
            $e = array_merge($e, $job->getExceptionalStreams());
        }
        return $e;
    }
    
    protected function notifyJobs($r, $w, $e) {
        $ret = '';
        //register input streams
        foreach($r as $stream) {
            foreach($this->jobs as $job) {
                if(in_array($stream, $job->getInputStreams())) {
                    $ret .= (String) $job->handleChange($stream,  ShellJobInterface::CHANGED_TYPE_INPUT);
                }
            }
        }
        
        foreach($w as $stream) {
            foreach($this->jobs as $job) {
                if(in_array($stream, $job->getOutputStreams())) {
                    $ret .= (String) $job->handleChange($stream, ShellJobInterface::CHANGED_TYPE_OUTPUT);
                }
            }
        }
        
        foreach($e as $stream) {
            foreach($this->jobs as $job) {
                if(in_array($stream, $job->getExceptionalStreams())) {
                    $ret .= (String) $job->handleChange($stream, ShellJobInterface::CHANGED_TYPE_EXCEPTIONAL);
                }
            }
        }
        return $ret;
    }
}

?>
