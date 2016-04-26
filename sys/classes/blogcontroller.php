<?php defined('BASE') or die('No access');

class BlogController{

	function __construct($config){
		$this->config=$config;
		$this->request=explode( '/', ( isset( $_GET['url'] )?$_GET['url']:'index' ).'////' );
		switch( $this->request[0] ){
			case 'index': case 'post': $here='index'; break;
			default: $here='about';
		}
		extract($config);
		$this->data=array( 'app'=>$app, 'request'=>$this->request, 'template'=>$template, 'menu'=>$menu, 'topActive'=>$here );
		$this->model=new BlogModel( $db );
	}

	function extendData( $arr ){ foreach ($arr as $key => $value) $this->data[$key]=$value; }

	function getIndex(){
		$obj=$this->model->getPosts( (int) $this->data['request'][1] );
		$this->extendData( array( 'type'=>'index', 'title'=>'Recent posts', 'content'=>$obj['list'], 'pagination'=>$obj['pagination'] ) );
	}

	function getPost(){
		$slug=$this->data['request'][1];
		$item=$this->model->getPost( $slug );
		if( !$item ) return $this->get404();
		$prevNext=$this->model->getPrevNext( $item['publishdate'] );
		$this->extendData( array( 'type'=>'post', 'title'=>$item['subject'], 'content'=>$item, 'prevNext'=>$prevNext ) );
	}

	function getPage(){
		$file=BASE.'/content/page_'.$this->data['request'][1].'.php';
		if( !file_exists($file) )return $this->get404();
		$item=require($file);
		$this->extendData( array( 'type'=>'page', 'title'=>$item['subject'], 'content'=>$item['body'] ) );
	}

	function get404(){ $this->extendData( array( 'type'=>'404', 'title'=>'404 error', 'content'=>'<p>The page you requested could not been found. </p>' ) ); }

	function handleRequest(){
		$fn='get'.ucfirst( $this->request[0] );
		call_user_func( array( $this, method_exists( __CLASS__, $fn )?$fn:'get404' ) );
		call_user_func( array( new ViewPage( $this->data ), 'render' ), $this->data['template'] );
	}

}
