<?php
/* ini_set('display_errors','On');
error_reporting(E_ALL | E_STRICT); */
	include '/var/www/html/PHPSCRIPTS/classes/class.phpmailer.php';

	$formData = json_decode($_POST['data']);
	
	/* If browser is IE, skip the captcha checking */
	if(isset($formData->ie))
	{
		$success = true;
	}
	else
	{
		$captcha = "";
		if(isset($formData->recaptcha))
		{
			$captcha = $formData->recaptcha;
		}
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LcUAAQTAAAAACB6FV1vIllOUvpAaTJ789xmxUx2&response=".$captcha);
		
		
		$response = json_decode($response);
		$success = $response->success;
	}
	if($success)
	{
		// Email for data collector
		$msg = "<p>Url at time of feedback submission: ".str_replace("&sect","&amp;sect",$formData->url)."</p>";
		$msg .= "<p>".$formData->text."</p>";
		// Create e-mail message for OTC Online using phpmailer
		$feedback_mail = new PHPMailer;
		$feedback_mail->From = $formData->email;
		$feedback_mail->FromName = "Enrollment Graph";
		$feedback_mail->AddAddress("web@otc.edu", "Web Services");
		$feedback_mail->WordWrap = 70;
		$feedback_mail->IsHTML(true);    // set email format to HTML
		$feedback_mail->Subject = "Canvas Feedback Form Submission";
		$feedback_mail->Body = $msg;
		$feedback_mail->Send();
		unset($feedback_mail);
		$new_response = array('status' => 'success', 'notice'=>'Thank you for your feedback!');
		print json_encode($new_response);
	}
	else
	{
		$new_response = array('status' => 'failed', 'notice'=>"Something went wrong. Please try sending your feedback again.");
		print json_encode($new_response);
	}
	exit();
?>