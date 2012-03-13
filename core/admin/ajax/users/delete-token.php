<?
	header("Content-type: text/javascript");
	$admin->deleteAPIToken($_POST["id"]);
?>
BigTree.growl("Users","Deleted API Token");