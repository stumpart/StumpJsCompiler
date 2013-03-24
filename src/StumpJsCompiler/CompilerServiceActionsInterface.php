<?php
namespace StumpJsCompiler;

/**
 *
 * @author barringtonhenry
 *        
 */
interface CompilerServiceActionsInterface
{
    function run();
    
    function getContents();
    
    function setContents($contents);
}

?>