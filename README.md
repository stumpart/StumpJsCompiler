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
    "stumpart/stumpjs-compiler": ">=0.0.1"
}
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

Configuration
-------------

The configurations need to get StumpjsCompiler up and running. This file is the 

```php
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

Definitions
cache-lifetime - The time in seconds that states how long the browser should cache the compiled javascript file
files          - The list of javascript files to include in the compilation
headers        - Additional headers that you may wish to send in the response

```

## Usage

With this module installed and configured, using StumpjsCompiler in your view scripts is easy:
'compiledScript' is a view helper that will enable the user to enter a build name, eg. 'jstest1', which 
will then map to the 'jstest1' under 'builds' in the module.config.php file.

```php
<?php echo $this->compiledScript('jstest1');?>
```




