<?php
//FB-login


//doesn't work yet though, Logging in works but doesn't define Login variables.

print <<<FBLOGIN

<div id="fb-root"></div>
<script>
(function(d,s,id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if(d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src="//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appID=142738356209102";
	fjs.parentNode.insertBefore(js,fjs);
}(document,'script','facebook-jssdk'));
</script>

<div class="fb-login-button" data-max-rows="1" data-size="medium" data-show-faces="false" data-auto-logout-link="false"></div>
FBLOGIN
?>
