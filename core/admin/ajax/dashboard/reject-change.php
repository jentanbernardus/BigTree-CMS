<?
	header("Content-type: text/javascript");
	
	$change = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".$_GET["change"]."'"));
	
	if ($change["table"] == "bigtree_pages")
		$r = $admin->getPageAccessLevelByUserId($page,$admin->ID);
	else {
		$user = $admin->getUser($admin->ID);
		$r = $user["permissions"][$change["module"]];
	}
	
	if ($r == "p") {
		sqlquery("DELETE FROM bigtree_pending_changes WHERE id = '".$_GET["change"]."'");
?>
$(".change_<?=$_GET["change"]?>").each(function() { $(this).remove(); });
BigTree.growl("Dashboard","Deleted Change");
<?
	} else {
?>
BigTree.growl("Denied","You don't have access to remove this change.");
<?
	}
?>