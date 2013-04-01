<?php

namespace StumpJsCompiler;

use Zend\Cache\StorageFactory;

class Cache {
    
    /**
     * The cache type
     * 
     * @var string
     */
    protected $type;
    
    /**
     * @var Zend\Cache\Storage\StorageInterface
     */
    protected $cacheObj;
    
    /**
     * @var string
     */
    protected static $defaultStorage = 'filesystem';
    

    public function __construct($type = null)
    {
        $this->setCacheType( $type );
        $this->setCacheObj();
    }
    
    /**
     * 
     * @param string $type
     */
    public function setCacheType($type)
    {
        $this->type = $type;
    }
    /**
     * 
     * @return \StumpJsCompiler\Cache
     */
    public function setCacheObj()
    {
        $this->cacheObj = StorageFactory::factory(array(
             'adapter' => ($this->type !== null) ? $this->type : self::$defaultStorage,
             'plugins' => array(
                  'exception_handler' => array('throw_exceptions' => false),
              ),
        ));
        
        return $this;
    }
    /**
     * 
     * @param unknown_type $key
     */
    public function get($key)
    {
        return $this->cacheObj->getitem($key);
    }
    
    /**
     * Set the value of the 
     * 
     * @param string $key
     * @param mixed $item
     */
    public function set($key, $item)
    {
        $this->cacheObj->setItem($key, $item);
    }
    
    /**
     * The default storage engine
     * 
     * @param string $default
     */
    public static function setDefaultStorage($default)
    {
        self::$defaultStorage  = $default;
    }
}

?>