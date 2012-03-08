<?
	$site = sqlfetch(sqlquery("SELECT nav_title FROM bigtree_pages WHERE id = '0'"));
	$layout = "login";
	
	// Check if we're forcing HTTPS
	if ($config["force_secure_login"] && $_SERVER["SERVER_PORT"] == 80) {
		header("Location: ".str_replace("http://","https://",$admin_root)."login/");
		die();
	}
	
	if (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE") !== false && !$_SESSION["ignore_browser_warning"]) {
		include bigtree_path("admin/pages/browser-warning.php");
		$admin->stop();
	}
?>