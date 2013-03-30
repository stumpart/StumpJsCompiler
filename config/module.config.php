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
		'current'=>'googleclosure',
		'minify'=>true,
		'storageAdapter'=>'filesystem',
		'workareaDir'=> __DIR__ . '/../../../data'
	),
	'builds'=>array(
		'jstest1'=>array(
		        'files'=>array(
		                'js/prototype.js',
			            'js/jquery-1.9.1.js',
		                'js/foo.js'
		                ),
		        'cache-lifetime'=>14400
		),
		'jstest2'=>array(
		        'files'=>array(
		               'js/helloworld.js',
		               'js/bar.js'         
		         ),
		        'headers'=>array(
		                'X-Foo-Debug'=>md5("some hash"),
		                'X-JS-Bar'=>'somebar',
		                'X-Content-Type-Options'=>'nosniff'
		                )
		)
	)
);