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
	
	$unread_pages = ceil(count($unread) / 5);
	$read_pages = ceil(count($read) / 5);
	$sent_pages = ceil(count($sent) / 5);
?>

<div class="table">
	<summary>
		<h2><span class="unread"></span>Unread Messages</h2>
		<ul id="unread_paging" class="view_paging"></ul>
	</summary>
	<header>
		<span class="messages_from_to">From</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</span>
		<span class="messages_date_time">Time</span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<?
			if (count($unread) == 0) {
		?>
		<li><section class="no_content">You have no unread messages.</section></li>
		<?	
			} else {
				foreach ($unread as $item) {
		?>
		<li>
			<section class="messages_from_to"><?=$item["sender_name"]?></section>
			<section class="messages_subject"><?=$item["subject"]?></section>
			<section class="messages_date_time"><?=date("n/j/y",strtotime($item["date"]))?></section>
			<section class="messages_date_time"><?=date("g:ia",strtotime($item["date"]))?></section>
			<section class="messages_view"><a href="view/<?=$item["id"]?>/" class="icon_message"></a></section>
		</li>
		<?
				}
			}
		?>
	</ul>
</div>

<div class="table">
	<summary>
		<h2><span class="read"></span>Read Messages</h2>
		<ul id="read_paging" class="view_paging"></ul>
	</summary>
	<header>
		<span class="messages_from_to">From</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</span>
		<span class="messages_date_time">Time</span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<?
			if (count($unread) == 0) {
		?>
		<li><section class="no_content">You have no read messages.</section></li>
		<?	
			} else {
				foreach ($read as $item) {
		?>
		<li>
			<section class="messages_from_to"><?=$item["sender_name"]?></section>
			<section class="messages_subject"><?=$item["subject"]?></section>
			<section class="messages_date_time"><?=date("n/j/y",strtotime($item["date"]))?></section>
			<section class="messages_date_time"><?=date("g:ia",strtotime($item["date"]))?></section>
			<section class="messages_view"><a href="view/<?=$item["id"]?>/" class="icon_message"></a></section>
		</li>
		<?
				}
			}
		?>
	</ul>
</div>

<div class="table">
	<summary>
		<h2><span class="sent"></span>Sent Messages</h2>
		<ul id="sent_paging" class="view_paging"></ul>
	</summary>
	<header>
		<span class="messages_from_to">To</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</span>
		<span class="messages_date_time">Time</span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<?
			if (count($sent) == 0) {
		?>
		<li><section class="no_content">You have no sent messages.</section></li>
		<?	
			} else {
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
		<?
				}
			}
		?>
	</ul>
</div>
<script type="text/javascript">
	BigTree.SetPageCount("#unread_paging",<?=$unread_pages?>,0);
	BigTree.SetPageCount("#read_paging",<?=$read_pages?>,0);
	BigTree.SetPageCount("#sent_paging",<?=$sent_pages?>,0);
	
	$(".view_paging a").click(function() {
	
		return false;
	});
</script>