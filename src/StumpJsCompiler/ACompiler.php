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
    
    protected $minifiedDirectory;
    
    protected $minifiedFiles;
    
    protected $minifiedOutput;
    
    public function __construct(JsCompiler $comp)
    {
        $this->compFactory = $comp;
        $this->setConfig($this->compFactory->getConfig());
        $this->setMinifiedDirectory();
        $this->setExecutable();
    }

    /**
     * Starts the execution
     */
    public function run()
    {
        $this->prepareExecution();
        $this->execute();
    }

    public function getExecutable()
    {
        return $this->executable;
    }

    /**
     * Sets the name of the current executable thats listed in the config
     * 
     * @param string|null $exec
     * @throws \StumpJsCompiler\Exception\UnknownExecutableException
     * @return \StumpJsCompiler\ACompiler
     */
    public function setExecutable($exec = null)
    {
        if($exec === null){
            $config = $this->compFactory->getConfig();
            $filekey = $config['compiler']['current'];
            
            if(isset($config['executables'][$filekey])){
                $file = $config['executables'][$filekey];
            }else{
                \StumpJsCompiler\Exception\Factory::throwUnknownExecutable();
            }
            $this->executable = $this->compFactory->getBinLoc().DIRECTORY_SEPARATOR.$file;
        }else{
            $this->executable = $exec;
        }
        
        return $this;
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
        $this->minifiedFiles[] = $this->minifiedOutput;
    }

    protected abstract function prepareExecution();

    /**
     * TODO check if the compiler has the necessary requirements
     */
    public function checkRequirements()
    {}
    
    public function createMinifiedDir()
    {
        if(!file_exists($this->minifiedDirectory)){
            $res = mkdir($this->minifiedDirectory, 0777, true);
            
            if(!$res){
            	throw new \Exception('cannot create minified directory');
            }
        }
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \StumpJsCompiler\Channels\IMinify::setMinifiedDirectory()
     */
    public function setMinifiedDirectory()
    {
    	$this->minifiedDirectory = $this->config['workarea'].DIRECTORY_SEPARATOR.'minified'; 
    	
    	return $this;
    }
    
    public function getMinifiedDirectory()
    {
        return $this->minifiedDirectory;
    }
    
    public function getMinifiedFiles()
    {
        return $this->minifiedFiles;
    }
    
    /**
     * 
     * @return \StumpJsCompiler\ACompiler
     */
    public function setMinifiedOutput()
    {
        if($this->minifiedDirectory !== null){
            $baseName = $this->fileToMinify->getBasename('.js');
            $this->minifiedOutput = $this->minifiedDirectory.DIRECTORY_SEPARATOR.$baseName.'-min.js';
        }
        
        return $this;
    }
    
    /**
     * 
     * @param string $m
     * @return \StumpJsCompiler\ACompiler
     */
    public function fileToMinify($m)
    {
        $this->fileToMinify = new \SplFileInfo($m);
        
        return $this;
    }
}
