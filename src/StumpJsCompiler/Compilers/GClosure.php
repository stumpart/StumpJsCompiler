<?php

namespace StumpJsCompiler\Compilers;

use StumpJsCompiler\ACompiler;
use StumpJsCompiler\Service\JsCompiler;

/**
 * 
 * @author barringtonhenry
 *
 */
class GClosure extends ACompiler{

    protected $commandTemplate  = "java -jar %s --js %s --js_output_file %s";
    
    public function __construct($comp)
    {
        parent::__construct($comp);
    }

    public function prepareExecution()
    {
        $this->CreateMinifiedDir();
        $this->setMinifiedOutput();
    
        $this->command = sprintf($this->commandTemplate, $this->executable,
                $this->fileToMinify->getRealPath(), $this->minifiedOutput);
        
        //echo $this->command;
    }
    
	/** (non-PHPdoc)
     * @see \StumpJsCompiler\Channels\IMinify::minify()
     */
    public function minify ()
    {
     
    }

}
