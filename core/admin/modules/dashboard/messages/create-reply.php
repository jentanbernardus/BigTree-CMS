<?
	if (!count($_POST["send_to"]) || !$_POST["subject"] || !$_POST["message"]) {
		$_SESSION["saved_message"] = $_POST;
		if (strpos("reply-all",$_SERVER["HTTP_REFERER"])) {
			header("Location: ../reply-all/".$_POST["response_to"]."/");
		} else {
			header("Location: ../reply/".$_POST["response_to"]."/");
		}
		die();
	}
	
	// Make sure the user has the right to see this message
	$parent = sqlfetch(sqlquery("SELECT * FROM bigtree_messages WHERE id = '".mysql_real_escape_string($_POST["response_to"])."'"));
	if ($parent["sender"] != $admin->ID && strpos("|".$admin->ID."|",$parent["recipients"]) === false) {
		$admin->stop("This message was not sent by you, or to you.");
	}
	
	// Clear tags out of the subject, sanitize the message body of XSS attacks.
	$subject = mysql_real_escape_string(htmlspecialchars(strip_tags($_POST["subject"])));
	$message = mysql_real_escape_string(htmlclean($_POST["message"]));
	$response_to = mysql_real_escape_string($_POST["response_to"]);
	
	// We build the send_to field this way so that we don't have to create a second table of recipients.  Is it faster database wise using a LIKE over a JOIN? I don't know, but it makes for one less table.
	$send_to = "|";
	foreach ($_POST["send_to"] as $r) {
		// Make sure they actually put in a number and didn't try to screw with the $_POST
		$send_to .= intval($r)."|";
	}
	
	sqlquery("INSERT INTO bigtree_messages (`sender`,`recipients`,`subject`,`message`,`response_to`,`date`) VALUES ('".$admin->ID."','$send_to','$subject','$message','$response_to',NOW())");
	
	$admin->growl("Message Center","Replied To Message");
	header("Location: ../");
	die();
?>