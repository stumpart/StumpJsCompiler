<?php

namespace StumpJsCompiler\Channels;

use StumpJsCompiler\Channels\IMinify;

class Combiner extends AChannel {

	protected $files;

	protected $destination;

    protected $combinedContents = '';


    public function __construct(JsCompiler $js)
    {
        $this->compilerFactory = $js;
    }

	public function run()
	{
		$this->getEventManager()->trigger('before.combine', $this, array());

        $files = $this->compilerFactory->getFiles();

        foreach($files as $f){
            $this->combinedContents .= file_get_contents($f);
        }

        $this->getEventManager()->trigger('after.combine', $this, array());

        return $this;
	}

    public function getCombinedContents()
    {
        return $this->combinedContents;
    }
}