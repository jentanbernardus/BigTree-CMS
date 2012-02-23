<?
	header("Content-type: text/javascript");
	
	$id = mysql_real_escape_string($_POST["id"]);
	
	// This section needs to be revamped to check permissions for modules
	$item = sqlfetch(sqlquery("SELECT * FROM bigtree_resources WHERE id = '$id'"));
	$entry = $item["entry"];
	$r = $admin->getPageAccessLevelByUser($id,$admin->ID);
	if ($r == "p") {
		unlink($config["server_root"]."site/files/resources/".$item["file"]);
		sqlquery("DELETE FROM bigtree_resources WHERE id = '$id'");
		echo 'BigTree.growl("Resources","Deleted file.");';
	 } else {
		echo 'BigTree.growl("Resources","You don\'t have permission to delete this file.");';
	}
?>