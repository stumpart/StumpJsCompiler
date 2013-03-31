<?php

namespace StumpJsCompiler\Channels;

use StumpJsCompiler\Channels\IMinify;
use StumpJsCompiler\Service\JsCompiler;

class Combiner extends AChannel {

    protected $files;

    protected $destination;


    public function __construct(JsCompiler $js)
    {
        $this->compilerFactory = $js;
    }

    public function run()
    {
        $this->getEventManager()->trigger('before.combine', $this, array());
        $combinedResults = "";
        
        if(is_array($this->contents) && !empty($this->contents)){
            foreach($this->contents as $f){
                if(file_exists($f)){
                    $combinedResults .= file_get_contents($f);
                }
            }   
        }
    
        $this->getEventManager()->trigger('after.combine', $this, array());
    
        return $combinedResults;
    }
}