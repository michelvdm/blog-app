<?php defined('BASE') or die('No access');

class AdminController extends BlogController{

	function __construct( $config ){
		parent::__construct( $config ); 
		$this->method=strtolower( $_SERVER['REQUEST_METHOD'] );
		session_set_cookie_params(60*60*24*365);
		session_start();
		$this->isLoggedIn=isset( $_SESSION[ 'userName' ] );
		$this->actions=array();
		extract($config);
		$this->model=new BlogAdminModel( $db );
		$this->data=array(  'app'=>$app,  'preview'=>$previewUrl, 'request'=>$this->request, 'user'=>$this->isLoggedIn?$_SESSION[ 'userName' ]:'' );
	}

	function goAndSay( $link, $text, $type='normal' ){ 
		$_SESSION[ 'message' ]=array( 'text'=>$text, 'type'=>$type ); 
		die( header('Location: '.ROOT.$link ) ); 
	}

	function getMessage(){
		$message='';
		if( isset( $_SESSION['message'] ) ){
			extract( $_SESSION['message'] );
			$message="<div class=\"msg $type\">$text</div>";
			unset($_SESSION['message']);
		}
		$this->data['message']=$message;
	}

	function addAction($link, $label){ $this->actions[]=array( 'link'=>$link, 'label'=>$label ); }
	function extendData( $arr ){ foreach ($arr as $key => $value) $this->data[$key]=$value; }
	function getLogin(){ $this->data['type']='login'; }
	function getLogout(){ session_destroy(); header('Location: '.ROOT.'/' ); }

	function postLogin(){
		extract($this->config['admin']);
		$hash=new PasswordHash();
		if( $user==$_POST['user'] && $hash->checkPassword( $_POST['password'], $password ) ){
			$_SESSION[ 'userName' ]=$name;
			$this->isLoggedIn=true;
			die ( header('Location: '.ROOT.'/' ) );
		}
		die('Login failed.');
	}

	function getIndex(){
		$obj=$this->model->getPosts( (int) $this->data['request'][1] );
		$this->addAction( '/newPost', 'New Post' );
		$this->extendData( array( 'type'=>'index', 'title'=>'Recent posts', 'content'	=>$obj['list'], 'pagination'=>$obj['pagination'] ) );
	}

	function getPost(){
		$slug=$this->data['request'][1];
		$item=$this->model->getPost( $slug );
		if( !$item ) die('Error - item not found: '.$slug);
		extract($item);
		$prevNext=$this->model->getPrevNext( $publishdate );
		$this->addAction( '/', 'Close' );
		$this->addAction( '/editPost/'.$slug, 'Edit' );
		$this->addAction( '/deletePost/'.$slug, 'Delete' );		
		$this->extendData( array( 'type'=>'post', 'title'=>$subject, 'content'=>$item, 'prevNext'=>$prevNext ) );
	}

	function getNewPost(){ $this->extendData( array( 'type'=>'newPost', 'title'=>'New post' ) ); }

	function postNewPost(){ 
		$this->model->createPost($_POST); 
		$this->goAndSay( '/post/'.$_POST['slug'], 'This post has been created.' ); 
	}

	function getEditPost(){ 
		$slug=$this->data['request'][1]; 
		$item=$this->model->getPost( $slug ); 
		$this->extendData( array( 'type'=>'editPost', 'title'=>$item['subject'].' (edit)', 'content'=>$item ) ); 
	}

	function postEditPost(){ 
		$this->model->updatePost($_POST); 
		$this->goAndSay('/post/'.$_POST['slug'], 'The post has been updated.' ); 
	}

	function getDeletePost(){ 
		$item=$this->model->getPost( $this->data['request'][1] ); 
		$this->extendData( array( 'type'=>'deletePost', 'title'=>'Delete post "'.$item['subject'].'"?', 'content'=>$item ) ); 
	}

	function postDeletePost(){ 
		$this->model->deletePost( $this->data['request'][1] ); 
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
		call_user_func( array( new ViewAdminPage( $this->data ), 'render' ) );
	}

}
