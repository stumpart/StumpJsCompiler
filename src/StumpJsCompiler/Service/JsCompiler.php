<?php

namespace StumpJsCompiler\Service;

use StumpJsCompiler\Channels\Minifier;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use StumpJsCompiler\Channels\Minify;
use StumpJsCompiler\Channels\Combiner;

class JsCompiler implements FactoryInterface
{
	protected $compiler;

	protected $config;

	protected $binLoc;

    protected $srcLocation;

    protected $files;
	
    protected $type;

	/* (non-PHPdoc)
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$this->setConfig($serviceLocator);
		$this->setWorkArea();
		$this->compiler = strtolower($this->config['compiler']['current']);

		$this->setBinLoc();
        $this->setSrcLocation();
		return $this;
	}
    /**
     *
     */
	public function compile($type)
	{
		$this->type = $type;
	    $this->compileFiles($type);

	    if(isset($this->config['compiler']['minify']) &&
	       $this->config['compiler']['minify']){
	       $min = new Minifier($this);
	       $min->run();
        }

        $combiner = new Combiner($this);
        $combiner->run();
        $result = $combiner->getCombinedContents();
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

    public function compileFiles($type)
    {
        if(isset($this->config['builds'][$type])){
            $paths = $this->config['builds'][$type];

            if(is_array($paths)){
                foreach($paths as $p){
                    $this->files[] = $this->srcLocation.DIRECTORY_SEPARATOR.$p;
                }
            }
        }
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
}
