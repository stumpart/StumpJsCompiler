<?php
namespace StumpJsCompiler\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Http\PhpEnvironment\Request;
use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;

/**
 *
 * @author barringtonhenry
 *        
 */
class CompiledScript extends AbstractHelper
{

    /**
     * 
     * @var Zend\View\HelperPluginManager
     */
    protected $helperPlugin;
    
    /**
     * 
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    
    protected $renderer;

    
    function __construct (HelperPluginManager $m)
    {
        $this->helperPlugin = $m;
        $this->serviceLocator = $m->getServiceLocator();
        $this->renderer = new PhpRenderer();
    }
    
    public function __invoke($type)
    {
      $baseUrl = $this->serviceLocator->get( 'Request' )->getUri()->normalize();
      $files   = $this->serviceLocator->get('jscompiler')->gatherSrcFiles($type)->getFiles();
      $mappedFileTime = array_map("filemtime", $files);
      rsort($mappedFileTime);

      $this->renderer->headScript()->setFile(
            $baseUrl.'stumpjscompiler/'.$type.'_'.$mappedFileTime[0].'.js',
            'text/javascript'
       );       
       return $this->renderer->headScript();
    }
}

?>