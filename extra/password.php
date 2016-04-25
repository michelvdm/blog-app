<?php define('BASE',getcwd());

$path='/webdev';

if( strtolower ( $_SERVER['REQUEST_METHOD'] )=='post' ){
	$val=$_POST['password'];
	require( BASE.'/passwordhash.php' );
	$hash=new PasswordHash();
	die( '"'.$val.'" hashed is: <br>'.$hash->hashPassword( $val ) );
}

?>
<!DOCTYPE html><html lang="en"><meta charset="utf-8">
<title>Password Hash</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo $path; ?>/inc/favicon.ico">
<link rel="stylesheet" href="<?php echo $path; ?>/inc/style.css">
<link rel="stylesheet" href="<?php echo $path; ?>/inc/admin.css">
<header><div><nav><ul><li><a href="/webdev/" class="on"><svg><use xlink:href="<?php echo $path; ?>/inc/icons.svg#duck-icon"/></svg>Web Duck</a></li></ul></nav></div></header>
<article>
<form method="POST">
	<h2>Password Hash</h2>
	<ul><li><label for="fPassword">Password: </label><input name="password" id="fPassword" required></li></ul>
	<div class="act"><button type="submit">Submit</button></div>
</form>
</article>
