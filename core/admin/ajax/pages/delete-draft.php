<?
	// Get the version, check if the user has access to the page the version refers to.
	$access = $admin->getPageAccessLevelByUser($_GET["id"],$admin->ID);
	if ($access != "p") {
		$admin->stop("You must be a publisher to manage revisions.");
	}
	
	// Delete draft copy
	sqlquery("DELETE FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND `item_id` = '".mysql_real_escape_string($_GET["id"])."'");
?>