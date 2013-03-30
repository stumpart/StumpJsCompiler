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


    public function minify()
    {}


}
