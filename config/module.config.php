<?php
return array(
    /**
     * The javscript minifier executables and corresponsing classes/adapters
     * that wraps these executables. You can add your own executables and classes/adapters
     */
    'executables' => array(
            'yuicompressor'  => array(
                                 'file'    =>   'yuicompressor-2.4.7.jar',
                                 'class'   =>   'StumpJsCompiler\Compilers\YUICompressor'
                                ),
            'closure'        => array(
                                 'file'    =>   'compiler.jar',
                                 'class'   =>   'StumpJsCompiler\Compilers\GClosure'
                                ),
            'jsmin'          => array(
                                 'file'    =>   '',
                                 'class'   =>   ''
                                )
    ),
        
    /**
     * Sets of actions to be taken on the javascript file
     * You can add additional actions, but make sure the selected compiler executable
     * supports it or that you add that functionality in your adapter class
     */
    'actions' => array(
        'minifier'   => 'StumpJsCompiler\Channels\Minifier', //minify each javascript file
        'combiner'   => 'StumpJsCompiler\Channels\Combiner' //combine a list of javascript files into one
    ),
        
    'compiler' => array(
        'current'       => 'closure',//The current compiler to use for compilation
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
            'cache-lifetime'=>31356000 //
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