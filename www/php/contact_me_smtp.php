<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("Exception.php");
include("PHPMailer.php");
include("SMTP.php");

if($_POST)
{
    $to_Email       = "halston@darkmoonhaunts.com"; // Replace with recipient email address
    $subject        = 'Message from DarkMoon '.$_SERVER['SERVER_NAME']; //Subject line for emails
    
    $host           = "darkmoonhaunts.com"; // Your SMTP server
    $username       = "halston@darkmoonhaunts.com"; // Your email address
    $password       = "JDwiwaves88!"; // Your email password
    $SMTPSecure     = "tls"; // For example, tls
    $port           = 587; // For TLS
    
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    
        //exit script outputting json data
        $output = json_encode(
        array(
            'type'=>'error', 
            'text' => 'Request must come from Ajax'
        ));
        
        die($output);
    } 
    
    //check $_POST vars are set, exit if any missing
    if(!isset($_POST["userName"]) || !isset($_POST["userEmail"]) || !isset($_POST["userMessage"]))
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Input fields are empty!'));
        die($output);
    }

    //Sanitize input data using PHP filter_var().
    $user_Name        = filter_var($_POST["userName"], FILTER_SANITIZE_STRING);
    $user_Email       = filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL);
    $user_Message     = filter_var($_POST["userMessage"], FILTER_SANITIZE_STRING);
    
    $user_Message = str_replace("\&#39;", "'", $user_Message);
    $user_Message = str_replace("&#39;", "'", $user_Message);
    
    //additional php validation
    if(strlen($user_Name)<4) // If length is less than 4 it will throw an HTTP error.
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Name is too short or empty!'));
        die($output);
    }
    if(!filter_var($user_Email, FILTER_VALIDATE_EMAIL)) //email validation
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Please enter a valid email!'));
        die($output);
    }
    if(strlen($user_Message)<5) //check emtpy message
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Message is too short.'));
        die($output);
    }
    

    $mail = new PHPMailer();

    $mail->IsSMTP(); 
    $mail->SMTPAuth = true;    
    $mail->Host = $host;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = $SMTPSecure;
    $mail->Port = $port;         
    $mail->setFrom($username);
    $mail->addReplyTo($user_Email);     
    $mail->AddAddress($to_Email);
    $mail->Subject = $subject;
    $mail->Body = $user_Message. "\r\n\n"  .'Name: '.$user_Name. "\r\n" .'Email: '.$user_Email;
    $mail->WordWrap = 200;
    $mail->IsHTML(false);

    if(!$mail->send()) {

        $output = json_encode(array('type'=>'error', 'text' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo));
        die($output);

    } else {
        $output = json_encode(array('type'=>'message', 'text' => 'Hi '.$user_Name .'! Thank you for your email. We will get back to you within 24 hours.'));
        die($output);
    }
    
}
?>