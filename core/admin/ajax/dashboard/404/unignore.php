<?
	header("Content-type: text/javascript");
	$admin->requireLevel(1);
	sqlquery("UPDATE bigtree_404s SET ignored = '' WHERE id = '".$_POST["id"]."'");
?>
BigTree.growl("Pages","Unignored 404 URL");