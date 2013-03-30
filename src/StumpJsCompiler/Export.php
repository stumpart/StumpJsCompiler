<?php

namespace StumpJsCompiler;

/**
 *
 * @author barringtonhenry
 *        
 */
class Export {
	
    protected $contents;
    
    protected $cacheLength;
    
    protected $fileType;
    
    protected $headers;
    
    protected $LastModified;
    
	/**
	 * 
	 */
	function __construct() 
	{}
	
	public function setContentType($type)
	{
	    $this->fileType = $type;
	}
	
	public function setContents($contents)
	{
	    $this->contents = $contents;
	}
	
	public function setCacheLength($len)
	{
	    $this->cacheLength = $len;
	}
	
	public function setLastModified($lm)
	{
	    $this->LastModified = gmdate('D, d M Y H:i:s', (int)$lm).' GMT';
	}
	
	public function send()
	{
	    foreach($this->headers as $key=>$value){
	        header($key . ': ' . $value);
	    }
	    
	    echo $this->contents;
	}
	
	public function initHeaders()
	{
	    $this->headers = 
	    array(
	          'Expires'         => gmdate('D, d M Y H:i:s', time() + $this->_cacheLength).' GMT',
	          'Content-Type'    => $this->fileType,
	          'Content-Length'  => strlen($this->contents),
	          'Last-Modified'   => $this->LastModified,
	          'Cache-Control'   => 'max-age='.$this->_cacheLength.', must-revalidate',
	          'ETag'            => sha1($this->LastModified)
	         );  
	}
	
	public function setHeader($name, $value)
	{
	    $name  = $this->_normalizeHeader($name);
	    $value = (string) $value;
	    $this->headers[$name] = $value;
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