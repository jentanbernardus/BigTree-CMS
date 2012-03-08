<h1><span class="dashboard"></span>Overview</h1>
<?
	$breadcrumb[] = array("title" => "Overview", "link" => "#");
	
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
			if ($f["read_by"] && strpos("|".$admin->ID."|",$f["read_by"]) !== false) {
				$read[] = $f;
			} else {
				$unread[] = $f;
			}
		}
	}
	
	// Get pending changes.
	$changes = $admin->getPendingChanges();
	
	// Get Google Analytics Traffic
	$ga_cache = $cms->getSetting("bigtree-internal-google-analytics-cache");
	// Only show this thing if they have Google Analytics setup already
	if ($ga_cache && count($ga_cache["two_week"])) {
		$visits = $ga_cache["two_week"];
		$min = min((is_array($visits)) ? $visits : array($visits));
		$max = max((is_array($visits)) ? $visits : array($visits)) - $min;
		$bar_height = 70;
?>
<div class="table">
	<summary>
		<h2 class="full">
			<span class="world"></span>
			Recent Traffic <small>Visits In The Past Two Weeks</small>
			<a href="<?=$aroot?>dashboard/analytics/" class="more">View Analytics</a>
		</h2>
	</summary>
	<section>
		<?
			if($visits) {
		?>
		<div class="graph">
			<?
				$x = 0;
			    foreach ($visits as $date => $count) {
			    	$height = round($bar_height * ($count - $min) / $max) + 12;
			    	$x++;
			?>
			<section class="bar<? if ($x == 14) { ?> last<? } elseif ($x == 1) { ?> first<? } ?>" style="height: <?=$height?>px; margin-top: <?=(82-$height)?>px;">
			    <?=$count?>
			</section>
			<?
				}
			   	
			   	$x = 0;
			   	foreach ($visits as $date => $count) {
			   		$x++;
			?>
			<section class="date<? if ($x == 14) { ?> last<? } elseif ($x == 1) { ?> first<? } ?>"><?=date("n/j/y",strtotime($date))?></section>
			<?
				}
			?>
		</div>
		<?
			} else {
		?>
		<p>No recent traffic</p>
		<?
			}
		?>
	</section>
</div>
<?
	}
?>

<div class="table">
	<summary>
		<h2 class="full">
			<span class="pending"></span>
			Pending Changes <small>Recent Changes Awaiting Approval</small>
			<a href="<?=$aroot?>dashboard/pending-changes/" class="more">View All Pending Changes</a>
		</h2>
	</summary>
	<header>
		<span class="changes_name">Change</span>
		<span class="changes_author">Author</span>
		<span class="changes_date">Date</span>
		<span class="changes_action">Preview</a></span>
		<span class="changes_action">Edit</a></span>
		<span class="changes_action">Approve</span>
		<span class="changes_action">Deny</span>
	</header>
	<ul>
		<?
			if (count($changes) == 0) {
		?>
		<li><section class="no_content"><p>No changes awaiting approval</p></section></li>
		<?	
			} else {
				$changes = array_slice($changes,0,10);
				foreach ($changes as $item) {
					if (!$item["title"]) {
						$item["title"] = $item["mod"]["name"];
					}
		?>
		<li>
			<section class="changes_name"><?=$item["title"]?></section>
			<section class="changes_author"><?=$item["user"]["name"]?></section>
			<section class="changes_date"><?=date("n/j/y",strtotime($item["date"]))?></section>
			<section class="changes_action"><a href="#" class="icon_preview"></a></section>
			<section class="changes_action"><a href="<?=$admin->getChangeEditLink($item)?>" class="icon_edit"></a></section>
			<section class="changes_action"><a href="#" class="icon_approve icon_approve_on"></a></section>
			<section class="changes_action"><a href="#" class="icon_deny"></a></section>
		</li>
		<?		
				}
			}
		?>
	</ul>
</div>

<div class="table">
	<summary>
		<h2 class="full">
			<span class="unread"></span>
			Unread Messages
			<a href="<?=$aroot?>dashboard/messages/" class="more">View All Messages</a>
		</h2>
	</summary>
	<header>
		<span class="messages_from_to">From</span>
		<span class="messages_subject">Subject</span>
		<span class="messages_date_time">Date</a></span>
		<span class="messages_date_time">Time</a></span>
		<span class="messages_view">View</span>
	</header>
	<ul>
		<?
			if (count($unread) == 0) {
		?>
		<li><section class="no_content"><p>No unread messages</p></section></li>
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