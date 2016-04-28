<?php defined('BASE') or die("Access denied");

date_default_timezone_set( 'Europe/Brussels' );

return array(
	'db'=>array(
		'name'=>'blogapp', 
		'host'=>'localhost', 
		'user'=>'root', 
		'password'=>''
	),
	'app'=>array(
		'title'=>'The blog title', 
		'desc'=>'The blog description'
	),
	'adminUrl'=>'http://wd-admin.dev',
	'previewUrl'=>'http://127.0.0.1',
	'template'=>'template.html',
	'menu'=>array(
			'index'=>array('link'=>'/', 'label'=>'<svg><use xlink:href="{{path}}/inc/icons.svg#home-icon"/></svg>Home'),
			'search'=>array('link'=>'/search', 'label'=>'Search'),
			'about'=>array('link'=>'/page/about', 'label'=>'About')
		),
	'admin'=>array(
		'user'=>'admin@your-domain',
		'name'=>'Admin',
		'password'=>'$2a$08$WH9gIwgF2PVONEwiW/fTGeWhJkYBuBer5fdeF4wkxBYOnQgX/OW7i'
		)
);

/* To use the admin pages for testing: 

modify the hosts file to create a virtual domain 'wd-admin.dev'

You can login to http://wd-admin.dev with: 
	user name: admin@your-domain
	password: password

To hash a new password, use extra/password.php

*/
