<?php
session_start();

//prints the panel if user is admin
if($_SESSION['rights']==3){
?>

<?php

print <<<PANEL
<div class="form" ><table><td><center><tr>
	Make a moderator:
	<form method="post" action="datahandler.php">
	<input type="text" name="userM">
	<input type="submit" value="submit" />
	</form>
	</tr></center><center><tr><br />
	Delete user:
	<form method="post" action="datahandler.php">
	<input type="text" name="userD">
	<input type="submit" value="submit" />
	</form>
</tr></center></td></table></div>
PANEL;

}


?>
