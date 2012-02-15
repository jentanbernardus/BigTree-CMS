<?
	// BigTree Version
	$GLOBALS["bigtree"]["version"] = "4.0";		
	$GLOBALS["wiki"] = "http://wiki.bigtreecms.com/index.php/";
	
	// If they're requesting images, css, or js, just give it to them.
	if ($path["1"] == "images") {
		$x = 2;
		$ipath = "";
		while ($x < count($path) - 1) {
			$ipath .= $path[$x]."/";
			$x++;
		}
		
		$ifile = (file_exists("../custom/admin/images/".$ipath.$path[$x])) ? "../custom/admin/images/".$ipath.$path[$x] : "../core/admin/images/".$ipath.$path[$x];
		$headers = apache_request_headers();
		$last_modified = filemtime($ifile);
		if ($headers["If-Modified-Since"] && strtotime($headers["If-Modified-Since"]) == $last_modified) {
			header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 304);
			die();
		}
		$type = explode(".",$path[$x]);
		$type = strtolower($type[count($type)-1]);
		if ($type == "gif") {
			header("Content-type: image/gif");
		} elseif ($type == "jpg") {
			header("Content-type: image/jpeg");
		} elseif ($type == "png") {
			header("Content-type: image/png");
		}
		
		echo file_get_contents($ifile);
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 200);
		die();
	}
	
	if ($path[1] == "css") {
		if (file_exists("../custom/inc/utils/bigtree.inc.php")) {
			include "../custom/inc/utils/bigtree.inc.php";		
		} else {
			include "../core/inc/utils/bigtree.inc.php";
		}
		$x = 2;
		$ipath = "";
		while ($x < count($path) - 1) {
			$ipath .= $path[$x]."/";
			$x++;
		}
		
		$ifile = (file_exists("../custom/admin/css/".$ipath.$path[$x])) ? "../custom/admin/css/".$ipath.$path[$x] : "../core/admin/css/".$ipath.$path[$x];
		$headers = apache_request_headers();
		$last_modified = filemtime($ifile);
		if ($headers["If-Modified-Since"] && strtotime($headers["If-Modified-Since"]) == $last_modified) {
			header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 304);
			die();
		}
		header("Content-type: text/css");		
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 200);
		echo bigtree_parse_css3(file_get_contents($ifile));
		die();
	}
	
	if ($path[1] == "js") {
		include "../templates/config.php";
		
		$pms = ini_get('post_max_size');
		$mul = substr($pms,-1);
		$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
		$max_file_size = $mul * (int)$pms;
		
		$x = 2;
		$ipath = "";
		while ($x < count($path) - 1) {
			$ipath .= $path[$x]."/";
			$x++;
		}
		
		$ifile = (file_exists("../custom/admin/js/".$ipath.$path[$x])) ? "../custom/admin/js/".$ipath.$path[$x] : "../core/admin/js/".$ipath.$path[$x];
		
		if (substr($ifile,-4,4) == ".php") {
			include $ifile;
			die();
		}
		
		$headers = apache_request_headers();
		$last_modified = filemtime($ifile);
		if ($headers["If-Modified-Since"] && strtotime($headers["If-Modified-Since"]) == $last_modified) {
			header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 304);
			die();
		}
		if (substr($path[$x],-3,3) == "css") {
			header("Content-type: text/css");
		} elseif (substr($path[$x],-3,3) == "htm" || substr($path[$x],-4,4) == "html") {
			header("Content-type: text/html");
		} else {
			header("Content-type: text/javascript");
		}
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 200);
		
		echo str_replace(array("{max_file_size}","www_root/"),array($max_file_size,$config["www_root"]),file_get_contents($ifile));
		die();
	}
	
	// Otherwise start the admin routing
	
	if (file_exists("../custom/bootstrap.php")) {
		include "../custom/bootstrap.php";
	} else {
		include "../core/bootstrap.php";
	}
	ob_start();
	session_start();
	include bigtree_path("inc/bigtree/admin.php");
	include bigtree_path("inc/bigtree/auto-modules.php");
	$aroot = $GLOBALS["www_root"]."admin/";
	
	if (BIGTREE_CUSTOM_ADMIN_CLASS) {
		eval('$admin = new '.BIGTREE_CUSTOM_ADMIN_CLASS.';');
	} else {
		$admin = new BigTreeAdmin;
	}
		
	if (!isset($path[1])) {
		$path[1] = "";
	}
	
	$css = array();
	$js = array();
	$layout = "default";
	if (!$admin->ID) {
		if ($path[1] == "ajax") {
			if ($path[2] == "change-password") {
				include bigtree_path("admin/ajax/change-password.php");
			} else {
				include bigtree_path("admin/ajax/login.php");
			}
			die();
		} else {
			if ($path[1] == "change-password") {
				include bigtree_path("admin/pages/change-password.php");
			} else {
				include bigtree_path("admin/pages/login.php");
			}
		}
	} else {
		// We're logged in, let's go somewhere.
		if (!$path[1]) {
			header("Location: ".$www_root."admin/dashboard/");
			die();
		// We're hitting an ajax page.
		} elseif ($path[1] == "ajax") {
			$x = 2;
			$ajpath = "";
			while ($x < count($path) - 1) {
				$ajpath .= $path[$x]."/";
				$x++;
			}
			
			// Permissions!
			$module = $admin->getModuleByRoute($path[2]);
			if ($module && !$admin->checkAccess($module["id"])) {
				include bigtree_path("admin/ajax/login.php");
				die();
			}

			$autoModule = new BigTreeAutoModule;
			
			$path[$x] = str_replace(".php","",$path[$x]);

			include bigtree_path("admin/ajax/".$ajpath.$path[$x].".php");
			die();
		// We've actually chosen a section now.
		} else {
			$ispage = false;
			$inc = false;
			// Check if it's a module or a normal page.
			if (is_dir("../custom/admin/modules/".$path[1])) {
				if (!isset($path[2])) {
					$inc = "../custom/admin/modules/".$path[1]."/default.php";
				} else {
					$inc = "../custom/admin/modules/".$path[1]."/";
					$inc_dir = $inc;
					$x = 1;
					$y = 1;
					while ($x < count($path)) {
						if (is_dir($inc.$path[$x])) {
							$inc .= $path[$x]."/";
							$inc_dir .= $path[$x]."/";
							$y++;
						} elseif (file_exists($inc.$path[$x].".php")) {
							$inc .= $path[$x].".php";
							$y++;
						}
						$x++;
					}
					if (substr($inc,-4,4) != ".php") {
						if (file_exists($inc.end($path).".php")) {
							$inc .= end($path).".php";
						} else {
							$inc .= "default.php";
						}
					}
					$commands = array_slice($path,$y+1);
				}
			}
			if (($inc && !file_exists($inc)) || (!$inc && is_dir("../core/admin/modules/".$path[1]))) {
				if (!isset($path[2])) {
					$inc = "../core/admin/modules/".$path[1]."/default.php";
				} else {
					$inc = "../core/admin/modules/".$path[1]."/";
					$inc_dir = $inc;
					$x = 1;
					$y = 1;
					while ($x < count($path)) {
						if (is_dir($inc.$path[$x])) {
							$inc .= $path[$x]."/";
							$inc_dir .= $path[$x]."/";
							$y++;
						} elseif (file_exists($inc.$path[$x].".php")) {
							$inc .= $path[$x].".php";
							$y++;
						}
						$x++;
					}
					if (substr($inc,-4,4) != ".php") {
						if (file_exists($inc.end($path).".php")) {
							$inc .= end($path).".php";
						} else {
							$inc .= "default.php";
						}
					}
					$commands = array_slice($path,$y+1);
				}
			// It's a normal page.
			} elseif (!$inc) {
				if (file_exists("../custom/admin/pages/".$path[1].".php")) {
					$inc = "../custom/admin/pages/".$path[1].".php";
				} elseif (file_exists("../core/admin/pages/".$path[1].".php")) {
					$inc = "../core/admin/pages/".$path[1].".php";
				}
				$ispage = true;
			}
			
			// Permissions!
			if (!$ispage || !$inc) {
				$module = $admin->getModuleByRoute($path[1]);
				$module_title = $module["name"];
				if ($module && !$admin->checkAccess($module["id"])) {
					ob_clean();
					include bigtree_path("admin/pages/_denied.php");
					$content = ob_get_clean();
					include bigtree_path("admin/layouts/".$layout.".php");
					die();
				}
			}
			
			// Ok, if this inc is real, let's include it -- otherwise see if it's an auto-module action.
			if (isset($path[1])) {
				$module = $admin->getModuleByRoute($path[1]);
			}
			if (!isset($path[2])) {
				$path[2] = "";
			}
			$action = sqlfetch(sqlquery("SELECT * FROM bigtree_module_actions WHERE module = '".$module["id"]."' AND route = '".$path[2]."'"));
			
			$inc_dir = str_replace("../",$server_root,$inc_dir);
			
			if ($module && ($action["view"] || $action["form"])) {
				if ($action["form"]) {
					$edit_id = isset($path[3]) ? $path[3] : "";
					include bigtree_path("admin/auto-modules/form.php");
				} else {
					include bigtree_path("admin/auto-modules/view.php");
				}
			} elseif (file_exists($inc)) {
				if (!$ispage && file_exists(bigtree_path("admin/modules/".$path[1]."/_header.php"))) {
					include bigtree_path("admin/modules/".$path[1]."/_header.php");
				}
				if (!$ispage && file_exists($inc_dir."_header.php") && bigtree_path("admin/modules/".$path[1]."/_header.php") != ($inc_dir."_header.php")) {
					include $inc_dir."_header.php";
				}
				
				include $inc;
				
				if (!$ispage && file_exists(bigtree_path("admin/modules/".$path[1]."/_footer.php"))) {
					include bigtree_path("admin/modules/".$path[1]."/_footer.php");
				}
				if (!$ispage && file_exists($inc_dir."_footer.php")) {
					include $inc_dir."_footer.php";
				}
			} else {
				include bigtree_path("admin/pages/_404.php");
			}
		}
	}
	
	$content = ob_get_clean();
	
	include bigtree_path("admin/layouts/".$layout.".php");
?>