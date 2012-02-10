<?
	// Make sure the user has the right to see this message
	$message = sqlfetch(sqlquery("SELECT * FROM bigtree_messages WHERE id = '".mysql_real_escape_string(end($path))."'"));
	if ($message["sender"] != $admin->ID && strpos("|".$admin->ID."|",$message["recipients"]) === false) {
		$admin->stop("This message was not sent by you, or to you.");
	}
	
	// Get the list of recipients to determine the names and also to tell _nav whether to show "Reply All"
	$recipients = explode("|",trim($message["recipients"],"|"));
	$recipient_names = array();
	foreach ($recipients as $r) {
		$u = sqlfetch(sqlquery("SELECT name FROM bigtree_users WHERE id = '".mysql_real_escape_string($r)."'"));
		$recipient_names[] = $u["name"];
	}
	
	// Get the sender's name
	$u = sqlfetch(sqlquery("SELECT name FROM bigtree_users WHERE id = '".mysql_real_escape_string($message["sender"])."'"));
	$sender_name = $u["name"];
?>
<h1><span class="messages"></span>Message Center</h1>
<? include "_nav.php" ?>
<div class="form_container">
	<summary>
		<h2><span class="unread"></span> <?=$message["subject"]?></h2>
	</summary>
	<section>
		<div class="alert">
			<article class="message_from">
				<label>From</label>
				<p><?=$sender_name?></p>
			</article>
			<article class="message_to">
				<label>To</label>
				<p><?=implode(", ",$recipient_names)?></p>
			</article>
		</div>
		<?=$message["message"]?>
	</section>
</div>