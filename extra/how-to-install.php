<?php 
define( 'BASE', str_replace( DIRECTORY_SEPARATOR.'extra', '', getcwd() ) );
define( 'ROOT', str_replace( '/extra', '', dirname( $_SERVER[ 'PHP_SELF' ] ) ) );

$title='Blog App: How to install';

?><!DOCTYPE html><html lang="en"><meta charset="utf-8">
<title><?php echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo ROOT; ?>/inc/favicon.ico">
<link rel="stylesheet" href="<?php echo ROOT; ?>/inc/style.css">
<header><div><a href="" class="on">Blog App</a></div></header>
<article>

<h1><?php echo $title; ?></h1>

<p class="intro">This application is intended for users who already have experience in PHP, HTML, CSS and JavaScript. </p>
<p>To test locally, download and install XAMPP, then download the code from GitHub and put it in the <code>htdocs</code> directory of the local server. To edit the config files, use a text/code editor. </p>

<h2>Configuration steps</h2>
<ol>
<li>Use PhpMyAdmin to create a new MySql database. Remember the database host, user, password and db name. </li>
<li>In this database, import <code>extra/posts.sql</code> to create the 'posts' table.  </li>
<li>In <code>content/config.php</code>: fill in the correct field values. Note: you should have a separate domain for the Admin part, preferrably with SSL. On <code>localhost</code>, modify the <code>hosts</code> file to add an admin domain, e.g. <code>127.0.0.1 admin.dev</code>. The Admin password has to be hashed. You can use <code>extra/password.php</code> to generate a hashed password. </li>
<li>Edit <code>content/template.html</code>. You can add, change or delete the <code>&lt;header></code> links, add analytics code, etc. </li>
<li>Edit <code>content/page_about.html</code>. </li>
<li>Open the admin URL. You should be prompted with the admin user and password to log in. When logged in, test the preview link. When no errors occur, you can start by creating your first post. </li>
<li>You can change the <code>content/template.html</code> and <code>inc/style.css</code> to change the presentation of your blog. </li>
</ol>

<h2>Go live</h2>
<p>Before going live, check if everything is rendered correctly and that the social media links are correct. You should have the about page ready and at least 5 posts.</p>

<p>Important: in the <code>index.php</code>file, switch off the error reporting on live sites. </p>

</article>
