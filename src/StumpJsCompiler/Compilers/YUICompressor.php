<?php

namespace StumpJsCompiler\Compilers;



use StumpJsCompiler\ACompiler;
use StumpJsCompiler\Service\JsCompiler;

class YUICompressor extends ACompiler
{
	protected $commandTemplate  = "java -jar %s %s -o %s";

	protected $executable;

	protected $combined;
    /**
     * @var SplFileInfo
     */
    protected $fileToMinify;

    


	public function __construct(JsCompiler $comp)
	{
		parent::__construct($comp);
	}


	public function prepareExecution()
	{
	    $this->CreateMinifiedDir();
        $this->setMinifiedOutput();

		$this->command = sprintf($this->commandTemplate, $this->executable,
		                          $this->fileToMinify->getRealPath(), $this->minifiedOutput);
	}



    public function setMinifiedOutput()
    {
        if($this->minifiedDirectory !== null){
            $baseName = $this->fileToMinify->getBasename('.js');
            $this->minifiedOutput = $this->minifiedDirectory.DIRECTORY_SEPARATOR.$baseName.'-min.js';
        }
    }

    public function minify()
    {}

    public function fileToMinify($m)
    {
        $this->fileToMinify = new \SplFileInfo($m);
    }

}
