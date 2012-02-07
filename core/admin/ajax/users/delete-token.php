<?
	header("Content-type: text/javascript");
	$admin->requireLevel(1);
	
	$id = mysql_real_escape_string($_POST["id"]);
	sqlquery("DELETE FROM bigtree_api_tokens WHERE id = '$id'");
?>
BigTree.growl("Users","Deleted API Token");