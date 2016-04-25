<?php defined('BASE') or die("Access denied");

date_default_timezone_set( 'Europe/Brussels' );

return array(
	'db'=>array(
		'name'=>'webdev', 
		'host'=>'localhost', 
		'user'=>'root', 
		'password'=>''
	),
	'app'=>array(
		'title'=>'The blog title', 
		'desc'=>'The blog description'
	),
	'adminUrl'=>'https://admin.url',
	'previewUrl'=>'http://127.0.0.1',
	'template'=>'template.html',
	'menu'=>array(
			'index'=>array('link'=>'/', 'label'=>'<svg><use xlink:href="{{path}}/inc/icons.svg#home-icon"/></svg>Home'),
			'about'=>array('link'=>'/page/about', 'label'=>'About')
		),
	'admin'=>array(
		'user'=>'yourmail.address@your-domain',
		'name'=>'Your Name',
		'password'=>'use extra/password.php to hash a password...'
		)
);

