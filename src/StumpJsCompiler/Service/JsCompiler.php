<?php

namespace StumpJsCompiler\Service;

use StumpJsCompiler\Channels\Minifier;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use StumpJsCompiler\Channels\Minify;
use StumpJsCompiler\Channels\Combiner;
use StumpJsCompiler\Cache;
use StumpJsCompiler\Export;

class JsCompiler implements FactoryInterface
{
	protected $compiler;

	protected $config;

	protected $binLoc;

    protected $srcLocation;

    protected $files;
	
    protected $type;
    
    protected $cacheObj;
    
    protected $timeStamp;
    
    protected $cachKey;
    
    protected $contentTypes = array(
                'javascript'=>'application/x-javascript; charset=utf-8',
                'css'=>'text/css; charset=utf-8'
            );

    /**
     * 
     * @var Export
     */
    protected $exportObj;
    
	/**
	 * TODO find some way to inject the Cache and the Export objects
	 *  
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$this->setConfig($serviceLocator);
		$this->setWorkArea();
		$this->compiler = strtolower($this->config['compiler']['current']);

		$this->setBinLoc();
        $this->setSrcLocation();
        
        //create caching object
        $this->cacheObj = new Cache();
        
        //create export object
        $this->exportObj = new Export();
           
		return $this;
	}
    /**
     *
     */
	public function compile($type, $timestamp)
	{
		$this->type = $type;
		$this->timeStamp = $timestamp;
	    $this->gatherSrcFiles($type);
	    $this->setCacheKey();
        
	    if(!($contents= $this->cacheObj->get($this->cachKey))){
            $contents = $this->runActions();
            
	        //cache results
	        $this->cacheObj->set($this->cachKey, $contents);
	    }
	    
	   $this->exportObj->setContents($contents);
	    $this->prepareForExport();
	    $this->exportObj->send();
	}
	
	public function runActions()
	{
	    $contents = '';
	    $actionsCollection = (array)$this->config['actions'];
	    foreach($actionsCollection as $action)
	    {
	        $actionObj = new $action($this);
	        $contents = $actionObj->setContents($contents)->run();	      
	    }
	    
	    return $contents;
	}
	
	
	protected function prepareForExport()
	{
	    $this->exportObj->setContentType($this->contentTypes['javascript']);
	    $this->exportObj->setCacheLength($this->config['builds'][$this->type]['cache-lifetime']);
	    $this->exportObj->initHeaders();

	    return $this->exportObj;
	}

	public function setCacheKey()
	{
	    $this->cachKey = $this->type.'_'.$this->timeStamp;
	}

    /**
     *
     * @param string $src
     */
    public function setSrcLocation($src = null)
    {
        if($src === null){
            $src = $this->orgWorkingDir = getcwd();

            $docroot = $_SERVER['DOCUMENT_ROOT'];

            if($src == $docroot){
                $src = $docroot;
            }

            if(realpath($docroot.'/../') == $src){
                $src .= DIRECTORY_SEPARATOR.'public';
            }
        }

        $this->srcLocation = $src;
    }
    
    public function gatherSrcFiles($type)
    {
        if(isset($this->config['builds'][$type]['files'])){
            $paths = $this->config['builds'][$type]['files'];
        
            if(is_array($paths)){
                foreach($paths as $p){
                    $this->files[] = $this->srcLocation.DIRECTORY_SEPARATOR.$p;
                }
            }
        }
        
        return $this;
    }
    
    public function compileFiles($type)
    {

    }

    public function getSrcLocation()
    {
        return $this->srcLocation;
    }

	public function getConfig()
	{
		return $this->config;
	}
	
	public function setConfig(ServiceLocatorInterface $serviceLocator)
	{
		$this->config = $serviceLocator->get('config');
		$this->config['compiler']['modulename'] = basename(realpath(__DIR__."/../../../"));
	}

    public function getFiles()
    {
        return $this->files;
    }

	public function getMinifierName()
	{
		return $this->compiler;
	}

	public function getBinLoc()
	{
		return $this->binLoc;
	}
	
	public function getType()
	{
		return $this->type;
	}

	public function setBinLoc($bin = null)
	{
		if($bin === null){
			$this->binLoc = realpath(__DIR__."/../../../bin");

			if(!$this->binLoc){
				//todo throw exception
			}
		}else{

		}
	}
	
	public function setWorkArea()
	{
		$this->config['workarea'] = realpath($this->config['compiler']['workareaDir']).
									DIRECTORY_SEPARATOR.$this->config['compiler']['modulename'];
	}
	
	public function scriptLocation()
	{
	    
	}
}
