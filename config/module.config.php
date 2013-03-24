<?php
return array(
    'executables' => array(
            'yuicompressor'=>'yuicompressor-2.4.7.jar',
            'closure'=>'compiler.jar',
            'jsmin'=>''
    ),
    'actions' => array(
        'minifier'=>'StumpJsCompiler\Channels\Minifier',
        'combiner'=>'StumpJsCompiler\Channels\Combiner'
    ),
	'compiler' => array(
		'current'=>'YUICompressor',
		'minify'=>true,
		'storageAdapter'=>'filesystem',
		'workareaDir'=> __DIR__ . '/../../../data'
	),
	'builds'=>array(
		'mine'=>array(
		        'files'=>array(
		                'js/prototype.js',
			            'js/jquery-1.9.1.js'
		                ),
		        'cache-lifetime'=>14400
		),
		'rhin'=>array()
	)
);