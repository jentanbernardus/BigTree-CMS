<?
	$admin->requireLevel(1);
	
	bigtree_process_post_vars(array("mysql_real_escape_string"));
	
	$token = str_rand(30);
	$r = sqlrows(sqlquery("SELECT * FROM bigtree_api_tokens WHERE token = '$token'"));
	while ($r) {
		$token = str_rand(30);
		$r = sqlrows(sqlquery("SELECT * FROM bigtree_api_tokens WHERE token = '$token'"));				
	}
	
	sqlquery("INSERT INTO bigtree_api_tokens (`token`,`user`,`readonly`) VALUES ('$token','$user','$readonly')");
	
	$admin->growl("Users","Added API Token");
	
	header("Location: ".$admin_root."users/view-tokens/");
	die();
?>