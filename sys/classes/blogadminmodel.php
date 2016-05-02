<?php defined('BASE') or die('No access');

class BlogAdminModel extends BlogModel{

	function createPost( $item ){
		extract( $item );
		$r=$this->db->prepare( 'INSERT INTO posts (subject, description, publishdate, slug, body) VALUES (:subject, :description, :publishdate, :slug, :body)' );
		$r->execute( array( ':subject'=>$subject, ':description'=>$description, ':publishdate'=>$publishdate, ':slug'=>$slug, ':body'=>$body ) );
		$id=$this->db->lastInsertId();
		$tags=$this->updateTags( $id, isset($tags)?$tags:array(), $newTags );
		$r=$this->db->prepare( 'UPDATE posts SET tags=:tags WHERE id=:id' );
		$r->execute( array( ':id'=>$id, ':tags'=>$tags ) );
	}

	function updateTags( $id, $tags, $newTags ){
		$tags=array_unique( array_filter( array_merge( $tags, explode(', ', $newTags) ) ) );
		$this->db->query( 'DELETE FROM tags WHERE postid='.$id );
		foreach ($tags as $item) $this->db->query( "INSERT INTO tags (name, postid) VALUES ('$item', $id) " );
		return implode(', ', $tags); 
	}

	function updatePost( $item ){
		extract( $item );
		$tags=$this->updateTags( $id, isset($tags)?$tags:array(), $newTags );
		$r=$this->db->prepare( 'UPDATE posts SET subject=:subject, description=:description, publishdate=:publishdate, slug=:slug, body=:body, tags=:tags WHERE id=:id' );
		$r->execute( array( 
			':subject'=>$subject, 
			':description'=>$description, 
			':publishdate'=>$publishdate, 
			':slug'=>$slug, 
			':body'=>$body, 
			':id'=>$id, 
			':tags'=>$tags
		) );
	}

	function getPostById( $id ){
		$r=$this->db->prepare( 'SELECT * from posts WHERE id=:id' );
		$r->execute( array( ':id'=>$id ) );
		return $r->fetch( PDO::FETCH_ASSOC );	
	}

	function deletePost( $id ){
		if( $id*1==0 ) die( 'Error: not a valid id: '.$id );
		$this->db->query( 'DELETE FROM tags WHERE postid='.$id );
		$r=$this->db->prepare( 'DELETE FROM posts WHERE id=:id' );
		return $r->execute( array( ':id'=>$id ) );
	}

}
