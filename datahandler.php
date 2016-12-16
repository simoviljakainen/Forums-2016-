<?php
//Handles all the database actions and information (model)

require_once('mailer.php');
require_once('u.php');
require_once('recaptchalib.php');
require_once("databaseHandler.php");
session_start();

//define this to match your website
define("WEBSITE","http://www.hessu.dev/");

/******************GET and POST request handler, calls the data functions and actions that are requested*********************/

//catch parse xml request
if(isset($_GET['name'])&&!empty($_GET['name'])){
	parseXML($_GET['name']);
	
}elseif(isset($_POST['emailKey'])&&!empty($_POST['emailKey'])){
	$memcache = connectMemcache();
	
	//gets the right validationkey from memcache
	$info = getMemcache($_SESSION['memUser'], $memcache);
	
	//checks if the given validationkey is valid and adds the user's details into database
	if($info===$_POST['emailKey']){
		$db = db();
		$details = getMemcache("details".$_SESSION['memUser'], $memcache);
		$userDetails = explode("|",$details);
		$pwHashnSalt = hashString($userDetails[2]);
		$db->writeDb("users", "username,email,phash,salt",
			$userDetails[0].",".$userDetails[1].",".$pwHashnSalt['pwhash'].",".$pwHashnSalt['salt']);
		$_SESSION['registered'] = "<center><p id="."message".">Your registeration is complete! You can now login</p></center>";
		header('Location: '.WEBSITE.'index.php?page=login');
	}else{
		$_SESSION['error']="invalid validationkey";
		header('Location: '.WEBSITE.'index.php?page=register');
	}
	
//login checker
}elseif($_SERVER['HTTP_REFERER']===WEBSITE.'index.php?page=login'&&!isset($_SESSION['user'])){
	 if(ctype_alnum($_POST['login'])&&isset($_POST['login'])&&!empty($_POST['login'])&&isset($_POST['pword'])&&!empty($_POST['pword'])){
		if(recaptcha()){
			
			//gets needed information from the db
			$db = db();
			$hash = $db->readDb('users','phash , rights', ' username= ',$_POST['login']);
			
			//check if the given password is correct
			if(password_verify($_POST['pword'],$hash[0]['phash'])){
				
				//defining the login session variables
				$_SESSION['user'] = $_POST['login'];
				$_SESSION['rights'] = $hash[0]['rights'];
				header('Location: '.WEBSITE.'index.php?page=home');
			}else{
				$_SESSION['error'] = "invalid username or password";
				header('Location: '.WEBSITE.'index.php?page=login');
			}
		}else{
			$_SESSION['error']="invalid captcha.";
			header('Location: '.WEBSITE.'index.php?page=login');
		}
	}else{
		$_SESSION['error'] = "invalid username or password";
		header('Location: '.WEBSITE.'index.php?page=login');
	}
//makes a new post
}elseif(isset($_POST['post'])&&$_POST['subject']=='post'&&isset($_POST['subject'])){
	$db = db();
	$post = $db->writeDb('thread_'.$_POST['thread'],'posts,user,rights', $_POST['post'].",".$_POST['poster'].",".$_POST['rights']);
	
//makes a new thread
}elseif(isset($_POST['post'])&&!empty($_POST['post'])&&isset($_POST['subject'])){
	if(checkThreadName($_POST['subject'])){
		$name = str_replace(" ","_",$_POST['subject']);
		$db = db();
		$db->createThreadTable($name);
	}else{
		$_SESSION['error']="Thread name has to be less than 15 letters and can only have numbers and letters";
	}
	
	
//gets all posts for the thread
}elseif(isset($_GET['posts'])&&$_GET['posts']=='getPosts'&&isset($_GET['thrd'])&&!empty($_GET['thrd'])){
	$db = db();
	$post = $db->readDb('thread_'.$_GET['thrd'],'*','','');
	$form = '';
	for($i=0; $i<count($post); $i++){
		if(isset($_SESSION['rights'])&&$_SESSION['rights']>1){
			
			//makes moderator/admin controls
			$form = "<form style="."float:right;"."><input id=".$i." type="."hidden "."value=".$post[$i]['id']
			." name="."id ".">"."<input type="."hidden"." value=".$_SESSION['thread']." name="."dThread "."><input type="
			."button"." value="."delete"." onclick = "."deleteusr(".$i.")"." /></form>";
		}
		print "<div class="."postrow"."><h4>".$post[$i]['created']."  ".$post[$i]['user']." said: </h4>".$form."<br />".$post[$i]['posts']."</div>";

	}
	
//gets needed data for the chart
}elseif($_SERVER['HTTP_REFERER']===WEBSITE.'index.php?page=home'&&$_GET['chartT']=='joppa'){
	$list = getChartPosts();
	$string = $_SESSION['chart_posts'] = "[".implode(",",$list)."]";
	$threads = getChartThreads();
	$_SESSION['chart_threads'] =$threads;
	print "1";
	
//makes a moderator
}elseif(isset($_SESSION['rights'])&&$_SESSION['rights']==3&&isset($_POST['userM'])){
	$db = db();
	$db->makeMod($_POST['userM']);
	header("Location ".WEBSITE."index.php?page=admin");
	
//deletes user
}elseif(isset($_SESSION['rights'])&&$_SESSION['rights']==3&&isset($_POST['userD'])){
	deleteUser($_POST['userD']);
	header("Location ".WEBSITE."index.php?page=admin");
	
//deletes a post
}elseif(isset($_SESSION['rights'])&&$_SESSION['rights']>1&&isset($_POST['id'])&&isset($_POST['dThread'])){
	deletePost($_POST['dThread'],$_POST['id']);
	
//gets threadlist 	
}elseif(isset($_GET['thread'])&&!empty($_GET['thread'])){
	getThreads();
	
//register checker
}elseif(isset($_POST['email'])&&isset($_POST['username'])&&isset($_POST['passwrd'])){
	if(!empty($_POST['email'])&&!empty($_POST['username'])&&!empty($_POST['passwrd'])){
		if(checkPw($_POST['passwrd'])){
			if(ctype_alnum($_POST['username'])){ 
				if(checkMatch($_POST['passwrd'],$_POST['passwrd2'])){
					if(checkEmail($_POST['email'])){
						if(checkUn($_POST['username'])){
							if(recaptcha()){
								$memcache = connectMemcache();
								
								//generates the random validation code
								$code = generateRand(20);
								
								//puts registering data to memcache, waiting for user to complete registration (email validation code)
								$_SESSION['memUser'] = $_POST['username'];
								setMemcache($_POST['username']."|".$_POST['email']."|".$_POST['passwrd'],$memcache,"details".$_POST['username']);
								setMemcache($code,$memcache,$_POST['username']);
								
								//sends email with the validation code
								sendMail($_POST['email'],"Your validation code is: ".$code);
								$_SESSION['set'] = 1;
								header('Location: '.WEBSITE.'index.php?page=register');
							}else{
								$_SESSION['error']="invalid captcha.";
								header('Location: '.WEBSITE.'index.php?page=register');
							}
						}else{
							$_SESSION['error']="Username is already in use.";
							header('Location: '.WEBSITE.'index.php?page=register');
						}
					}else{
						$_SESSION['error']="invalid email.";
						header('Location: '.WEBSITE.'index.php?page=register');
					}	
				  }else{
					  $_SESSION['error']="given passwords do not match.";
					  header('Location: '.WEBSITE.'index.php?page=register');
				  }	  
				}else{
					$_SESSION['error']="invalid input.";
					header('Location: '.WEBSITE.'index.php?page=register');
				}
		}else{
			$_SESSION['error']="password must be atleast 9 chars long, have numbers and lower and uppercase letters";
			header('Location: '.WEBSITE.'index.php?page=register');
		}
	}else{
		$_SESSION['error']="invalid input.";
		header('Location: '.WEBSITE.'index.php?page=register');
	}
}

/******************DATA FUNCTIONS*********************/

//checks if the captcha is correct
function recaptcha(){
		$privatekey = "6Ld4ZQ4UAAAAAC-tSJY_ltjzXggQEVxN_H0_umSM";
		$response = recaptcha_check_answer ($privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);
		
		if(!$response->is_valid){
			
			return 0;
		}else{
			return 1;
		}
}

//gets an instance from dbhandler (singleton)
function db(){
	$db = DbHandler::getInstance();
	
	return $db;
}

//checks if username is already registered
function checkUn($username){
	$db = db();
	$post = $db->readDb('users','*','username=',$username);
	print count($post);
	if(count($post)>0){
		return 0;
	}else{
		return 1;
	}
		
}
//makes memcache connection
function connectMemcache(){
	$memcache = new Memcache();
	$memcache->addServer('127.0.0.1', 11211) or die ("Could not connect");
	
	return $memcache;
}

//returns thread list for chart
function getChartThreads(){
	$db = db();
	$threads = $db->getThreadA();
	return $threads;
}
//returns post count for chart
function getChartPosts(){
	$db = db();
	$count = $db->getPostC();
	return $count;
}
//deletes a post
function deletePost($thread,$pId){
	$db = db();
	$db->dbDelete("thread_".$thread,"id",$pId);
}
//deletes a user
function deleteUser($username){
	$db = db();
	$db->dbDelete("users","username",$username);
	header("Location ".WEBSITE."index.php?page=admin");
}
//displays threads' names
function getThreads(){
	$db = db();
	$threads = $db->getThreads();
	print "<h3>Open threads: </h3><ul>";
	for($i=0; $i<count($threads); $i++){
		if(substr($threads[$i]['Tables_in_'.DATABASE],0,7)=='thread_'){
			
			print "<li class="."thread_list"." onclick="."goThread(event)".">".substr($threads[$i]['Tables_in_'.DATABASE],7)."</li><br />";
		}
	}
	print "</ul>";
}

//checks if the user or email is found from memcache
function checkMemcache($email,$username){
	$memcache = connectMemcache();
	if(getMemcache($email,$memcache)||getMemcache($username,$memcache)){
		return 0;
	}else{
		return 1;
	}
	
}

//sets given data into memcache
function setMemcache($value, $memcache, $user){
	if(!isset($memcache)){
		
	}else{
		$memcache->set($user, $value, false, 3600)or die ("Failed to save data at the server");
	}
}
//gets data from memcache
function getMemcache($value, $memcache){
	if(!isset($memcache)){
		
	}else{
		$mget = $memcache->get($value);
		return $mget;
	}
	
}
//parses the xml data (from api) for gamesearch 
function parseXML($game){
	$xml1=file_get_contents("http://thegamesdb.net/api/GetGamesList.php?name=".$game);
	$xml = simplexml_load_string($xml1);
	print "<tr><th>Game</th><th>Release date</th><th>Platform</th></tr>";
	$n=0;
	for($i=0;$i<$xml->count();$i++){
		if($n > 8){
			break;
		}
		echo "<tr class="."gamerows"."><td>".$xml->Game[$i]->GameTitle."</td><td>"
			 .$xml->Game[$i]->ReleaseDate."</td><td> "
			 .$xml->Game[$i]->Platform."</td></tr>";
		$n++;
	}
}


?>