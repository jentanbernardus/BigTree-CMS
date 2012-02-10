<?
	// Get all the messages we've received.
	$sent = array();
	$read = array();
	$unread = array();
	$q = sqlquery("SELECT bigtree_messages.*, bigtree_users.name AS sender_name FROM bigtree_messages JOIN bigtree_users ON bigtree_messages.sender = bigtree_users.id WHERE recipients LIKE '%|".$admin->ID."|%' ORDER BY date DESC");
	
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
<h1><span class="dashboard"></span>Dashboard</h1>
<div class="table">
	<summary><h2 class="full"><span class="world"></span>Recent Traffic<a href="analytics/" class="more">View Analytics</a></h2></summary>
</div>

<div class="table">
	<summary><h2 class="full"><span class="pending"></span>Pending Changes<a href="pending/" class="more">View All Pending Changes</a></h2></summary>
</div>

<div class="table">
	<summary><h2 class="full"><span class="unread"></span>Unread Messages<a href="messages/" class="more">View All Messages</a></h2></summary>
</div>