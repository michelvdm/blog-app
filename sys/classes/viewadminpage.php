<?php defined('BASE') or die('No access');

class ViewAdminPage extends ViewPage{

	function renderActions( $actions ){
		if( sizeof($actions)==0 ) return;
		out( '<div class="act">' );
		foreach ($actions as $item) out( '<a href="'.ROOT.$item['link'].'">'.$item['label'].'</a>' );
		out( '</div>' );
	}

	function startForm($action=''){ out( "<form method=\"post\" action=\"$action\">" ); }
	function hidden($name, $val){ out( "<input name=\"$name\" value=\"$val\" type=\"hidden\">" ); }
	function input($name, $label, $val='', $opt=''){ out( "<li><label for=\"f$name\">$label</label><input id=\"f$name\" name=\"$name\" value=\"$val\" $opt></li>" ); }
	function area($name, $label, $val='', $opt=''){ out( "<li><label for=\"f$name\">$label</label><textarea id=\"f$name\" name=\"$name\" $opt>$val</textarea></li>" ); }
	function body($name, $label, $val='', $opt=''){ out( "<li class=\"richtext\"><label for=\"f$name\">$label</label><textarea id=\"f$name\" name=\"$name\" $opt>$val</textarea></li>" ); }

	function select($name, $label, $enum, $val='', $opt=''){
		out( "<li><label for=\"f$name\">$label</label><select id=\"f$name\" name=\"$name\" $opt>" );
		foreach($enum as $p){
			$sel=($p==$val)?' selected':'';
			out( "<option value=\"$p\"$sel>$p</option>" );
		}
		out( '</select></li>' );
	}

	function endForm($back='', $label='Submit'){
		$cancel=($back=='')?'':"<a href=\"$back\">Cancel</a> ";
		out( "<div class=\"act\">$cancel<button type=\"submit\">$label</button></div>\n</form>" );
	}

	function renderNewPost(){
		$now=date_create()->format('Y-m-d H:i:s');
		$this->startForm();
		out( '<ul>' );
		$this->input('subject', 'Subject: ', '', 'autofocus required');
		$this->area('description', 'Description: ');
		$this->input('publishdate', 'Publish date: ', $now, 'type="datetime" required');
		$this->input('slug', 'Slug: ', $now.'-', 'required');
		$this->body('body', 'Body ');
		out( '</ul>' );
		$this->endForm(ROOT.'/');
	}

	function renderEditPost(){
		extract($this->data['content']);
		$this->startForm();
		$this->hidden('id', $id);
		out('<ul>' );
		$this->input('subject', 'Subject: ', $subject, 'autofocus required' );
		$this->area('description', 'Description: ', $description );
		$this->input('publishdate', 'Publish date: ', $publishdate, 'type="datetime" required');
		$this->input('slug', 'Slug: ', $slug, 'required');
		$this->body('body', 'Body ', $body );
		out( '</ul>' );
		$this->endForm(ROOT.'/post/'.$slug);
	}

	function renderDeletePost(){
		extract($this->data['content']);
		$this->startForm();
		echo '<p class="msg">', 'Are you sure you want to delete this post?', '</p>', PHP_EOL;
		$this->endForm(ROOT.'/post/'.$slug, 'OK');
	}

	function render(){
		extract( $this->data );
		$path=ROOT;
		$pageTitle=$app['title'];
		echo <<<EOT
<!DOCTYPE html><html lang="en"><meta charset="utf-8">
<title>$pageTitle Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="$path/inc/favicon.ico">
<link rel="stylesheet" href="$path/inc/style.css">
<link rel="stylesheet" href="$path/inc/admin.css">

EOT;
		if($type=='login'){
			echo <<<EOT
<div class="ovl"><form method="POST">
	<h2>Log in</h2>
	<ul>
		<li><label for="fUser">User: </label><input name="user" id="fUser" autofocus required></li> 
		<li><label for="fPassword">Password: </label><input name="password" id="fPassword" type="password" required></li> 
	</ul>
	<div class="act"><button type="submit">Submit</button></div>
</form></div>
EOT;
			die();
		}

		echo <<<EOT
<header><div>
	<a href="$path/" class="on">{$app['title']} Admin</a><a href="$preview$path/" target="preview">Preview</a>
	<div class="user">$user <a href="$path/logout">Log out</a></div>
</div></header>

<article>
$message
EOT;
		$this->renderActions( $actions );
		out( '<h1>'.$title.'</h1>' );
		$fn='render'.ucFirst($type);
		if( method_exists( __CLASS__, $fn ) ) call_user_func( array( $this, $fn ) );
		else out( 'Error - '.__CLASS__.' method does not exist: '.$fn );
		out( '</article>'.PHP_EOL );
		out( '<footer>Rendered in '.round( microtime( true )-START_TIME , 3 ).' sec.</footer>' );
	}

}
