<?php
	//simply just deletes the session. "logs out"
	define("WEBSITE","http://www.hessu.dev/");
	session_start();
	session_destroy();
	header('Location: '.WEBSITE.'index.php?page=login');

?>