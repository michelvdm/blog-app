<?php defined('BASE') or die('No access');

class BlogModel{

	function __construct( $db ){
		extract( $db );
		try{ $db=new PDO( "mysql:host=$host;dbname=$name;charset=utf8", $user, $password ); }
		catch( PDOException $e ){ die( 'Error: '.$e->getMessage() ); }
		$this->db=$db;
	}

	function getPagination( $currentPage, $perPage, $count ){
		if( !$currentPage )$currentPage=1;
		$numPages=ceil($count/$perPage);
		$offset=($currentPage-1)*$perPage;
		return array( 'count'=>$count, 'currentPage'=>$currentPage, 'numPages'=>$numPages, 'queryAdd'=>" LIMIT $offset, $perPage" );
	}

	function getPosts( $thisPage, $perPage=10 ){
		$count=$this->db->query( 'SELECT COUNT(*) FROM posts' )->fetch()[0];
		$pagination=$this->getPagination( $thisPage, $perPage, $count );
		$r=$this->db->query( 'SELECT slug, subject, publishdate, description, tags from posts ORDER BY publishdate DESC'.$pagination['queryAdd'] );
		return array( 'list'=>$r->fetchAll( PDO::FETCH_ASSOC ), 'pagination'=>$pagination );
	}

	function getTags(){
		$r=$this->db->query('SELECT LOWER(name) AS tag, name, count(*) AS count FROM tags GROUP BY tag ORDER BY name');
		return $r->fetchAll( PDO::FETCH_GROUP | PDO::FETCH_UNIQUE );
	}

	function getTagged( $tag, $thisPage, $perPage=10 ){
		$r=$this->db->prepare( 'SELECT COUNT(*) FROM tags WHERE LOWER(name)=:tag' );
		$r->execute( array( ':tag'=>$tag ) );
		$count=$r->fetch()[0];
		$pagination=$this->getPagination( $thisPage, $perPage, $count );
		$r=$this->db->prepare( 'SELECT slug, subject, publishdate, description, tags FROM tags a, posts b WHERE LOWER(name)=:tag AND a.postid=b.id ORDER BY publishdate DESC'.$pagination['queryAdd'] );
		$r->execute( array( ':tag'=>$tag ) );
		return array( 'list'=>$r->fetchAll( PDO::FETCH_ASSOC ), 'pagination'=>$pagination );	
	}

	function getSearch( $term, $thisPage, $perPage=10 ){
		$match='MATCH ( subject, description, body ) AGAINST ( :term IN BOOLEAN MODE )';
		$r=$this->db->prepare( 'SELECT COUNT(*) FROM posts WHERE '.$match );
		$r->execute( array( ':term'=>$term ) );
		$count=$r->fetch()[0];
		$pagination=$this->getPagination( $thisPage, $perPage, $count );
		$r=$this->db->prepare( 'SELECT slug, subject, publishdate, description, body, tags, '.$match.' AS score FROM posts WHERE '.$match.' ORDER BY score DESC'.$pagination['queryAdd'] );
		$r->execute( array( ':term'=>$term ) );
		return array( 'list'=>$r->fetchAll( PDO::FETCH_ASSOC ), 'pagination'=>$pagination );
	}

	function getPost( $slug ){
		$r=$this->db->prepare( 'SELECT * from posts WHERE slug=:slug' );
		$r->execute( array( ':slug'=>$slug ) );
		return $r->fetch( PDO::FETCH_ASSOC );	
	}

	function getPrevNext( $date ){
		$prevNext=array( 'prev'=>null, 'next'=>null );
		foreach( $prevNext as $key=>$value ){
			$r=$this->db->prepare('SELECT slug,subject FROM posts WHERE publishdate'.($key=='prev'?'<':'>').':date ORDER BY publishdate '.($key=='prev'?'DESC':'').' LIMIT 1');
			$r->execute( array( ':date'=>$date ) );
			$prevNext[ $key ]=$r->fetch(PDO::FETCH_ASSOC);
		}
		return $prevNext;		
	}

	function getPrevNextTagged( $date, $tag ){
		$prevNext=array( 'prev'=>null, 'next'=>null );
		foreach( $prevNext as $key=>$value ){
			$r=$this->db->prepare('SELECT slug,subject FROM tags a, posts b WHERE LOWER(name)=:tag AND a.postid=b.id AND publishdate'.($key=='prev'?'<':'>').':date ORDER BY publishdate '.($key=='prev'?'DESC':'').' LIMIT 1');
			$r->execute( array( ':tag'=>$tag, ':date'=>$date ) );
			$prevNext[ $key ]=$r->fetch(PDO::FETCH_ASSOC);
		}
		return $prevNext;
	}


}

