<?
	$admin->requireLevel(1);
	sqlquery("UPDATE bigtree_404s SET redirect_url = '".$_POST["value"]."' WHERE id = '".$_POST["id"]."'");
?>