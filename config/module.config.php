<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
	'compiler' => array(
		'current'=>'YUICompressor',
		'minify'=>true,
		'storageAdapter'=>'filesystem',
		'workareaDir'=> __DIR__ . '/../../../data'
	),
	'files' => array(
		'yuicompressor'=>'yuicompressor-2.4.7.jar',
		'closure'=>'compiler.jar',
		'jsmin'=>''
	),
	'builds'=>array(
		'mine'=>array(
			'js/prototype.js',
			'js/jquery-1.9.1.js',
		),
		'rhin'=>array()
	)
);