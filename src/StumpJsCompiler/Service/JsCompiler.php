<?php

namespace StumpJsCompiler\Service;

use StumpJsCompiler\Exception\InvalidLocationException;
use StumpJsCompiler\Channels\Minifier;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use StumpJsCompiler\Channels\Minify;
use StumpJsCompiler\Channels\Combiner;
use StumpJsCompiler\Cache;
use StumpJsCompiler\Export;

/**
 * 
 * @author barringtonhenry
 *
 */
class JsCompiler implements FactoryInterface
{
    /**
     * String that represents the compiler adapter
     * 
     * @var string
     */
	protected $compiler;
    /**
     * The module configuration
     * 
     * @var array
     */
	protected $config;
    
    /**
     * String representing the 
     * 
     * @var string
     */
	protected $binLoc;
    /**
     * 
     * @var string
     */
    protected $srcLocation;
    /**
     * 
     * @var array
     */
    protected $files;
	/**
	 * 
	 * @var string
	 */
    protected $type;
    /**
     * The cache object that will be used to cache the
     * results of the compilation
     * 
     * @var StumpJsCompiler\Cache
     */
    protected $cacheObj;
    
    /**
     * The time of the last change of the set of javascript files 
     * 
     * @var int
     */
    protected $timeStamp;
    
    /**
     * The key value for the cached javascript compilation 
     * 
     * @var string
     */
    protected $cachKey;
    
    /**
     * 
     * @var array
     */
    protected $contentTypes = array(
        'javascript'=>'application/javascript; charset=utf-8',
        'css'=>'text/css; charset=utf-8'
    );

    /**
     * The object used to provide a set of utilities to export the 
     * results of the compilation request to the browser
     * 
     * @var StumpJsCompiler\Export
     */
    protected $exportObj;
    
	/**
	 * Creates the javascript compiler 
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
     * This serves as the driver to the compilation process.
     * Serves as the starting point for javascript/css compilation
     * Involves executing a set of actions that is needed to minify and combine the 
     * set of javascript files
     * 
     * @param string $type The type of the compilation
     * @param int $timestamp the timestamp of the last change made to the javascript files
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
    
    /**
     * 
     * @return string
     */
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
    
    /**
     * Prepare the results of the compilation process for export to 
     * the browser
     * 
     * @return \StumpJsCompiler\Export
     */
    protected function prepareForExport()
    {
        $this->exportObj->setContentType($this->contentTypes['javascript']);
        $this->exportObj->setCacheLength($this->config['builds'][$this->type]['cache-lifetime']);
        $this->exportObj->setLastModified($this->timeStamp);
        $this->exportObj->initHeaders();
        
        $compilerService = $this;
        $this->exportObj->getEventManager()->attach(Export::PRE_EXPORT, function($e) use ($compilerService){
            $config = $compilerService->getConfig();
            $compilationConfig = $config['builds'][$compilerService->getType()];
             
            if(isset( $compilationConfig['headers'] )){
               $e->getTarget()->setHeaders($compilationConfig['headers']);
            }
        });
        
        return $this->exportObj;
    }
    /**
     * Generates and sets the cache key
     * 
     * @return null
     */
    public function setCacheKey()
    {
        $this->cachKey = $this->type.'_'.$this->timeStamp;
    }

    /**
     * Generates or sets the location of the javascript source
     * files
     *
     * @param string $src The src location
     * @return null 
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
    
    /**
     * Gathers all the source files for the particular compilation
     * type and stores the location of the files in collection
     * 
     * @param string $type
     * @return \StumpJsCompiler\Service\JsCompiler
     */
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
    
    /**
     * 
     * @return \StumpJsCompiler\Service\unknown_type
     */
    public function getSrcLocation()
    {
        return $this->srcLocation;
    }
    
    /**
     * 
     * @return \StumpJsCompiler\Service\unknown_type
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * 
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setConfig(ServiceLocatorInterface $serviceLocator)
    {
        $this->config = $serviceLocator->get('config');
        $this->config['compiler']['modulename'] = basename(realpath(__DIR__."/../../../"));
    }
    
    /**
     * 
     * @return \StumpJsCompiler\Service\unknown_type
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * 
     * @return \StumpJsCompiler\Service\unknown_type
     */
    public function getMinifierName()
    {
        return $this->compiler;
    }
    
    /**
     * 
     * @return string
     */
    public function getBinLoc()
    {
        return $this->binLoc;
    }
    /**
     * Gets the type of location
     *  
     * @return string The type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * @param unknown_type $bin
     */
    public function setBinLoc($bin = null)
    {
        if($bin === null){
            $this->binLoc = realpath(__DIR__."/../../../bin");
            
            if(!$this->binLoc){
                \StumpJsCompiler\Exception\Factory::throwInvalidLocation();
            }
        }else{
            $this->binLoc = $bin;
        }
    }
    
    /**
     * @return null
     */
    public function setWorkArea()
    {
        $this->config['workarea'] = realpath($this->config['compiler']['workareaDir']).
                                    DIRECTORY_SEPARATOR.$this->config['compiler']['modulename'];
    }
    
    
    public function ifNotModified(array $matches)
    {
        $iETag = sha1($matches['timestamp']);
        $lastModified = gmdate('D, d M Y H:i:s', $matches['timestamp']).' GMT';
        
        if (
                (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] ==  $lastModified) ||
                (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $iETag)
        ) {
            $this->exportObj->setHeader("ETag", $iETag);
            $this->exportObj->setHeader("{$_SERVER['SERVER_PROTOCOL']} 304 Not Modified");
            $this->exportObj->sendheaders();
            
            exit;
        }
    }
	
}
