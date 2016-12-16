<?php
/************************CONTROLLER******************************/

//getting the view html
require_once("site.php");

//printing navigation bar
navBar();

//handling navigation through server (not showing the user direct php files.)

if($_SERVER['REQUEST_URI']=='/'){
	header("Location: index.php?page=home");
}
if(isset($_GET["page"])&&$_GET["page"]==="login"&&!isset($_SESSION['user'])){
	require("login.php");
	
}elseif(isset($_GET["page"])&&$_GET["page"]==="register"){
	require("register.php");
	
}elseif(isset($_GET["page"])&&$_GET["page"]==="gamesearch"&&isset($_SESSION['user'])){
	require("gamesearch.php");
	
}elseif(isset($_GET["page"])&&$_GET["page"]==="logout"&&isset($_SESSION['user'])){
	require("logout.php");
	
}elseif(isset($_GET["page"])&&$_GET["page"]==="posts"){
	require("threads.php");
	
}elseif(isset($_GET["page"])&&isset($_SESSION['rights'])&&$_SESSION['rights']==3&&$_GET["page"]==="admin"){
	require("adminpanel.php");
	
}elseif(isset($_GET["thread"])){
	$_SESSION['thread']=$_GET["thread"];
	require("forumposts.php");
	
}else{
	require("home.php");
	
}





?>