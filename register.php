<?php
require_once('site.php');
require_once('recaptchalib.php');

canvas();

/*if validation code is set in the memcache ($_SESSION['set']), 
displays the validation form instead of register form*/

if(isset($_SESSION['set'])){
	register(0);
	unset($_SESSION['set']);
}else{
	register(1);
	if(isset($_SESSION['error'])){
		echo"<center><p style=".'color:red;'.">". $_SESSION['error']."</p></center>";
		unset($_SESSION['error']);
	}
	
}
?>