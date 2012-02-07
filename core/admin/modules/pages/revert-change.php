<?
	$page = end($path);
	$r = $admin->getPageAccessLevelByUserId($page,$admin->ID);
	if ($r) {
		sqlquery("DELETE FROM bigtree_pending_changes WHERE `table` = 'bigtree_pages' AND item_id = '$page'");
	}
	header("Location: ".$aroot."pages/edit/".$page."/");
?>