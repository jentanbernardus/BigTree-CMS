<?
	sqlquery("UPDATE bigtree_resources SET name = '".htmlspecialchars(mysql_real_escape_string($_POST["title"]))."' WHERE file = '".mysql_real_escape_string($_POST["file"])."'");
?>