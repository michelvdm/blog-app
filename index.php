<?php /* minimal Blog App in PHP */
define('START_TIME',microtime(true));
define('BASE',getcwd());
define( 'ROOT', dirname( $_SERVER[ 'PHP_SELF' ] )=='\\'?'':dirname( $_SERVER[ 'PHP_SELF' ] ) );

function out($val){ echo $val, PHP_EOL; }
function debug( $val, $label='Debug' ){require(BASE.'/sys/debug.php');}

error_reporting( 0 );
spl_autoload_register( function( $class, $data=null ){ require_once( str_replace( '\\', '/', BASE.'/sys/classes/'.strtolower( $class ).'.php' ) ); });
$config=require( BASE.'/content/config.php');
$isAdmin='http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER[ 'HTTP_HOST' ]==$config[ 'adminUrl' ];
$app=$isAdmin?'AdminController':'BlogController'; 
call_user_func( array( new $app( $config ), 'handleRequest' ) );
