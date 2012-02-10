<h1><span class="messages"></span>Message Center</h1>
<?
	include "_nav.php";
	
	// Get all the messages we've sent or received.  We're going to paginate them in a hidden type fashion and just load them all at once.
	$sent = array();
	$read = array();
	$unread = array();
	$q = sqlquery("SELECT bigtree_messages.*, bigtree_users.name AS sender_name FROM bigtree_messages JOIN bigtree_users ON bigtree_messages.sender = bigtree_users.id WHERE sender = '".$admin->ID."' OR recipients LIKE '%|".$admin->ID."|%' ORDER BY date DESC");
	
	while ($f = sqlfetch($q)) {
		// If we're the sender put it in the sent array.
		if ($f["sender"] == $admin->ID) {
			$sent[] = $f;
		} else {
			// If we've been marked read, put it in the read array.
			if (strpos("|".$admin->ID."|",$f["read_by"]) !== false) {
				$read[] = $f;
			} else {
				$unread[] = $f;
			}
		}
	}
?>

<div class="table">
	<summary><h2><span class="unread"></span>Unread Messages</h2></summary>
	<header>
		<span class="messages_from_to">From</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</span>
		<span class="messages_date_time">Time</span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<? foreach ($unread as $item) { ?>
		<li>
			<section class="messages_from_to"><?=$item["sender_name"]?></section>
			<section class="messages_subject"><?=$item["subject"]?></section>
			<section class="messages_date_time"><?=date("n/j/y",strtotime($item["date"]))?></section>
			<section class="messages_date_time"><?=date("g:ia",strtotime($item["date"]))?></section>
			<section class="messages_view"><a href="view/<?=$item["id"]?>/" class="icon_message"></a></section>
		</li>
		<? } ?>
	</ul>
</div>

<div class="table">
	<summary><h2><span class="read"></span>Read Messages</h2></summary>
	<header>
		<span class="messages_from_to">From</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</span>
		<span class="messages_date_time">Time</span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<? foreach ($read as $item) { ?>
		<li>
			<section class="messages_from_to"><?=$item["sender_name"]?></section>
			<section class="messages_subject"><?=$item["subject"]?></section>
			<section class="messages_date_time"><?=date("n/j/y",strtotime($item["date"]))?></section>
			<section class="messages_date_time"><?=date("g:ia",strtotime($item["date"]))?></section>
			<section class="messages_view"><a href="view/<?=$item["id"]?>/" class="icon_message"></a></section>
		</li>
		<? } ?>
	</ul>
</div>

<div class="table">
	<summary><h2><span class="sent"></span>Sent Messages</h2></summary>
	<header>
		<span class="messages_from_to">To</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</span>
		<span class="messages_date_time">Time</span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<?
			foreach ($sent as $item) {
				// Get the recipient names
				$recipients = explode("|",trim($item["recipients"],"|"));
				$r_names = array();
				foreach ($recipients as $r) {
					$u = sqlfetch(sqlquery("SELECT name FROM bigtree_users WHERE id = '".mysql_real_escape_string($r)."'"));
					$r_names[] = $u["name"];
				}
		?>
		<li>
			<section class="messages_from_to"><?=implode(", ",$r_names)?></section>
			<section class="messages_subject"><?=$item["subject"]?></section>
			<section class="messages_date_time"><?=date("n/j/y",strtotime($item["date"]))?></section>
			<section class="messages_date_time"><?=date("g:ia",strtotime($item["date"]))?></section>
			<section class="messages_view"><a href="view/<?=$item["id"]?>/" class="icon_message"></a></section>
		</li>
		<? } ?>
	</ul>
</div>