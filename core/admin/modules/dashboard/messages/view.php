<?
	// Make sure the user has the right to see this message
	$message = $admin->getMessage(end($bigtree["path"]));
	$admin->markMessageRead($message["id"]);
	
	// Get the list of recipients to determine the names and also to tell _nav whether to show "Reply All"
	$recipients = explode("|",trim($message["recipients"],"|"));
	$recipient_names = array();
	$recipient_gravatar = false;
	foreach ($recipients as $r) {
		$u = $admin->getUser($r);
		$recipient_names[] = $u["name"];
		if ($r == $admin->ID) {
			$recipient_gravatar = $u["email"];
		}
	}
	if (!$recipient_gravatar) {
		$u = $admin->getUser($recipients[0]);
		$recipient_gravatar = $u["email"];
	}
	
	// Get the sender's name
	$u = $admin->getUser($message["sender"]);
	$sender_name = $u["name"];
	$sender_gravatar = $u["email"];
?>
<h1>
	<span class="messages"></span>Message Center
	<? include BigTree::path("admin/modules/dashboard/_nav.php") ?>
</h1>
<? include "_nav.php" ?>
<div class="form_container">
	<summary>
		<h2><span class="unread"></span> <?=$message["subject"]?></h2>
	</summary>
	<section>
		<div class="alert">
			<article class="message_from">
				<span class="gravatar">
					<img src="<?=BigTree::gravatar($sender_gravatar)?>" alt="" />
				</span>
				<label>From</label>
				<p><?=$sender_name?></p>
			</article>
			<article class="message_to">
				<span class="gravatar">
					<img src="<?=BigTree::gravatar($recipient_gravatar)?>" alt="" />
				</span>
				<label>To</label>
				<p><?=implode(", ",$recipient_names)?></p>
			</article>
		</div>
		<?=$message["message"]?>
	</section>
</div>