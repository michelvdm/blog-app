<?php define( 'START_TIME', microtime( true ) );

define( 'BASE', getcwd() );
define( 'ROOT', dirname( $_SERVER[ 'PHP_SELF' ] )=='\\'?'':dirname( $_SERVER[ 'PHP_SELF' ] ) );
define( 'APPNAME' , 'Minimal Blog App 1.4' );

function out($val){ echo $val, PHP_EOL; }
function debug( $val, $label='Debug' ){require(BASE.'/sys/debug.php');}

//ini_set( 'display_errors', 0 ); error_reporting( 0 );
ini_set( 'display_errors', 1 ); error_reporting( E_ALL );

spl_autoload_register( function( $class, $data=null ){ require_once( str_replace( '\\', '/', BASE.'/sys/classes/'.strtolower( $class ).'.php' ) ); });

$config=require( BASE.'/content/config.php' );
$app=( 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER[ 'HTTP_HOST' ]==$config[ 'adminUrl' ] )?'AdminController':'BlogController'; 
call_user_func( array( new $app( $config ), 'handleRequest' ) );
