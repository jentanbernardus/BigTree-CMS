<?
	$admin->requireLevel(1);
	
	bigtree_process_post_vars(array("mysql_real_escape_string"));
	
	$id = mysql_real_escape_string(end($path));
	
	sqlquery("UPDATE bigtree_api_tokens SET user = '$user', readonly = '$readonly' WHERE id = '$id'");
	
	$admin->growl("Users","Updated API Token");
	
	header("Location: ".$aroot."users/view-tokens/");
	die();
?>