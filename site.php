<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="print.css" type="text/css" media="print" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
	<script src="js.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script type="text/javascript"> var RecaptchaOptions={theme : 'blackglass'};</script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.css" />

	<title>Gameplaza</title>
</head>
<body>
<div id="sitecontent">
<?php

/****************************VIEW**********************************/

session_start();

//error_reporting(0);


//menu bar

function navBar(){
	
	?>
<ul id="bar">
<li><a href="index.php?page=home">Home</a></li>
<li class="dropdown">
  <a class="dropbtn">List</a>
  <div class="dropdown-content">
		
		<?php if(!isset($_SESSION['user'])){print '<a href="index.php?page=login">Login</a>';}?>
		<?php if(isset($_SESSION['user'])){print '<a href="index.php?page=gamesearch">GameSearch</a>';}?>
		<a href="index.php?page=posts">Posts</a>
		<a href="index.php?page=register">Register</a>
		
  </div>
</li>
<?php  

//showing a link to logout if logged in.
if(isset($_SESSION['user'])){print '<li><a href="index.php?page=logout"> Logout: '.$_SESSION['user'].'</a></li>'; } ?>
</ul>
		

<?php
}

//display canvas (advertisement panel)
function canvas(){
?>
<div id="canvasAd">
<canvas id="ad" width="180" height="300" style="border:1px solid black"></canvas>
</div>
<?php
}

//display home view
function homeContent(){
?>
<h1>Welcome to Gameplaza, the generic gameforum site!</h1>
	   <p>Here you can post comments and reviews on games you like or hate! </p>
	   <p>Posts/Threads chart</p>
<?php	
}
//display gamesearch view
function getGameInfo(){
?>

<div class="form">
<form id="getGames">
	Search games:<input id = "game" type="text" name="search" />
	<input type="button" value="search" onclick="getXML();"/>
</form>
</div>

<table id="gameBox">
</table>

<script>

//ajax GETter. gets data from remote server using API(xml) and displays it in gameBox div
function getXML(){
	var game = document.getElementById("game").value;
	
	$.ajax({
			url: "datahandler.php",
			type: "GET",
			data:{
				"name": game
			},
			dataType: "html",
			success: function(data){
				$("#gameBox").html(data);
				
			}
		
	});
	
}
</script>

<?php	
}
//displays thread view when user is in thread -menu
function threads(){
?>
<div id="threads" >
	<script>
	
	//when website is loaded, gets updated thread list
	$(document).ready(function(){
		getThreads();
	});
	
	</script>
</div>

<form  class= "form">
<?php

//if logged in, shows posting form
if(isset($_SESSION['user'])){
echo '
	Make a thread: <input id="subject" type="text" name="subject" />
	<input type="button" value="post" onclick="makeThread();">';
}

?>

</form>

<script>

//change the page to selected(clicked) "thread" page
function goThread(event){

	window.location = "index.php?thread="+$(event.target).text();
	
}

//updates the thread menu through ajax
function getThreads(){
	
	$.ajax({
			url: "datahandler.php",
			type: "GET",
			data:{
				"thread":"threads"
			},
			dataType: "html",
			success: function(data){
				$("#threads").html(data);
				
			}
		
	});
}
</script>
<?php
}
function postThreads(){
?>
<p id="error" style="color:red;"></p>
<script>
//uses ajax to make a new thread
function makeThread(){
	var value = "set";
	var subject = document.getElementById("subject").value;
	
	$.ajax({
			url: "datahandler.php",
			type: "POST",
			data:{
				"post": value,
				"subject": subject,
				"poster":"<?php echo $_SESSION["user"]?>",
				"rights":<?php echo $_SESSION["rights"]?>
			},
			dataType: "html",
			success: function(data){
				$("#error").html("<?php if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']);} ?>");
				getThreads();
			}
		
	});
}
</script>
<?php
}
?>

<?php	

//displays the selected thread (posts and post form)
function posts(){
?>
<script>

//if download link clicked, makes a pdf from the html(stripped, only text), using jsPDF
function getPdf(){
	var pdf = new jsPDF();
	pdf.fromHTML($('#posts').get(0),20,20,{'width': 500});
	pdf.save("thread.pdf");
	}

</script>
<a href="javascript:getPdf()">Download Thread as PDF</a>

<div id="posts">
	<script>$(document).ready(function(){getPosts();});</script>	
</div>

<form id="postform" >
<?php

//if user is logged in, displays the posting form
if(isset($_SESSION['user'])){
echo '
	Make a post
	<textarea id="textA" rows="10" cols="50"></textarea>
	<input type="button" value="post" onclick="makePost();">';
}
?>
</form>

<script>

//updates the posts on the thread
function getPosts(){
	
	$.ajax({
			url: "datahandler.php",
			type: "GET",
			data:{
				"thrd": "<?php echo $_SESSION['thread'];?>",
				"posts": 'getPosts'
			},
			dataType: "html",
			success: function(data){
				$("#posts").html(data);
				
			}
		
	});
}

</script>

<?php 
}
//includes the ajax/jquery needed for posting

function makePosts(){	
?>
<script>
function makePost(){
	var value = document.getElementById("textA").value;
	
	$.ajax({
			url: "datahandler.php",
			type: "POST",
			data:{
				"post": value,
				"subject": 'post',
				"poster":"<?php echo $_SESSION['user']?>",
				"rights":<?php echo $_SESSION['rights']?>,
				"thread":"<?php echo $_SESSION['thread']?>"
			},
			dataType: "html",
			success: function(data){
				getPosts();
				
			}
		
	});
}
</script>
<?php	

	
}

//displays the login view
function login(){
?>				

<script type="text/javascript" src="/fblogin/fb.js"></script>
<?php  //if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']);}  ?>
<div id="login" class="form"><form id = "form" action="datahandler.php" method="post">
Username:<br />
		<input class = "textbox" name = "login" type="text" /><br />
Password:<br />
		<input class = "textbox" name = "pword" type="password" /><br />
		<?php //captcha
			  $publickey = "6Ld4ZQ4UAAAAAGs3QbB-vNY6dIE6h9al-WAX1VDF";
			  echo "<center>".recaptcha_get_html($publickey)."</center>";
		?>		
		<center><table>
		<tr><div id ="loginButton"><td><div class="fb-login-button" data-scope="public_profile,email" onlogin="checkLoginState();"></div></td>
		<td><input id = "loginButton" type = "submit"  value = "Login" /></td></div></tr></table>
		</center>
		
		</form>
</div>
<?php	
}

//displays register form, $set parameter selects which form gets displayed, the register or validation
function register($set){
	if($set){
		
//register form
?>
<div class="form">
		<form action="datahandler.php" method="post">
Username:<br />
		<input class = "textbox" name = "username" type="text" /><br />
Email:<br />
		<input class = "textbox" name = "email" type="text" /><br />
Password:<br />
		<input class = "textbox" name = "passwrd" type="password" /><br />
Password again:<br />
		<input class = "textbox" name = "passwrd2" type="password" /><br />
		<?php //captcha
			  $publickey = "6Ld4ZQ4UAAAAAGs3QbB-vNY6dIE6h9al-WAX1VDF";
			  echo "<center>".recaptcha_get_html($publickey)."</center>";
		?>
		
		<center><input id = "registerButton" type = "submit"  value = "Register" /></center>
		
		</form>
</div>

<?php
	}else{
		
//validation form
?>	
<div class="form">
	<form method = "post" action="datahandler.php">
	Enter Validation key:<input type="text" name="emailKey" />
	<input type="Submit" value="Submit" />
	</form>
</div>

<?php		
	}
}
//displays the dynamic svg chart.

function chart(){
?>
<div id = "chart" class="ct-chart"></div>
<?php 

?>

<script>

//chart gets input data from database (post count/threads)
//chart uses "chartist.js" lib
$(document).ready(function(){
	$.ajax({
			url: "datahandler.php",
			type: "GET",
			data:{
				"chartT": "joppa"
			},
			dataType: "html",
			success: function(data){
				if(data){
					setChart(1);
				}
			}
		
	});
	
function setChart(set){
	if(set){	
		var data = {
			//setting the labels, defining them with threads' names
		  labels: <?php 
		  $string ='';
		  if(isset($_SESSION['chart_threads'])){
			  for($i=0; $i<count($_SESSION['chart_threads']); $i++){
				  $string = $string."'".$_SESSION['chart_threads'][$i]."',";
				}
			  echo '['.rtrim($string,",").']';
			}else{
				//just a placeholder array, so the chart isn't empty if something goes wrong
				echo "[2,3,4]";}?>,
				
			//defining series aka the y-axis with postcounts
		  series: [
			<?php if(isset($_SESSION['chart_posts'])){
					print ($_SESSION['chart_posts']);
				  }else{
					  //placeholder
					  echo "['k','j','l']";}?>
		  ]
		};
		//extra options for the chart. Removing the padding.
		var options = {
		 
		  chartPadding: 0,
		  low: 0
		};
		//Making a chartist object and drawing the chart with it
		new Chartist.Line('.ct-chart', data, options);
	}
}	
});
	
</script>


<?php
}
?>
</div>
</body>
</html>