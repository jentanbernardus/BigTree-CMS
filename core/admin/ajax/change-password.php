<?
	header("Content-type: text/javascript");
	
	$user = mysql_real_escape_string($_POST["user"]);
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_users WHERE email = '$user'"));
	if (!$f)
		die('BigTree.growl("Password Request","No user was found for the email address you provided.");');
	
	$hash = md5(microtime().$f["password"]);
	
	sqlquery("UPDATE bigtree_users SET change_password_hash = '$hash' WHERE id = '".$f["id"]."'");
	
	$change_link = $admin_root."change-password/$hash/";
	
	mail($f["email"],$site["title"]." Password Change Request","Hello ".$f["name"].",\n\nTo change your password for the ".$site["title"]." Admin, please click the link below.\n\n".$change_link."\n\n-- BigTree CMS --","From: no-reply@".str_replace(array("http://","www."),"",$config["domain"]));
?>
BigTree.growl("Password Change","Instructions have been emailed to you.");