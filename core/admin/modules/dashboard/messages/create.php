<?
	if (!count($_POST["send_to"]) || !$_POST["subject"] || !$_POST["message"]) {
		$_SESSION["saved_message"] = $_POST;
		header("Location: ../new/");
		die();
	}
	
	// Clear tags out of the subject, sanitize the message body of XSS attacks.
	$subject = mysql_real_escape_string(htmlspecialchars(strip_tags($_POST["subject"])));
	$message = mysql_real_escape_string(htmlclean($_POST["message"]));
	// We build the send_to field this way so that we don't have to create a second table of recipients.  Is it faster database wise using a LIKE over a JOIN? I don't know, but it makes for one less table.
	$send_to = "|";
	foreach ($_POST["send_to"] as $r) {
		// Make sure they actually put in a number and didn't try to screw with the $_POST
		$send_to .= intval($r)."|";
	}
	
	sqlquery("INSERT INTO bigtree_messages (`sender`,`recipients`,`subject`,`message`,`date`) VALUES ('".$admin->ID."','$send_to','$subject','$message',NOW())");
	
	$admin->growl("Message Center","Sent Message");
	header("Location: ../");
	die();
?>