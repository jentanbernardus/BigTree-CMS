<?
	$id = mysql_real_escape_string(end($commands));
	sqlquery("DELETE FROM bigtree_feeds WHERE id = '$id'");
	$admin->growl("Developer","Deleted Feed");
	header("Location: ".$developer_root."feeds/view/");
	die();
?>