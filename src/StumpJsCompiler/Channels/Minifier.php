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

    protected $compilerFactory;

    protected $compilers = array(
    	'yuicompressor'=>'YUICompressor',
    	'jsmin'=>'JSMin',
    	'googleclosure'=>'GClosure'
    );
    
    protected static $minifiedDir = 'minified';

    public function __construct(JsCompiler $js)
    {
    	$this->compilerFactory = $js;
    	$this->minifierFactory();
    }

    public function minifierFactory()
    {
    	$cn = self::COMPILER_NAMESPACE.'\\'.$this->compilers[$this->compilerFactory->getMinifierName()];
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
    }

    /**
     *
     */
    public function setMinifyAdapter($adapter)
    {
        $this->minifyAdapter = $adapter;
    }

    public function getCompilerFactory()
    {
        return $this->compilerFactory;
    }
}

?>