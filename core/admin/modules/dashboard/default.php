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
	
	// Get Google Analytics Traffic
	$ga_cache = $cms->getSetting("bigtree-internal-google-analytics-cache");
	// Only show this thing if they have Google Analytics setup already
	if (count($ga_cache["two_week"])) {
		$visits = $ga_cache["two_week"];
		$min = min($visits);
		$max = max($visits) - $min;
		$bar_height = 70;
?>
<div class="table">
	<summary><h2 class="full"><span class="world"></span>Recent Traffic <small>Visits</small><a href="analytics/" class="more">View Analytics</a></h2></summary>
	<section>
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
	</section>
</div>
<?
	}
?>

<div class="table">
	<summary><h2 class="full"><span class="pending"></span>Pending Changes <small>Awaiting Your Approval</small><a href="pending/" class="more">View All Pending Changes</a></h2></summary>
	<ul>
		<?
			if (count($changes) == 0) {
		?>
		<li><section class="no_content"><p>You have no changes awaiting your approval.</p></section></li>
		<?	
			} else {
				foreach ($changes as $item) {
				
				}
			}
		?>
	</ul>
</div>

<div class="table">
	<summary><h2 class="full"><span class="unread"></span>Unread Messages<a href="messages/" class="more">View All Messages</a></h2></summary>
	<ul>
		<?
			if (count($unread) == 0) {
		?>
		<li><section class="no_content"><p>You have no unread messages.</p></section></li>
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