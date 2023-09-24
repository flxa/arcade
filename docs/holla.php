<?php
date_default_timezone_set('Australia/Sydney');
$from 	= "noreply@arcadestudios.com.au";
$to 	= "dean@arcadestudios.com.au";
$subject = 'A message from: ';
$feedback = "We received your request.\r\n";
$snd = true;
// Validate the Name field
if ( isset($_REQUEST['contactName']) && strlen(trim($_REQUEST['contactName']))>0 ) {
	$contactName = $_REQUEST['contactName'];
	$subject .= $contactName;
} else {
	$feedback .= "Please enter a contact name!\r\n";
	$snd = false;
	$fields[] = 'contactName';
}
// Validate the Email field
if ( isset($_REQUEST['contactEmail']) && strlen(trim($_REQUEST['contactEmail']))>0 ) {
	$contactEmail = $_REQUEST['contactEmail'];
	$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
	if (preg_match($pattern, $contactEmail) === 1) {
		// valid
	} else {
		$feedback .= "Please enter a valid email address!\r\n";
		$snd = false;
		$fields[] = 'contactEmail';
	}
} else {
	$feedback .= "Please enter an email address!\r\n";
	$snd = false;
	$fields[] = 'contactEmail';
}
// Validate the Message field
if ( isset($_REQUEST['contactMessage']) && strlen(trim($_REQUEST['contactMessage']))>0 ) {
	$contactMessage = $_REQUEST['contactMessage'];
} else {
	$feedback .= "Please type a message!\r\n";
	$snd = false;
	$fields[] = 'contactMessage';
}
$contactPhone = $_REQUEST['contactPhone'];
// Load the template and replace the variables
if ($snd) {
	$file 	= "email.html";
	$handle = fopen("$file", "r");
	$html 	= fread($handle, filesize($file));
	$html 	= str_replace("{{name}}", $contactName, $html);
	$html 	= str_replace("{{email}}", $contactEmail, $html);
	$html 	= str_replace("{{phone}}", $contactPhone, $html);
	$html 	= str_replace("{{date}}", date('jS F g:ia',strtotime('now')), $html);
	$html 	= str_replace("{{message}}", $contactMessage, $html);
	$html 	= str_replace("{{subject}}", $contactName, $html);
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: ' . $from . "\r\n" .
	    'Reply-To: ' . $from . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	// Send the message
	if (mail($to, $subject, $html, $headers)) {
		$feedback .= "Your message has been sent to ".$to.".\r\n We'll be in touch shortly.";
	} else {
		$feedback .= "Your message failed to send.\r\n Please send an email to ".$to." from your email client.\r\n";
	}
}
// Provide feedback
$return = array('status'=>$snd,'message'=>nl2br($feedback),'fields'=>$fields);
echo json_encode($return);
?>
