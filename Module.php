<?php
namespace StumpJsCompiler;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class Module implements ServiceProviderInterface
{
    
    public function controllersInit($controllerInstance, ControllerManager $controllerManager)
    {
        $this->injectControllerDependencies($controllerInstance, $controllerManager->getServiceLocator());
    }
    
    /**
     * 
     * @param DispatchableInterface $controller
     * @param ServiceLocatorInterface $serviceLocator
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
        return include __DIR__ . '/config/module.config.php';
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
}