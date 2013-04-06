<?php

namespace StumpJsCompiler;


use StumpJsCompiler\Service\JsCompiler;
use StumpJsCompiler\Channels\IMinify;

abstract class ACompiler implements IMinify{

    /**
     * The shell command to be executed
     * 
     * @var string
     */
    protected $command = '';
    /**
     * 
     * @var array
     */
    protected $config;
    
    /**
     * 
     * @var JsCompiler
     */
    protected $compFactory;
    
    /**
     * 
     * @var string
     */
    protected $executable;
    
    protected $orgWorkingDir;
    
    /**
     * 
     * 
     * @var string
     */
    protected $minifiedDirectory;
    
    /**
     * A collection of absolute paths that contains all the files minified during this 
     * execution request
     * 
     * @var array
     */
    protected $minifiedFiles;
    
    /**
     * A absolute file path pointing to the resulting file
     * from the compilation
     * 
     * @var string
     */
    protected $minifiedOutput;
    
    /**
     * The results that comes from executing the shell command
     * 
     * @var int
     */
    protected $executionResults;
    
    public function __construct(JsCompiler $comp)
    {
        $this->compFactory = $comp;
        $this->setConfig($this->compFactory->getConfig());
        $this->setMinifiedDirectory();
        $this->setExecutable();
        $this->checkRequirements();
    }

    /**
     * Starts the execution
     * 
     * @return void
     */
    public function run()
    {
        $this->prepareExecution();
        $this->execute();
    }
    
    /**
     * returns the executable 
     * 
     * @return string
     */
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
            
            if(isset($config['executables'][$filekey]) && 
               isset($config['executables'][$filekey]['file']))
            {
                $file = $config['executables'][$filekey]['file'];
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
     * @return string
     */
    public function getSrcLocation()
    {
        return $this->srcLocation;
    }

    /**
     * Executes shell command and attempts to redirect any stderr to stdout 
     * so that it gets captured in the $output variable. We then send this as an 
     * exception in the compilation as a variant of RuntimeException
     * 
     * @return void
     */
    private function execute()
    {
       $res = exec(escapeshellcmd($this->command)." 2>&1", $output, $returnVar);
       $this->executionResults = $returnVar;
       
       if($this->executionResults == 1){
           $errmsg = implode("\n", $output);
           \StumpJsCompiler\Exception\Factory::throwCompilerExecutionException($errmsg);
       }
       
       $this->minifiedFiles[] = $this->minifiedOutput;       
    }

    protected abstract function prepareExecution();

    /**
     * Check if the current setup satisfies the requirements
     * 
     * @return void
     */
    public function checkRequirements()
    {
        $execFile = new \SplFileInfo($this->executable);
        
        if(!$execFile->isReadable())
        {
            \StumpJsCompiler\Exception\Factory::throwRequirementsNotSatisfied(
                        'The exectable '.$this->executable. ' needs to be readable by the apache user '
                    );
        }
    }
    
    /**
     * 
     * @throws \Exception
     * @return \StumpJsCompiler\ACompiler
     */
    public function createMinifiedDir()
    {
        if(!file_exists($this->minifiedDirectory)){
            $mkDirectory = function($dir){
                    return mkdir($dir, 0755, true);
                 };
            
            if(!$mkDirectory($this->minifiedDirectory)){
                //we use the temp folder instead
                $temp = $this->generateTempWorkArea();
                
                if(!file_exists($temp)){
                    
                    if(!$mkDirectory($temp)){
                        \StumpJsCompiler\Exception\Factory::throwException(
                                'cannot create minified directory, maybe permissions issue'
                        );
                    }
                }
                
                $this->minifiedDirectory = $temp;
            }
        }
        
        return $this;
    }
    
    /**
     * Generates a work area in the temp folder
     * @return string
     */
    protected function generateTempWorkArea()
    {
        $paths = array_slice(explode("/", $this->minifiedDirectory), -2, 2);
        $temp = "/tmp/".implode('/',$paths);
        
        return $temp;
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
    
    /**
     * (non-PHPdoc)
     * @see \StumpJsCompiler\Channels\IMinify::getMinifiedDirectory()
     */
    public function getMinifiedDirectory()
    {
        return $this->minifiedDirectory;
    }
    
    /**
     * (non-PHPdoc)
     * @see \StumpJsCompiler\Channels\IMinify::getMinifiedFiles()
     */
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
