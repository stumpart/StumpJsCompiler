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
        //minify each javascript file
        'minifier'   => 'StumpJsCompiler\Channels\Minifier',
        
        //combine a list of javascript files into one
        'combiner'   => 'StumpJsCompiler\Channels\Combiner'
    ),
        
    'compiler' => array(
         //The current compiler to use for compilation
        'current'       => 'closure',
         
        //can use the various caching mechanisms provided by the ZF2
        'storageAdapter'=> 'filesystem',
        
        //work area for the jscompiler module, will create a StumpJsCompiler directory
        //where it stores temporary files for updating. So the web server needs to have write permissions
        //if no permissions, will use the /tmp/ directory and leave a notice in the log files
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