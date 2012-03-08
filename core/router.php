<?
	// Handle Javascript Minifying and Caching
	if ($path[0] == "js") {
		clearstatcache();
		// Get the latest mod time on any included js files.
		$mtime = 0;
		$js_file = str_replace(".js","",$path[1]);
		$cfile = $server_root."cache/".$js_file.".js";
		$last_modified = file_exists($cfile) ? filemtime($cfile) : 0;
		if (is_array($config["js"][$js_file])) {
			foreach ($config["js"][$js_file] as $script) {
				$m = file_exists($site_root."js/$script") ? filemtime($site_root."js/$script") : 0;
				if ($m > $mtime) {
					$mtime = $m;
				}
			}
		}
		// If we have a newer Javascript file to include or we haven't cached yet, do it now.
		if (!file_exists($cfile) || $mtime > $last_modified) {
			$data = "";
			if (is_array($config["js"][$js_file])) {
				foreach ($config["js"][$js_file] as $script) {
					$data .= file_get_contents($site_root."js/$script")."\n";
				}
			}
			// Replace www_root/ and Minify
			$data = str_replace(array('$www_root',"www_root/"),$www_root,$data);
			if (is_array($_GET)) {
				foreach ($_GET as $key => $val) {
					if ($key != "bigtree_htaccess_url") {
						$data = str_replace('$'.$key,$val,$data);
					}
				}
			}
			if (is_array($config["js"]["vars"])) {
				foreach ($config["js"]["vars"] as $key => $val) {
					$data = str_replace('$'.$key,$val,$data);
				}
			}
			$data = JSMin::minify($data);
			file_put_contents($cfile,$data);
			header("Content-type: text/javascript");
			die($data);
		} else {
			$headers = apache_request_headers();
			if (!$headers) {
				header("Content-type: text/javascript");
				die(file_get_contents($cfile));
			}
			if ($headers["If-Modified-Since"] && strtotime($headers["If-Modified-Since"]) == $last_modified) {
				header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 304);
				die();
			} else {
				header("Content-type: text/javascript");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 200);
				die(file_get_contents($cfile));
			}
		}
	}
	
	// Handle CSS Shortcuts and Minifying
	if ($path[0] == "css") {
		clearstatcache();
		// Get the latest mod time on any included css files.
		$mtime = 0;
		$css_file = str_replace(".css","",$path[1]);
		$cfile = $server_root."cache/".$css_file.".css";
		$last_modified = file_exists($cfile) ? filemtime($cfile) : 0;
		if (is_array($config["css"][$css_file])) {
			foreach ($config["css"][$css_file] as $style) {
				$m = (file_exists($site_root."css/$style")) ? filemtime($site_root."css/$style") : 0;
				if ($m > $mtime) {
					$mtime = $m;
				}
			}
		}
		// If we have a newer CSS file to include or we haven't cached yet, do it now.
		if (!file_exists($cfile) || $mtime > $last_modified) {
			$data = "";
			if (is_array($config["css"][$css_file])) {
				foreach ($config["css"][$css_file] as $style) {
					$data .= file_get_contents($site_root."css/$style")."\n";
				}
			}
			if (is_array($config["css"]["vars"])) {
				foreach ($config["css"]["vars"] as $key => $val) {
					$data = str_replace('$'.$key,$val,$data);
				}
			}
			// Replace CSS3 easymode and Minify
			$data = bigtree_parse_css3($data);
			
			require_once($server_root."core/inc/utils/less-compiler.inc.php");
			$less = new lessc();
			$data = $less->parse($data);
			
			file_put_contents($cfile,$data);
			header("Content-type: text/css");
			die($data);
		} else {
			$headers = apache_request_headers();
			if (!$headers) {
				header("Content-type: text/css");
				die(file_get_contents($cfile));
			}
			if ($headers["If-Modified-Since"] && strtotime($headers["If-Modified-Since"]) == $last_modified) {
				header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 304);
				die();
			} else {
				header("Content-type: text/css");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified).' GMT', true, 200);
				die(file_get_contents($cfile));
			}
		}
	}
	
	// Start output buffering and sessions
	ob_start();
	session_start();
	
	// Handle AJAX calls.
	if ($path[0] == "ajax") {
		$x = 1;
		$ajpath = "";
		while ($x < count($path) - 1) {
			$ajpath .= $path[$x]."/";
			$x++;
		}
		if (file_exists("../templates/ajax/".$ajpath.$path[$x].".php")) {
			include "../templates/ajax/".$ajpath.$path[$x].".php";
		} else {
			$inc = "../templates/ajax/".$path[1]."/";
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
			if (file_exists($inc)) {
				include $inc;
			} else {
				include str_replace("/default.php",".php",$inc);
			}
		}
		die();
	}
	
	// Handle API calls.
	if ($path[0] == "api") {
		if (!count($_POST) && count($_GET)) {
			$_POST = $_GET;
		}
		include bigtree_path("inc/bigtree/admin.php");
		include bigtree_path("inc/bigtree/auto-modules.php");
		$admin = new BigTreeAdmin;
		$autoModule = new BigTreeAutoModule;
		$api_type = $path[1];
		$x = 2;
		$apipath = "";
		while ($x < count($path) - 1) {
			$apipath .= $path[$x]."/";
			$x++;
		}
		if ($apipath.$path[$x] != "users/authenticate") {
			if (isset($_POST["bigtreeapi"]["token"])) {
				if (!$admin->validateToken($_POST["bigtreeapi"]["token"])) {
					echo bigtree_api_encode(array("success" => false,"error" => "Invalid token. Please login."));
					die();
				}			
			} else {
				if (!$admin->validateToken($_POST["token"])) {
					echo bigtree_api_encode(array("success" => false,"error" => "Invalid token. Please login."));
					die();
				}
			}
		}
		include bigtree_path("api/".$apipath.$path[$x].".php");
		die();	
	}

	// Tell the browser we're serving HTML
	header("Content-type: text/html");

	// See if we're previewing changes.
	$preview = false;
	if ($path[0] == "_preview" && $_SESSION["bigtree"]["id"]) {
		$npath = array();
		foreach ($path as $item) {
			if ($item != "_preview") {
				$npath[] = $item;
			}
		}
		$path = $npath;
		$preview = true;
		$config["cache"] = false;
	}
	
	if ($path[0] == "_preview-pending" && $_SESSION["bigtree"]["id"]) {
		$preview = true;
		$commands = array();
		$navid = $path[1];
	}
	
	// So we don't lose this.
	define("BIGTREE_PREVIEWING",$preview);
	
	// Sitemap setup
	$sitemap = false;
	$sitemap_xml = false;
	if ($path[0] == "sitemap") {
		$sitemap = true;
	}
	if ($path[0] == "sitemap.xml") {
		$sitemap_xml = true;
	}
	if ($path[0] == "feeds") {
		$route = $path[1];
		$feed = $cms->getFeedByRoute($route);
		if ($feed) {
			header("Content-type: text/xml");
			echo '<?xml version="1.0"?>';
			include bigtree_path("feeds/".$feed["type"].".php");
			die();
		}
	}
	
	if (!$navid) {
		list($navid,$commands) = $cms->getNavId($path);
	}
	
	// Pre-init a bunch of vars to keep away notices.
	$module_title = "";
	$css = array();
	$js = array();
	$layout = "default";
	if ($navid) {
		// If we're previewing, get pending data as well.
		if ($preview) {
			$page = $cms->getPendingPage($navid);
		} else {
			$page = $cms->getPage($navid);
		}
			
		$resources = $page["resources"];
		$callouts = $page["callouts"];

		// Quick access to resources
		if (is_array($resources)) {
			foreach ($resources as $key => $val) {
				if (substr($key,0,1) != "_") {
					$$key = &$resources[$key];
				}
			}
		}
				
		// Redirect lower if the template is !
		if ($page["template"] == "!") {
			$nav = $cms->getNavByParent($page["id"],1);
			$first = $nav[0];
			header("Location: ".$cms->getLink($first["id"]));
			die();
		}
		
		// If the template is a module, do its routing for it, otherwise just include the template.
		if (substr($page["template"],0,7) == "module-") {
			// We need to figure out how far down the directory structure to route the,.	
			$inc = "../templates/modules/".substr($page["template"],7)."/";
			$inc_dir = $inc;
			$module_commands = array();
			$ended = false;
			foreach ($commands as $command) {
				if (!$ended && is_dir($inc.$command)) {
					$inc = $inc.$command."/";
				} elseif (!$ended && file_exists($inc.$command.".php")) {
					$inc_dir = $inc;
					$inc = $inc.$command.".php";
					$ended = true;
				} elseif (!$ended) {
					$ended = true;
					$module_commands[] = $command;
					$inc_dir = $inc;
					$inc = $inc."default.php";
				} else {
					$module_commands[] = $command;
				}
			}
			if (!$ended) {
				$inc_dir = $inc;
				$inc = $inc."default.php";
			}
			
			$commands = $module_commands;
			
			// Include the module's header
			if (file_exists("../templates/modules/".substr($page["template"],7)."/_header.php")) {
				include_once "../templates/modules/".substr($page["template"],7)."/_header.php";
			}
			
			// Include the sub-module's header if it exists.
			if (file_exists($inc_dir."_header.php")) {
				include_once $inc_dir."_header.php";
			}
			
			include $inc;

			// Include the sub-module's footer if it exists.
			if (file_exists($inc_dir."_footer.php")) {
				include_once $inc_dir."_footer.php";
			}
			
			// Include the module's footer
			if (file_exists("../templates/modules/".substr($page["template"],7)."/_footer.php")) {
				include_once "../templates/modules/".substr($page["template"],7)."/_footer.php";
			}

		} elseif ($page["template"]) {
			include "../templates/pages/".$page["template"].".php";
		} else {
			header("Location: ".$page["external"]);
		}
	} elseif (!$_GET["bigtree_htaccess_url"] || empty($path[0])) {
		include "../templates/pages/_home.php";
	} elseif ($sitemap) {
		include "../templates/pages/_sitemap.php";
	} elseif ($sitemap_xml) {
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		$q = sqlquery("SELECT template,external,path FROM bigtree_pages WHERE archived = '' AND (publish_at >= NOW() OR publish_at IS NULL)");
		while ($f = sqlfetch($q)) {
			if ($f["template"] || strpos($f["external"],$GLOBALS["domain"])) {	
				if (!$f["template"]) {
					if (substr($f["external"],0,6) == "ipl://") {
						$link = $this->getInternalPageLink($f["external"]);
					} else {
						$link = str_replace("{wwwroot}",$GLOBALS["www_root"],$f["external"]);
					}
				} else {
					$link = $GLOBALS["www_root"].$f["path"]."/";
				}
				
				echo "<url><loc>".$link."</loc></url>";
			}
		}
		echo '</urlset>';
		die();
	} else {
		// Let's check if it's in the old routing table.
		$found = false;
		$x = count($path);
		while ($x) {
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_route_history WHERE old_route = '".implode("/",array_slice($path,0,$x))."'"));
			if ($f) {
				$old = $f["old_route"];
				$new = $f["new_route"];
				$found = true;
				break;
			}
			$x--;
		}
		// If it's in the old routing table, send them to the new page.
		if ($found) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$www_root.str_replace($old,$new,$_GET["bigtree_htaccess_url"]));
			die();
		} else {
			header("HTTP/1.0 404 Not Found");
			$f = sqlfetch(sqlquery("SELECT * FROM bigtree_404s WHERE broken_url = '".mysql_real_escape_string(rtrim($_GET["bigtree_htaccess_url"],"/"))."'"));
			if ($f["redirect_url"]) {
				if ($f["redirect_url"] == "/") {
					$f["redirect_url"] = "";
				}
				
				if (substr($f["redirect_url"],0,7) == "http://" || substr($f["redirect_url"],0,8) == "https://") {
					$redirect = $f["redirect_url"];
				} else {
					$redirect = $www_root.str_replace($www_root,"",$f["redirect_url"]);
				}
				
				sqlquery("UPDATE bigtree_404s SET requests = (requests + 1) WHERE = '".$f["id"]."'");
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: $redirect");
				die();
			} else {
				$referer = $_SERVER["HTTP_REFERER"];
				$requester = $_SERVER["REMOTE_ADDR"];

				if ($f) {
					sqlquery("UPDATE bigtree_404s SET requests = (requests + 1) WHERE id = '".$f["id"]."'");
				} else {
					sqlquery("INSERT INTO bigtree_404s (`broken_url`,`requests`) VALUES ('".mysql_real_escape_string(rtrim($_GET["bigtree_htaccess_url"],"/"))."','1')");
				}
				include "../templates/pages/_404.php";
			}
			
			$nocache = true;
		}
	}
	
	$content = ob_get_clean();
	
	// Load the content again into the layout.
	ob_start();
	include "../templates/layouts/$layout.php";
	$content = ob_get_clean();
	
	// Allow for special output filter functions.
	$filter = false;
	if ($config["output_filter"]) {
		$filter = $config["output_filter"];
	}
	
	ob_start($filter);
	
	// If we're in HTTPS, make sure all Javascript, images, and CSS are pulling from HTTPS
	if ($cms->Secure) {
		$content = str_replace(array('src="http://','link href="http://'),array('src="https://','link href="https://'),$content);
	}
	
	
	// Load the BigTree toolbar if you're logged in to the admin.
	if ($page["id"] && !$cms->Secure && isset($_COOKIE["bigtree"]["email"]) && !$_SESSION["bigtree"]["id"]) {
		include bigtree_path("inc/bigtree/admin.php");

		if (BIGTREE_CUSTOM_ADMIN_CLASS) {
			eval('$admin = new '.BIGTREE_CUSTOM_ADMIN_CLASS.';');
		} else {
			$admin = new BigTreeAdmin;
		}
	}
	
	if ($page["id"] && $_SESSION["bigtree"]["id"] && !$cms->Secure) {
		$show_bar_default = $_COOKIE["hide_bigtree_bar"] ? "false" : "true";
		$show_preview_bar = "false";
		$return_link = "";
		if ($_GET["bigtree_preview_bar"]) {
			$show_bar_default = "false";
			$show_preview_bar = "true";
			$return_link = $_SERVER["HTTP_REFERER"];
		}
				
		$content = str_replace('</body>','<script type="text/javascript">var bigtree_is_previewing = '.(BIGTREE_PREVIEWING ? "true" : "false").'; var bigtree_current_page_id = '.$page["id"].'; var bigtree_bar_show = '.$show_bar_default.'; var bigtree_user_name = "'.$_SESSION["bigtree"]["name"].'"; var bigtree_preview_bar_show = '.$show_preview_bar.'; var bigtree_return_link = "'.$return_link.'";</script><script type="text/javascript" src="'.$config["admin_root"].'js/bar.js"></script></body>',$content);
		$nocache = true;
	}
	
	echo $content;
	
	// Write to the cache
	if ($config["cache"] && !$nocache) {
		$cache = ob_get_flush();
		$curl = $_GET["bigtree_htaccess_url"];
		if (!$curl) {
			$curl = "home";
		}
		file_put_contents("../cache/".base64_encode($curl),$cache);
	}
?>