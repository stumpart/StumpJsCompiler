<?php
return array(
    /**
     * The javscript minifier executables.
     */
    'executables' => array(
            'yuicompressor' => 'yuicompressor-2.4.7.jar',
            'closure'       => 'compiler.jar',
            'jsmin'         => ''
    ),
    /**
     * Sets of actions to be taken on the javascript
     */
    'actions' => array(
        'minifier'   => 'StumpJsCompiler\Channels\Minifier', //minify each javascript file
        'combiner'   => 'StumpJsCompiler\Channels\Combiner' //combine a list of javascript files into one
    ),
    /**
     * 
     */
	'compiler' => array(
		'current'       => 'yuicompressor',//The current the 
		'minify'        => true,//TODO remove
		'storageAdapter'=> 'filesystem',//can use the various caching mechanisms provided by the ZF2
		'workareaDir'   => __DIR__ . '/../../../data'
	),
    /**
     * A mapping of the builds that can be compiled.
     * Customize and replace to your own liking. Javascript files and values are
     * just place holders
     */
	'builds'=>array(
		'jstest1'=>array(
		        'files'=>array(
		                'js/prototype.js',
			            'js/jquery-1.9.1.js',
		                'js/foo.js'
		         ),
		        'cache-lifetime'=>31356000
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