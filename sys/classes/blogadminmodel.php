<?php defined('BASE') or die('No access');

class BlogAdminModel extends BlogModel{

	function createPost( $item ){
		extract( $item );
		$r=$this->db->prepare( 'INSERT INTO posts (subject, description, publishdate, slug, body) VALUES (:subject, :description, :publishdate, :slug, :body)' );
		$r->execute( array( ':subject'=>$subject, ':description'=>$description, ':publishdate'=>$publishdate, ':slug'=>$slug, ':body'=>$body ) );
	}

	function updatePost( $item ){
		extract( $item );
		$r=$this->db->prepare( 'UPDATE posts SET subject=:subject, description=:description, publishdate=:publishdate, slug=:slug, body=:body WHERE id=:id' );
		$r->execute( array( ':subject'=>$subject, ':description'=>$description, ':publishdate'=>$publishdate, ':slug'=>$slug,':body'=>$body,':id'=>$id ) );
	}

	function deletePost( $slug ){
		$r=$this->db->prepare( 'DELETE FROM posts WHERE slug=:slug' );
		return $r->execute( array( ':slug'=>$slug ) );
	}

}
