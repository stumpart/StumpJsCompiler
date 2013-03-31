<?php

namespace StumpJsCompiler\Channels;

use StumpJsCompiler\CompilerServiceActionsInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;

abstract class AChannel implements EventManagerAwareInterface, CompilerServiceActionsInterface {

    const COMPILER_NAMESPACE = 'StumpJsCompiler\Compilers';

	protected $events;
	
	protected $contents;

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    abstract public function run ();


    /** 
     * (non-PHPdoc)
     * @see \StumpJsCompiler\CompilerServiceInterface::getContents()
     */
    public function getContents ()
    {
        return $this->contents;
    }
    
    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }

}