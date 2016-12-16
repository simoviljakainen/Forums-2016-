<?php
//checker and utils library 

//generates random sequence with given length
function generateRand($length){
	$sequence = openssl_random_pseudo_bytes($length);
	return bin2hex($sequence);
}
//hashes given string with bcrypt
function hashString($string){
	$values = ["cost" => 10, "salt" =>
        bin2hex(mcrypt_create_iv(20, MCRYPT_DEV_URANDOM))];

    $pwhash = password_hash($string, PASSWORD_BCRYPT, $values);
	$saltnHash = array( 'salt' => $values['salt'], 'pwhash' => $pwhash);
	return($saltnHash);
	
}
//checks thread's name
function checkThreadName($name){
	$trimmedName = str_replace(" ","",$name);
	if(ctype_alnum($trimmedName)&&strlen($trimmedName)< 15){
		return 1;
	}else{
		return 0;
	}
}
//checks if the 2 words match
function checkMatch($password, $password2){
	if($password == $password2){
		
		return 1;
	}else{
		return 0;
	}
	
}
//checks if the given string assembles an emailaddress
function checkEmail($email){
	if(preg_match('/[0-9a-zåäöA-ZÅÄÖ]+@[a-zåäöA-ZÅÄÖ]+\.[a-zåäöA-ZÅÄÖ]+/', $email)) {
        return 1;
    }else{
		return 0;
	}
}

//checks if the given password is over 8 chars, is alphanumeric, and isn't over 256 chars
function checkPw($password){
	$upper = false;
	$num = false;
	$lower = false;
	if(strlen($password)>8&&strlen($password)<256&&ctype_alnum($password)){
		
		foreach(str_split($password) as &$str){
			if(is_numeric($str)){
				$num = true;
			}else if(ctype_upper($str)){
				$upper = true;
			}else if(ctype_lower($str)){
				$lower = true;
			}
			if($upper&&$num&$lower){
				return true;
			}
		}
		return false;
	}else{
		return false;
	}
	
}
?>