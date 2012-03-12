<?
	$admin->requireLevel(1);
	
	BigTree::globalizePOSTVars(array("mysql_real_escape_string"));
	
	$token = BigTree::randomString(30);
	$r = sqlrows(sqlquery("SELECT * FROM bigtree_api_tokens WHERE token = '$token'"));
	while ($r) {
		$token = BigTree::randomString(30);
		$r = sqlrows(sqlquery("SELECT * FROM bigtree_api_tokens WHERE token = '$token'"));				
	}
	
	sqlquery("INSERT INTO bigtree_api_tokens (`token`,`user`,`readonly`) VALUES ('$token','$user','$readonly')");
	
	$admin->growl("Users","Added API Token");
	
	header("Location: ".$admin_root."users/view-tokens/");
	die();
?>