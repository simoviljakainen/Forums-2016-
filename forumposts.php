<?php
require_once('site.php');
posts();

if(isset($_SESSION['user'])){
	
	//if user is logged in (not a guest), displays the posting form
	makePosts();
	
	//gets moderator tools (delete post) if user is moderator or higher
	if($_SESSION['rights']>1){
		require_once('moderator.php');
	}
}


?>