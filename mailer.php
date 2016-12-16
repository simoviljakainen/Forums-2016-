<?php
	
function sendMail($email,$detail){

require 'class.phpmailer.php';
require 'class.smtp.php';

//email sender, uses PHPMailer lib to send emails

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;      for debugging
                        
$mail->isSMTP();            

//change this to ur smtp -server
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;         

//change these to your mail service's username and password                     
$mail->Username = "kimmokimmo72@gmail.com";                 
$mail->Password = "perse123";                           
$mail->SMTPSecure = "tls";                            
$mail->Port = 587;                                   
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
//From field doesn't work if you aren't using open smtp -server without authentication
$mail->From = "Gamerplaza@no-reply.com";
$mail->FromName = "Gamerplaza";

$mail->addAddress($email, $email);

$mail->isHTML(true);

$mail->Subject = "You are almost done!";
$mail->Body = "<p>".$detail."</p>";
$mail->AltBody = "";

if(!$mail->send()) 
{
    echo "Error: " . $mail->ErrorInfo;
} 
else 
{
    echo "Message sent";
}
}






?>