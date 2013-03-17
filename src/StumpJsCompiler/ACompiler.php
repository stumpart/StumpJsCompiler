<?php

namespace StumpJsCompiler;

use StumpJsCompiler\Service\JsCompiler;
use StumpJsCompiler\Channels\IMinify;

abstract class ACompiler implements IMinify{

	protected $command = '';

	protected $config;

	protected $compFactory;

	protected $executable;

	protected $orgWorkingDir;

    protected $events;

    protected $minifiedDirectory;
    
	public function __construct(JsCompiler $comp)
	{
		$this->compFactory = $comp;
		$this->setConfig($this->compFactory->getConfig());
		$this->setMinifiedDirectory();
		$this->setExecutable();
	}

	/**
	 *
	 * @param EventManagerInterface $events
	 * @return \StumpJsCompiler\ACompiler
	 */
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

    /**
     *
     */
    public function run($sds)
    {
		$this->prepareExecution();
		$this->execute();
    }

    public function getExecutable()
    {
    	return $this->executable;
    }

	public function setExecutable($exec = null)
	{
		if($exec === null){
			$config = $this->compFactory->getConfig();
			$file = $config['files']['yuicompressor'];

			$this->executable = $this->compFactory->getBinLoc().DIRECTORY_SEPARATOR.$file;
		}else{
			$this->executable = $exec;
		}
	}

	/**
	 *
	 * @param array $config
	 */
    public function setConfig(array $config)
    {
    	$this->config = $config;
    }

    /**
     *
     * @return Ambigous <string, unknown>
     */
    public function getSrcLocation()
    {
    	return $this->srcLocation;
    }



    private function execute()
    {
    	exec(escapeshellcmd($this->command), $output, $returnVar);
    }

    protected abstract function prepareExecution();


    public function checkRequirements()
    {

    }
    
    public function createMinifiedDir()
    {
    	if(!file_exists($this->minifiedDirectory)){
    		$res = mkdir($this->minifiedDirectory, 0777, true);
    
    		if(!$res){
    			throw new \Exception('cannot create minified directory');
    		}
    	}
    }
    
    /**
     * (non-PHPdoc)
     * @see \StumpJsCompiler\Channels\IMinify::setMinifiedDirectory()
     */
    public function setMinifiedDirectory()
    {
    	$this->minifiedDirectory = $this->config['workarea'].DIRECTORY_SEPARATOR.'minified'; 
    }
}
