<?
	$breadcrumb[] = array("title" => "Pending Changes", "link" => "#");

	// Get pending changes.
	$changes = $admin->getPendingChanges();
?>
<div class="table">
	<summary>
		<h2 class="full">
			<span class="pending"></span>
			Pending Changes <small>All Changes Awaiting Your Approval</small>
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
		<li><section class="no_content"><p>You have no changes awaiting your approval.</p></section></li>
		<?	
			} else {
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
			<section class="changes_action"><a href="#" class="icon_edit"></a></section>
			<section class="changes_action"><a href="#" class="icon_approve icon_approve_on"></a></section>
			<section class="changes_action"><a href="#" class="icon_deny"></a></section>
		</li>
		<?		
				}
			}
		?>
	</ul>
</div>