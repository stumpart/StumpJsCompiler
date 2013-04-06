StumpJsCompiler
===============
Version 0.0.1 Created by Barrington Henry

Introduction
---------------

Zend Framework 2 module that will combine and minify javascript files. Retrieves from cache on subsequent requests
(Still in development)

Installation
------------

First, add the following line into your `composer.json` file:

```json
"require": {
    "stumpart/stumpjs-compiler": "dev-master"
}
```

and then run 

```json
    php composer.phar update
```

Then, enable the module by adding `StumpJsCompiler` in your application.config.php file.

```php
<?php
return array(
    'modules' => array(
        'Application',
        'StumpJsCompiler',
    ),
);
```

Requirements
------------

- Let the webserver user have write access to the application's data folder.
- Ensure that the Java Virtual Machine is installed on your web server. The compiler executables are jar files
  that will need this to execute

Configuration
-------------

The configurations need to get StumpjsCompiler up and running. This file is the module config file
config/module.config.php

```php
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

Definitions
cache-lifetime - The time in seconds that states how long the browser should cache the compiled javascript file
files          - The list of javascript files to include in the compilation
headers        - Additional headers that you may wish to send in the response. This will be added to the 
                 default list of headers.

```

## Usage

With this module installed and configured, using StumpjsCompiler in your view scripts is easy:
'compiledScript' is a view helper that will enable the user to enter a build name, eg. 'jstest1', which 
will then map to the 'jstest1' under 'builds' in the module.config.php file. 

```php
<?php echo $this->compiledScript('jstest1');?>
```

This view helper call can be placed anywhere in ur view script in the head or body tag.




