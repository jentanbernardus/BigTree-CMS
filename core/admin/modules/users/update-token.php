<?
	$admin->requireLevel(1);
	
	BigTree::globalizePOSTVars(array("mysql_real_escape_string"));
	
	$id = mysql_real_escape_string(end($path));
	
	sqlquery("UPDATE bigtree_api_tokens SET user = '$user', readonly = '$readonly' WHERE id = '$id'");
	
	$admin->growl("Users","Updated API Token");
	
	header("Location: ".$admin_root."users/view-tokens/");
	die();
?>