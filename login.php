<?php
require_once("site.php");
require_once('recaptchalib.php');

//prints a registered message for new user if he has just registered

if(isset($_SESSION['registered'])){
	print $_SESSION['registered'];
	unset($_SESSION['registered']);
}elseif(isset($_SESSION['error'])){
	echo "<center><p style=".'color:red;'.">". $_SESSION['error']."</p></center>";
	unset($_SESSION['error']);
}
canvas();

login();
?>






