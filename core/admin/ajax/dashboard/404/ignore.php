<?
	header("Content-type: text/javascript");
	$admin->requireLevel(1);
	sqlquery("UPDATE bigtree_404s SET ignored = 'on' WHERE id = '".$_POST["id"]."'");
?>
BigTree.growl("Pages","Ignored 404 URL");