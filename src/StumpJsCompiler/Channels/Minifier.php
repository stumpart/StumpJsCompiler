<?php

namespace StumpJsCompiler\Channels;

use StumpJsCompiler\Service\JsCompiler;

class Minifier extends AChannel {
	
	/**
	 * 
	 * @var StumpJsCompiler\ACompiler
	 */
    protected $minifyAdapter;

    protected $fileContents;
    
    /**
     * 
     * @var JsCompiler
     */
    protected $compilerFactory;
    
    /**
     *  TODO maybe we should put this in the configs
     * @var array
     */
    /*protected $compilers = array(
    	'yuicompressor'=>'YUICompressor',
    	'jsmin'=>'JSMin',
    	'googleclosure'=>'GClosure'
    );*/
    
    /**
     * 
     * @var 
     */
    protected static $minifiedDir = 'minified';

    public function __construct(JsCompiler $js)
    {
        $this->compilerFactory = $js;
        $this->minifierFactory();
    }

    /**
     * 
     */
    public function minifierFactory()
    {
        $config = $this->compilerFactory->getConfig();
        $cn = $config['executables'][$this->compilerFactory->getMinifierName()]['class'];
        $this->minifyAdapter = new $cn($this->compilerFactory);
    }
    
    /**
     *
     */
    public function run()
    {
        $this->getEventManager()->trigger('before.minify', $this, array());
        $files = $this->compilerFactory->getFiles();
        
        foreach($files as $f){
            $this->minifyAdapter->fileToMinify($f);
            $this->minifyAdapter->run();
        }

        $this->getEventManager()->trigger('after.minify', $this, array());
        
        return $this->minifyAdapter->getMinifiedFiles();
    }

    /**
     *
     */
    public function setMinifyAdapter($adapter)
    {
        $this->minifyAdapter = $adapter;
    }
    
    /**
     * 
     * @return JsCompiler
     */
    public function getCompilerFactory()
    {
        return $this->compilerFactory;
    }
}

?>