<?php

namespace StumpJsCompiler;

class Cache {
	protected $type;
	
	public function __construct()
	{
		
	}
	
	public function setCacheType($type)
	{
		$this->type = $type;
	}
}

?>