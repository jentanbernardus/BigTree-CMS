<?
	if (!isset($_GET["bigtree_htaccess_url"])) {
		$_GET["bigtree_htaccess_url"] = "";
	}
	$path = explode("/",rtrim($_GET["bigtree_htaccess_url"],"/"));
	
	$debug = false;
	$config = array();
	include str_replace("site/index.php","templates/config.php",__FILE__);	
	
	// Let admin bootstrap itself.
	if ($path[0] == "admin") {
		include "../core/admin/router.php";
		die();
	}
	
	// See if this thing is cached
	if ($config["cache"] && $path[0] != "_preview" && $path[0] != "_preview-pending") {
		$curl = $_GET["bigtree_htaccess_url"];
		if (!$curl) {
			$curl = "home";
		}
		$file = "../cache/".base64_encode($curl);
		// If the file is at least 5 minutes fresh, serve it up.
		if (file_exists($file) && filemtime($file) > (time()-300)) {
			if ($config["xsendfile"]) {
				header("X-Sendfile: ".$server_root."cache/".base64_encode($curl));
				header("Content-Type: text/html");
				die();
			} else {
				die(file_get_contents("../cache/".base64_encode($curl)));
			}
		}
	}

	// Bootstrap BigTree 4.0
	include "../core/bootstrap.php";
	include "../core/router.php";
?>