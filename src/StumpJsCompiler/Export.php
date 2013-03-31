<?php

namespace StumpJsCompiler;

use StumpJsCompiler\Service\JsCompiler;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;

/**
 *
 * @author barringtonhenry
 *        
 */
class Export {
	
    const PRE_EXPORT = 'compilerexport.pre';
    const POST_EXPORT = 'compilerexport.post';
    
    /**
     * 
     * @var unknown_type
     */
    protected $contents;
    
    /**
     * 
     * @var unknown_type
     */
    protected $cacheLength;
    
    /**
     * 
     * @var unknown_type
     */
    protected $fileType;
    
    /**
     * 
     * @var unknown_type
     */
    protected $headers;
    
    /**
     * 
     * @var int
     */
    protected $LastModified;
    
    /**
     * 
     * @var Zend\EventManager\EventManager
     */
    protected $events;
    
    
    /**
     * 
     */
    function __construct() 
    {}
    
    /**
     * 
     * @param string $type
     */
    public function setContentType($type)
    {
        $this->fileType = $type;
    }
    
    /**
     * 
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }
    
    /**
     * 
     * @param int $len
     */
    public function setCacheLength($len)
    {
        $this->cacheLength = $len;
    }
    
    /**
     * 
     * @param mixed $lm
     */
    public function setLastModified($lm)
    {
        $this->LastModified = gmdate('D, d M Y H:i:s', (int)$lm).' GMT';
    }
    
    /**
     *
     * @param EventManagerInterface $events
     * @return
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
                __CLASS__,
                get_called_class(),
        ));
    
        $this->events = $events;
        return $this;
    }
    
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
    
    /**
     * Send the compiled results to the browser
     * 
     * @return null
     */
    public function send()
    {
        $this->getEventManager()->trigger(self::PRE_EXPORT, $this);
        $this->sendheaders();
        echo $this->contents;
        $this->getEventManager()->trigger(self::POST_EXPORT, $this);
    }

    /**
     * 
     * @return \StumpJsCompiler\Export
     */
    public function sendheaders()
    {	    
        foreach($this->headers as $key=>$value){
            if(is_int($key)){
                header( $value );
            }else{
                header( $key . ': ' . $value );
            }
        }
        
        return $this;
    }
    
    /**
     * Initializes defalt headers
     * 
     * @return \StumpJsCompiler\Export
     */
    public function initHeaders()
    {
        $this->headers = 
        array(
              'Expires'         => gmdate('D, d M Y H:i:s', time() + $this->cacheLength).' GMT',
              'Content-Type'    => $this->fileType,
              'Content-Length'  => strlen($this->contents),
              'Last-Modified'   => $this->LastModified,
              'Cache-Control'   => 'max-age='.$this->cacheLength.', must-revalidate',
              'ETag'            => sha1($this->LastModified)
             ); 

        return $this;
    }
    
    /**
     * Sets an header
     * 
     * @param string $name
     * @param string $value
     * @return \StumpJsCompiler\Export
     */
    public function setHeader($name, $value = null)
    {
        if($value == null){
            $this->headers[] = $name;
        }else{
            $name  = $this->_normalizeHeader($name);
            $value = (string) $value;
            $this->headers[$name] = $value;
        }
        
        return $this;
    }
    
    /**
     * Set a collection of headers
     * @param array $headers
     * 
     * @return \StumpJsCompiler\Export
     */
    public function setHeaders(array $headers)
    {
        foreach($headers as $k=>$h){
            $this->setHeader($k, $h);    
        }
        
        return $this;
    }
    
    /**
     * Taken form Zend Framework 1.1.12
     * Normalizes a header name to X-Capitalized-Names
     * 
     * @param string $name
     * @return string
     */
    protected function _normalizeHeader($name)
    {
        $filtered = str_replace(array('-', '_'), ' ', (string) $name);
        $filtered = ucwords(strtolower($filtered));
        $filtered = str_replace(' ', '-', $filtered);
        return $filtered;
    }
}

?>