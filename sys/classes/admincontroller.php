<?php defined('BASE') or die('No access');

class AdminController extends BlogController{

	function __construct( $config ){
		parent::__construct( $config ); 
		$this->method=strtolower( $_SERVER['REQUEST_METHOD'] );
		$here=$this->request[0];
		switch( $here ){
			case 'post': $here='index'; break;
			case 'page': $here=$this->request[1]; break;
			case 'tag': $here='tags'; break;
		}
		foreach( $_POST as $key=>$value ) if($key!='body' && !is_array($value) )$_POST[$key]=filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRIPPED );
		session_set_cookie_params(60*60*24*365);
		session_start();
		$this->isLoggedIn=isset( $_SESSION[ 'userName' ] );
		$this->actions=array();
		extract($config);
		$this->model=new BlogAdminModel( $db );
		$this->data=array( 
			'app'=>$app,  
			'preview'=>$previewUrl, 
			'request'=>$this->request, 
			'menu'=>$menu, 
			'topActive'=>$here,
			'user'=>$this->isLoggedIn?$_SESSION[ 'userName' ]:'' 
		);
	}

	function goAndSay( $link, $text, $type='normal' ){ 
		$_SESSION[ 'message' ]=array( 'text'=>$text, 'type'=>$type ); 
		die( header('Location: '.ROOT.$link ) ); 
	}

	function getMessage(){
		$key='message';
		$value='';
		if( isset( $_SESSION[ $key ] ) ){
			extract( $_SESSION[ $key ] );
			$value="<div class=\"msg $type\">$text</div>";
			unset( $_SESSION[ $key ] );
		}
		$this->data[ $key ]=$value;
	}

	function addAction($link, $label){ $this->actions[]=array( 'link'=>$link, 'label'=>$label ); }
	function extendData( $arr ){ foreach( $arr as $key=>$value ) $this->data[ $key ]=$value; }
	function getLogin(){ $this->data['type']='login'; }
	function getLogout(){ session_destroy(); header('Location: '.ROOT.'/' ); }

	function postLogin(){
		extract( $this->config[ 'admin' ] );
		$hash=new PasswordHash();
		if( $user==$_POST['user'] && $hash->checkPassword( $_POST['password'], $password ) ){
			$_SESSION[ 'userName' ]=$name;
			$this->isLoggedIn=true;
			die ( header('Location: '.ROOT.'/' ) );
		}
		die('Login failed.');
	}

	function getIndex(){
		$obj=$this->model->getPosts( (int) $this->request[1] );
		$this->addAction( '/newPost', 'New Post' );
		$this->extendData( array( 'type'=>'index', 'title'=>'Recent posts', 'content'	=>$obj['list'], 'pagination'=>$obj['pagination'] ) );
	}

	function getPost(){
		$slug=$this->request[1];
		$item=$this->model->getPost( $slug );
		if( !$item ) die('Error - item not found: '.$slug);
		extract($item);
		$prevNext=$this->model->getPrevNext( $publishdate );
		$this->addAction( '/', 'Close' );
		$this->addAction( '/editPost/'.$slug, 'Edit' );
		$this->addAction( '/deletePost/'.$slug, 'Delete' );		
		$this->extendData( array( 'type'=>'post', 'title'=>$subject, 'content'=>$item, 'prevNext'=>$prevNext ) );
	}

	function getTaggedPost( $tag ){
		$slug=$this->request[3];
		$item=$this->model->getPost( $slug );
		if( !$item ) die('Error - item not found: '.$slug);
		extract($item);
		$prevNext=$this->model->getPrevNext( $publishdate );
		$this->addAction( '/', 'Close' );
		$this->addAction( '/editPost/'.$slug, 'Edit' );
		$this->addAction( '/deletePost/'.$slug, 'Delete' );		
		$this->extendData( array( 'type'=>'post', 'title'=>$subject, 'content'=>$item, 'prevNext'=>$prevNext ) );
	}

	function getNewPost(){ 
		$this->extendData( array( 
			'type'=>'newPost', 
			'title'=>'New post', 
			'tags'=>$this->model->getTags() 
		) ); 
	}

	function postNewPost(){ 
		$this->model->createPost( $_POST ); 
		$this->goAndSay( '/post/'.$_POST[ 'slug' ], 'This post has been created.' ); 
	}

	function getEditPost(){ 
		$item=$this->model->getPost( $this->request[1] ); 
		$this->extendData( array( 
			'type'=>'editPost', 
			'title'=>$item['subject'].' (edit)', 
			'content'=>$item,
			'tags'=>$this->model->getTags()  
		) ); 
	}

	function postEditPost(){ 
		$this->model->updatePost( $_POST ); 
		$this->goAndSay('/post/'.$_POST['slug'], 'The post has been updated.' ); 
	}

	function getDeletePost(){ 
		$item=$this->model->getPost( $this->request[1] ); 
		$this->extendData( array( 'type'=>'deletePost', 'title'=>'Delete post "'.$item['subject'].'"?', 'content'=>$item ) ); 
	}

	function postDeletePost(){ 
		$this->model->deletePost( $this->request[1] ); 
		$this->goAndSay( '/', 'The post has been deleted.' ); 
	}

	function handleRequest(){
		$top=$this->request[0];
		$fn=$this->method.ucfirst( $this->isLoggedIn?$top:'login' );
		if( method_exists( __CLASS__, $fn ) ) call_user_func( array( $this, $fn ) );
		else die( 'Error - '.__CLASS__.' method does not exist: '.$fn );
		if( $this->method!='get' ) return;
		$this->data['actions']=$this->actions;
		$this->getMessage();
		call_user_func( array( new ViewAdminPage( $this->data ), 'render' ), 'admintemplate.html' );
	}

}
