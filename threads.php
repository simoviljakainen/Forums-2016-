<?php
require_once("site.php");

threads();

if(isset($_SESSION['user'])){
	postThreads();
	
}

?>
