<?php
namespace StumpJsCompiler;

use StumpJsCompiler\Service\JsCompiler;
/**
 *
 * @author barringtonhenry
 *        
 */
interface JsCompilerAwareInterface
{
    public function setJsCompiler(JsCompiler $j);
}

?>