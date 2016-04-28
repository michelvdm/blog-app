<?php defined('BASE') or die('No access');

class BlogController{

	function __construct($config){
		$this->config=$config;
		$request=filter_input( INPUT_GET, 'url', FILTER_SANITIZE_STRIPPED );
		$this->request=explode( '/', ($request==''?'index':$request).'////' );
		$here=$this->request[0];
		switch( $here ){
			case 'post': $here='index'; break;
			case 'page': $here=$this->request[1]; break;
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

	function getSearch(){
		$url=$_SERVER['REQUEST_URI'];
		$searchTerm=filter_var( substr( $url, stripos( $url.'?for=', '?for=' )+5 ), FILTER_SANITIZE_STRIPPED );
		$obj=( $searchTerm>'' )?$this->model->getSearch( $searchTerm, (int) $this->data['request'][2] ):array( 'list'=>array(), 'pagination'=>null );
		$this->extendData( array( 'type'=>'search', 'title'=>'Search', 'searchTerm'=>$searchTerm, 'content'=>$obj['list'], 'pagination'=>$obj['pagination']  ) );	
	}

	function getPost(){
		$slug=$this->request[1];
		$item=$this->model->getPost( $slug );
		if( !$item ) return $this->get404();
		$prevNext=$this->model->getPrevNext( $item['publishdate'] );
		$this->extendData( array( 'type'=>'post', 'title'=>$item['subject'], 'content'=>$item, 'prevNext'=>$prevNext ) );
	}

	function getPage(){
		$file=BASE.'/content/page_'.$this->request[1].'.php';
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
