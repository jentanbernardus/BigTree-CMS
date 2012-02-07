<?
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_locks WHERE `table` = '".$form["table"]."' AND item_id = '".end($path)."'"));
	sqlquery("UPDATE bigtree_locks SET user = '".$_SESSION["bigtree"]["id"]."', last_accessed = NOW() WHERE id = '".$f["id"]."'");
	
	include bigtree_path("admin/auto-modules/forms/edit.php");
?>