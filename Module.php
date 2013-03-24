<?php
namespace StumpJsCompiler;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;
use StumpJsCompiler\View\Helper\CompiledScript;

class Module implements ServiceProviderInterface
{
    /**
     * The priority in the mvc event route queue
     * 
     * @var int
     */
    public static $routePriority = 5; 
    
    protected $jsFileRegexPartial = "\/(?P<type>\w+)_(?P<timestamp>\d+)";
    
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    { 
        $em = $e->getApplication()->getEventManager();
        $em->attach(\Zend\Mvc\MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), self::$routePriority);
    }
    
    /**
     * Start before actual routing
     * 
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onRoute(\Zend\Mvc\MvcEvent $e)
    {
        $request = $e->getRequest();
        $path = $request->getRequestUri();
        $jsCompiler = $e->getApplication()->getServiceManager()->get("jscompiler");
        
        if(preg_match("/".__NAMESPACE__.$this->jsFileRegexPartial."/i", $path, $matches)){
            $jsCompiler->compile($matches['type'], $matches['timestamp']);
            
            exit;
        }
    }
    
    
    public function controllersInit($controllerInstance, ControllerManager $controllerManager)
    {
        $this->injectControllerDependencies($controllerInstance, $controllerManager->getServiceLocator());
    }
    
    /**
     * 
     * @param unknown_type $controller
     * @param ServiceManager $serviceLocator
     */
    public function injectControllerDependencies($controller, ServiceManager $serviceLocator)
    {
        if ($controller instanceof JsCompilerAwareInterface) {
            $controller->setJsCompiler($serviceLocator->get('jscompiler'));
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        	'Zend\Loader\ClassMapAutoloader' => array(
        		__DIR__ . '/autoload_classmap.php',
        	),
        );
    }

    public function getConfig()
    {
        return array_merge(include __DIR__ . '/config/module.config.php',
                            array('modulename'=>strtolower(__NAMESPACE__)));
    }

    public function getServiceConfig()
    {
        return array(
            'factories'=>array(
            	'jscompiler'=>'StumpJsCompiler\Service\JsCompiler'
            )
        );
    }
    
    public function getControllerConfig()
    {
        return array(
            'initializers'=>array(array($this, 'controllersInit'))
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
                'factories' => array(
                    'compiledScript' => function($sm) {
                        return new CompiledScript($sm);
                    },
                ),
        );
    }
}