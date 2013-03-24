<?php

namespace StumpJsCompiler;

use Zend\Cache\StorageFactory;

class Cache {
    protected $type;
    /**
     * 
     * @var Zend\Cache\Storage\StorageInterface
     */
    protected $cacheObj;
    
    protected static $defaultStorage = 'filesystem';
    
    public function __construct($type = null)
    {
        $this->setCacheType( $type );
        $this->setCacheObj();
    }

    public function setCacheType($type)
    {
        $this->type = $type;
    }

    public function setCacheObj()
    {
        $this->cacheObj = StorageFactory::factory(array(
             'adapter' => ($this->type !== null) ? $this->type : self::$defaultStorage,
             'plugins' => array(
                  'exception_handler' => array('throw_exceptions' => false),
              ),
        ));
	}
	
	public function get($key)
	{
        return $this->cacheObj->getitem($key);
	}
	
	public function set($key, $item)
	{
	    $this->cacheObj->setItem($key, $item);
	}
	
	public static function setDefaultStorage($default)
	{
	    self::$defaultStorage  = $default;
	}
}

?>