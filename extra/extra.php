<?php defined('BASE') or die('No access');

function sanitizeFields( $obj ){ foreach( $obj as $key => $value ) $obj[$key]=is_array( $value )?sanitize_fields( $value ):htmlspecialchars( $value ); return $obj; }
function strLeft( $val, $to ){return substr($val, 0, strpos($val.$to, $to));}
function setFlashMessage( $val, $class='info' ){$_SESSION['flash_msg']=array( 'msg'=>$val, 'class'=>$class );}
function isImage($file){return in_array(strtolower( pathinfo($file,  PATHINFO_EXTENSION ) ), array('jpg','jpeg','gif','png') );}

/* use to obfuscate e-mail addresses (from: https://till.im/ ) */
function encodeStr($string){
	$chars=str_split($string);
	$seed=mt_rand( 0, (int) abs(crc32( $string )/strlen( $string ) ) );
	foreach( $chars as $key=>$char ){
		$ord=ord($char);
		if( $ord<128 ){
			$r=( $seed*( 1+$key ) )%100;
			if( $r>60 && $char!='@' );
			else if( $r<45) $chars[ $key ]='&#x'.dechex( $ord ).';';
			else $chars[ $key ]='&#'.$ord.';';
		}
	}
	return implode('', $chars);
}

/* get the age based on date of birth like 'yyy/mm/dd' */
function getAge( $dob ){return ( (new DateTime( 'today' ) )->diff( new DateTime( $dob ))->y ); }

// in a class method: echo the class & method name: 
echo __METHOD__, '()<br>';

// check for sufficient php version
if( version_compare(phpversion(), '5.5', '<' ) ) die( 'Error: PHP version has to be at least 5.5. ' );

// turn off PHP error messages (use on production environment only)
error_reporting(0);

function humanFilesize( $bytes, $decimals=2 ){
  $sz='BKMGTP';
  $factor=floor( (strlen($bytes)-1)/3 );
  return sprintf("%.{$decimals}f", $bytes/pow(1024, $factor)).@$sz[$factor];
}
