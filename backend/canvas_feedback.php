<?php
ini_set('display_errors','On');
error_reporting(E_ALL | E_STRICT);
	include("/var/www/html/FORMS/class.phpmailer.php");
	//var_dump($_POST);
	$captcha = "";
	if(isset($_POST['g-recaptcha-response']))
	{
		$captcha = trim($_POST['g-recaptcha-response']);
	}
	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcUAAQTAAAAACB6FV1vIllOUvpAaTJ789xmxUx2&response=".$captcha);
	//print($response);
	
	//$msg = "<p>".$isValid."</p>";
	$msg = "";
	$response = json_decode($response);
	$success = $response->success;
	//print_r($_POST);
	if($success)
	{
		// Email for data collector
		$msg .= $_POST['text'];
		
		// Create e-mail message for OTC Online using phpmailer
		$feedback_mail = new phpmailer;
		$feedback_mail->From = $formData->email;
		$feedback_mail->FromName = $formData->firstName . " " . $formData->lastName;
		$feedback_mail->AddAddress("wrighta@otc.edu", "Aaron Wright");
		$feedback_mail->WordWrap = 70;
		$feedback_mail->IsHTML(true);    // set email format to HTML
		$feedback_mail->Subject = "Success Coach Application Submission ";
		$feedback_mail->Body = $msg;
		$feedback_mail->Send();
		unset($feedback_mail);
		header("Location: /confirmation.php");
	}
	else
	{
		print("<h3>Something went wrong.</h3><p>The captcha was not solved correctly. Please use your browser's back button to return to the form and try again.'</p>");
	}
?>