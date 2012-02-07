<?
	sqlquery("UPDATE bigtree_locks SET last_accessed = NOW() WHERE id = '".$_POST["id"]."'");
?>