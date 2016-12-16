<?php

if($_SESSION['rights']>1){


?>

<script>
// if the user is atleast moderator, this includes an ajax POSTer for deleting posts

function deleteusr(i){
	var id = document.getElementById(i).value;
	$.ajax({
			url: "datahandler.php",
			type: "POST",
			data:{
				"id": id,
				"dThread":"<?php echo $_SESSION['thread']?>"
				
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
?>