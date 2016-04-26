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

	function renderLogin(){
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

	function process( $val ){
		extract( $this->data );
		$tmp=explode( '{{', $val );
		echo $tmp[0];
		for( $i=1; $i< sizeof($tmp); $i++){
			$tmp2=explode( '}}', $tmp[ $i ] );
			switch($tmp2[0]){
				case 'path': echo ROOT; break;
				case 'renderTime': echo round( microtime( true )-START_TIME , 3 ); break;
				case 'pageTitle': echo $app['title'].' Admin'; break;
				case 'previewPath': echo $preview.ROOT; break; 
				case 'userName': echo $user; break; 
				case 'title': echo isset($title)?$title:'Error: title is missing'; break;
				case 'content': 
					if($type=='login') $this->renderLogin();
					else{
						out( '<article>' );
						out( $message );
						$this->renderActions( $actions );
						out( '<h1>'.$title.'</h1>' );
						$fn='render'.ucfirst($type);
						if( method_exists( __CLASS__,  $fn ) ) call_user_func( array( $this, $fn ) );
						else out( '<p>Error - no page method: '.$fn.'</p>' );
						out( '</article>' );
					}
					break;
				default: out( 'Error - no rule for key: '.$tmp2[0] ); 
			}
			echo $tmp2[1];
		}
	}

}
