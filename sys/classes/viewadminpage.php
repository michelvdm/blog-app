<?php defined('BASE') or die('No access');

class ViewAdminPage extends ViewPage{

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

	function renderActions( $actions ){
		if( sizeof($actions)==0 ) return;
		out( '<div class="act">' );
		foreach ($actions as $item) out( '<a href="'.ROOT.$item['link'].'">'.$item['label'].'</a>' );
		out( '</div>' );
	}

	function renderNewPost(){
		$now=date_create();
		$f=new WebForm();
		$f->start();
		out( '<ul>' );
		$f->input('subject', 'Subject: ', '', 'autofocus required');
		$f->area('description', 'Description: ');
		$f->selectTags( $this->data['tags'] );
		$f->input('publishdate', 'Publish date: ', $now->format('Y-m-d H:i:s'), 'type="datetime" required');
		$f->input('slug', 'Slug: ', $now->format('Y-m-d').'-', 'required');
		$f->body('body', 'Body ');
		out( '</ul>' );
		$f->end(ROOT.'/');
	}

	function renderEditPost(){
		extract($this->data['content']);
		$f=new WebForm();
		$f->start();
		$f->hidden('id', $id);
		out('<ul>' );
		$f->input('subject', 'Subject: ', $subject, 'autofocus required' );
		$f->area('description', 'Description: ', $description );
		$f->selectTags( $this->data['tags'], $tags );
		$f->input('publishdate', 'Publish date: ', $publishdate, 'type="datetime" required');
		$f->input('slug', 'Slug: ', $slug, 'required');
		$f->body('body', 'Body ', str_replace( '&', '&amp;', $body ) );
		out( '</ul>' );
		$f->end(ROOT.'/post/'.$slug);
	}

	function renderDeletePost(){
		extract($this->data['content']);
		$f=new WebForm();
		$f->start();
		echo '<p class="msg">', 'Are you sure you want to delete this post?', '</p>', PHP_EOL;
		$f->end(ROOT.'/post/'.$slug, 'OK');
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
				case 'topNav': $this->renderTopNav(); break;
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
				case 'appName': echo APPNAME; break;
				default: out( 'Error - no rule for key: '.$tmp2[0] ); 
			}
			echo $tmp2[1];
		}
	}

}
